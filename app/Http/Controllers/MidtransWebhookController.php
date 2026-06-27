<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Notifications\OrderPaidNotification;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $order = Order::where('order_number', $payload['order_id'] ?? '')->first();
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $newStatus = $this->midtrans->mapStatus(
            $payload['transaction_status'] ?? 'pending',
            $payload['fraud_status'] ?? null,
        );

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

        if ($order->status !== $newStatus) {
            $order->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : $order->paid_at,
            ]);

            // Send confirmation once, only on the transition into "paid".
            if ($newStatus === 'paid' && ! $wasPaid) {
                Notification::route('mail', $order->customer_email)
                    ->notify(new OrderPaidNotification($order));
            }
        }

        return response()->json(['message' => 'OK']);
    }
}
