<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Notifications\OrderPaidNotification;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class MidtransWebhookController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->midtrans->verifySignature($payload)) {
            Log::warning('Midtrans webhook: invalid signature', ['order_id' => $payload['order_id'] ?? null]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $newStatus = $this->midtrans->mapStatus(
            $payload['transaction_status'] ?? 'pending',
            $payload['fraud_status'] ?? null,
        );

        /* Seluruh pemrosesan dibungkus satu transaksi dengan baris pesanannya
           dikunci (lockForUpdate).
           -----------------------------------------------------------------
           Midtrans mengirim ulang webhook-nya bila balasan pertama tidak
           diterima, dan kiriman ulang bisa tiba BERSAMAAN dengan yang pertama.
           Sebelumnya tidak ada apa pun yang menyerialkan keduanya, dan
           akibatnya berlipat:

             1. updateOrCreate melakukan SELECT lalu INSERT — bukan operasi
                atomik. Dua permintaan sama-sama mendapat "tidak ketemu" dan
                keduanya menyisipkan baris. (Kini juga ditahan indeks unik
                pada midtrans_order_id.)
             2. Keduanya membaca status pesanan "pending", jadi keduanya
                menghitung dirinya sebagai perpindahan ke "paid".
             3. Keduanya mengirim surel konfirmasi — pelanggan menerimanya
                dua kali.
             4. Keduanya menambah seats_taken. Inilah kerugian yang nyata:
                kuota terpakai dua kali untuk satu peserta, dan satu kursi
                yang sebenarnya kosong menolak pendaftar berikutnya.

           lockForUpdate menahan permintaan kedua sampai yang pertama selesai
           dan mengakhiri transaksinya. Ketika giliran yang kedua tiba, ia
           membaca status yang SUDAH diperbarui, sehingga pemeriksaan
           perpindahan di bawah bekerja sebagaimana dimaksud sejak awal.

           Pencarian pesanannya dipindah ke dalam transaksi: baris yang dibaca
           di luar kunci adalah salinan lama, dan mengunci setelah membacanya
           tidak menolong apa-apa. */
        return DB::transaction(function () use ($payload, $newStatus) {
            $midtransOrderId = (string) ($payload['order_id'] ?? '');
            $baseOrderNumber = preg_replace('/-R\d+$/', '', $midtransOrderId);

            $order = Order::where('order_number', $midtransOrderId)
                ->orWhere('order_number', $baseOrderNumber)
                ->orWhereHas('transactions', fn ($q) => $q->where('midtrans_order_id', $midtransOrderId))
                ->lockForUpdate()
                ->first();

            if (! $order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            return $this->applyPayment($order, $payload, $newStatus);
        });
    }

    /**
     * Mencatat transaksinya lalu menjalankan efek samping yang hanya boleh
     * terjadi sekali. Dipanggil hanya dari dalam transaksi berkunci di atas.
     */
    private function applyPayment(Order $order, array $payload, string $newStatus): JsonResponse
    {
        // Idempotent: record/update the transaction and only act on real transitions.
        Transaction::updateOrCreate(
            ['midtrans_order_id' => $payload['order_id']],
            [
                'order_id' => $order->id,
                'transaction_id' => $payload['transaction_id'] ?? null,
                'payment_type' => $payload['payment_type'] ?? null,
                'gross_amount' => $payload['gross_amount'] ?? $order->total_amount,
                'transaction_status' => $payload['transaction_status'] ?? null,
                'fraud_status' => $payload['fraud_status'] ?? null,
                'status_code' => $payload['status_code'] ?? null,
                'va_number' => $payload['va_numbers'][0]['va_number'] ?? ($payload['permata_va_number'] ?? null),
                'bank' => $payload['va_numbers'][0]['bank'] ?? null,
                'payment_code' => $payload['payment_code'] ?? null,
                'signature_key' => $payload['signature_key'] ?? null,
                'transaction_time' => $payload['transaction_time'] ?? null,
                'settlement_time' => $payload['settlement_time'] ?? null,
                'expiry_time' => $payload['expiry_time'] ?? null,
                'raw_response' => $payload,
            ],
        );

        $wasPaid = $order->status === 'paid';

        // Pesanan yang sudah lunas tidak boleh diturunkan kembali ke status gagal/kedaluwarsa
        // oleh notifikasi terlambat dari percobaan pembayaran sebelumnya.
        if ($wasPaid && ! in_array($newStatus, ['paid', 'refunded'], true)) {
            return response()->json(['message' => 'OK']);
        }

        // Bila pelanggan sudah melakukan retry/reset (ada transaksi terbaru yang masih pending),
        // notifikasi expire/cancel/deny dari percobaan lama tidak boleh membatalkan pesanan aktifnya.
        if (in_array($newStatus, ['expired', 'cancelled', 'failed'], true)) {
            $latestTx = $order->latestTransaction()->first();
            if (
                $latestTx
                && $latestTx->midtrans_order_id !== ($payload['order_id'] ?? '')
                && $latestTx->transaction_status === 'pending'
            ) {
                return response()->json(['message' => 'OK']);
            }
        }

        if ($order->status !== $newStatus) {
            $order->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : $order->paid_at,
            ]);

            if ($newStatus === 'paid' && ! $wasPaid) {
                /* Kursi TIDAK ditambah di sini. $order->update() di atas sudah
                   memicu Order::booted(), yang menambah seats_taken milik
                   angkatannya — di dalam transaksi berkunci yang sama, jadi
                   tetap jadi atau batal bersama perubahan status.

                   Dulu di sini juga ada penambahan kursi milik LAYANAN, sehingga
                   satu pembayaran lunas tercatat di dua penghitung sekaligus.
                   Kuota kini hanya milik angkatan. */

                /* Surelnya justru harus di LUAR — afterCommit, bukan langsung.
                   Dua alasan, keduanya nyata:

                   Pertama, surel tidak bisa dibatalkan. Dikirim di dalam
                   transaksi, ia tetap sampai ke pelanggan sekalipun transaksi
                   itu kemudian digagalkan dan pembayarannya tidak pernah
                   tercatat — pelanggan memegang bukti lunas yang tidak ada
                   padanannya di basis data.

                   Kedua, baris pesanan ini sedang dikunci lockForUpdate.
                   Selama SMTP bekerja — dan SMTP bisa makan beberapa detik —
                   kunci itu ditahan, dan setiap kiriman ulang webhook untuk
                   pesanan yang sama ikut menunggu di belakangnya. Di
                   pengembangan hal ini tak terasa karena MAIL_MAILER=log
                   menulis ke berkas dalam hitungan milidetik. */
                DB::afterCommit(function () use ($order) {
                    Notification::route('mail', $order->customer_email)
                        ->notify(new OrderPaidNotification($order));
                });
            }
        }

        return response()->json(['message' => 'OK']);
    }
}
