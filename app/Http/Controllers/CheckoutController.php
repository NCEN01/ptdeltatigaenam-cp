<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ServiceSchedule;
use App\Models\Transaction;
use App\Notifications\OrderPaidNotification;
use App\Rules\PersonName;
use App\Rules\PhoneNumber;
use App\Rules\SafeEmail;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;

class CheckoutController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    /** Confirm page for a chosen schedule. */
    public function create(Request $request, string $locale, ServiceSchedule $schedule)
    {
        $schedule->load('service');
        abort_unless($schedule->service && $schedule->service->is_purchasable, 404);

        $customer = $request->user('customer');

        $paidOrder = Order::where('customer_id', $customer->id)
            ->where('service_schedule_id', $schedule->id)
            ->where('status', 'paid')
            ->latest('paid_at')
            ->latest('id')
            ->first();

        $pendingOrder = Order::where('customer_id', $customer->id)
            ->where('service_schedule_id', $schedule->id)
            ->where('status', 'pending')
            ->latest('id')
            ->first();

        return view('pages.checkout.create', [
            'schedule' => $schedule,
            'service' => $schedule->service,
            'customer' => $customer,
            'unitPrice' => $schedule->effectivePrice(),
            'paidOrder' => $paidOrder,
            'pendingOrder' => $pendingOrder,
            'repurchase' => $request->boolean('repurchase'),
        ]);
    }

    /** Create the pending order + Snap token, then go to the pay page. */
    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'service_schedule_id' => [
                'required', 'integer',
                Rule::exists('service_schedules', 'id')->where('is_active', true),
            ],
            'customer_name' => ['required', 'string', 'max:150', new PersonName],
            'customer_email' => ['required', 'email', 'max:150', new SafeEmail],
            'customer_phone' => ['required', 'string', 'max:50', new PhoneNumber],
            'customer_company' => ['nullable', 'string', 'min:2', 'max:200'],
            'participants' => ['required', 'array', 'min:1', 'max:50'],
            'participants.*.name' => ['required', 'string', 'max:150', new PersonName],
            'participants.*.phone' => ['required', 'string', 'max:50', new PhoneNumber],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $schedule = ServiceSchedule::with('service')->findOrFail($data['service_schedule_id']);
        abort_unless($schedule->service && $schedule->service->is_purchasable, 404);

        $customer = $request->user('customer');

        // Jika pelanggan sudah memiliki pesanan lunas pada jadwal ini dan belum
        // mengonfirmasi pembelian ulang (confirm_repurchase), langsung skip ke status lunas.
        $paidOrder = Order::where('customer_id', $customer->id)
            ->where('service_schedule_id', $schedule->id)
            ->where('status', 'paid')
            ->latest('paid_at')
            ->latest('id')
            ->first();

        if ($paidOrder && ! $request->boolean('confirm_repurchase')) {
            return redirect()->route('checkout.finish', $paidOrder->order_number)
                ->with('status', __('site.checkout.already_paid_redirect'));
        }

        // Jadwal yang sudah selesai tidak bisa dibeli lagi.
        if ($schedule->timeframe === 'past') {
            return back()->withInput()->with('error', __('Jadwal ini sudah berakhir.'));
        }

        /* Kuota dicek terhadap jumlah peserta yang dipesan, bukan sekadar
           "sudah penuh atau belum": sisa 2 kursi tidak boleh menerima 5 peserta.
           Kuota layanan dan kuota batch jadwal sama-sama dihormati. */
        $service = $schedule->service;
        $qtyRequested = count($data['participants']);
        $serviceLeft = $service->quota ? max(0, $service->quota - $service->seats_taken) : null;
        $scheduleLeft = $schedule->seats_available;
        $left = collect([$serviceLeft, $scheduleLeft])->filter(fn ($v) => $v !== null)->min();

        if ($left !== null && $qtyRequested > $left) {
            $msg = $left === 0
                ? __('Kuota sudah penuh.')
                : __('Sisa kuota hanya :n peserta.', ['n' => $left]);

            return back()->withInput()->with('error', $msg);
        }

        if (! $this->midtrans->isConfigured()) {
            return back()->withInput()->with('error', __('site.checkout.unavailable'));
        }

        // Participant list is the source of truth for the quantity.
        $participants = array_values($data['participants']);
        $unit = $schedule->effectivePrice();
        $qty = count($participants);
        $subtotal = $unit * $qty;

        $order = DB::transaction(function () use ($customer, $schedule, $data, $participants, $unit, $qty, $subtotal) {
            // Reset/batalkan pesanan pending lama milik pelanggan di jadwal yang sama
            // agar tidak menumpuk pesanan menggantung saat pelanggan mengisi ulang form checkout.
            $stalePendingOrders = Order::where('customer_id', $customer->id)
                ->where('service_schedule_id', $schedule->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->get();

            foreach ($stalePendingOrders as $stale) {
                $stale->update(['status' => 'cancelled']);
                $stale->transactions()
                    ->where('transaction_status', 'pending')
                    ->update(['transaction_status' => 'cancel']);
            }

            $order = Order::create([
                'order_number' => Order::generateNumber(),
                'customer_id' => $customer->id,
                'service_id' => $schedule->service_id,
                'service_schedule_id' => $schedule->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_company' => $data['customer_company'] ?? null,
                'quantity' => $qty,
                'participants' => $participants,
                'unit_price' => $unit,
                'subtotal' => $subtotal,
                'tax' => 0,
                'total_amount' => $subtotal,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);

            foreach ($participants as $p) {
                $order->attendees()->create([
                    'name' => $p['name'],
                    'phone' => $p['phone'] ?? null,
                ]);
            }

            return $order;
        });

        try {
            $token = $this->midtrans->createSnapToken($order->load('service'));
        } catch (\Throwable $e) {
            report($e);
            $order->update(['status' => 'failed']);

            return back()->withInput()->with('error', __('site.checkout.gateway_error'));
        }

        Transaction::create([
            'order_id' => $order->id,
            'midtrans_order_id' => $order->order_number,
            'snap_token' => $token,
            'gross_amount' => $order->total_amount,
            'transaction_status' => 'pending',
        ]);

        return redirect()->route('checkout.pay', $order->order_number);
    }

    /** Snap payment page. */
    public function pay(Request $request, string $locale, Order $order)
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 403);

        if ($order->status === 'paid') {
            return redirect()->route('checkout.finish', $order->order_number)
                ->with('status', __('site.checkout.already_paid_redirect'));
        }

        if (in_array($order->status, ['cancelled', 'refunded'], true)) {
            return redirect()->route('checkout.finish', $order->order_number);
        }

        if (! $this->midtrans->isConfigured()) {
            return redirect()->route('services.index')->with('error', __('site.checkout.unavailable'));
        }

        try {
            $latestTx = $order->latestTransaction;
            if ($latestTx?->snap_token && $order->status === 'pending') {
                $token = $latestTx->snap_token;
            } else {
                $midtransOrderId = $this->nextMidtransOrderId($order);
                $token = $this->midtrans->createSnapToken($order->load('service'), $midtransOrderId);

                Transaction::create([
                    'order_id' => $order->id,
                    'midtrans_order_id' => $midtransOrderId,
                    'snap_token' => $token,
                    'gross_amount' => $order->total_amount,
                    'transaction_status' => 'pending',
                ]);

                if ($order->status !== 'pending') {
                    $order->update(['status' => 'pending']);
                }
            }
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('services.index')->with('error', __('site.checkout.gateway_error'));
        }

        return view('pages.checkout.pay', [
            'order' => $order,
            'snapToken' => $token,
            'clientKey' => config('midtrans.client_key'),
        ]);
    }

    /**
     * Retry / reset Snap payment for a pending, expired, or failed order.
     * Issues a new Midtrans order_id suffix (-R2, -R3, ...) so Midtrans Snap
     * allows the customer to pick a different payment channel or retry cleanly.
     */
    public function retry(Request $request, string $locale, Order $order)
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 403);

        if ($order->status === 'paid') {
            return redirect()->route('checkout.finish', $order->order_number)
                ->with('status', __('site.checkout.already_paid_redirect'));
        }

        if (! in_array($order->status, ['pending', 'expired', 'failed'], true)) {
            return redirect()->route('checkout.finish', $order->order_number);
        }

        $order->load(['service', 'schedule']);

        if ($order->schedule && $order->schedule->timeframe === 'past') {
            return redirect()->route('account.orders')->with('error', __('Jadwal ini sudah berakhir.'));
        }

        $service = $order->service;
        $serviceLeft = ($service && $service->quota) ? max(0, $service->quota - $service->seats_taken) : null;
        $scheduleLeft = $order->schedule?->seats_available;
        $left = collect([$serviceLeft, $scheduleLeft])->filter(fn ($v) => $v !== null)->min();

        if ($left !== null && $order->quantity > $left) {
            $msg = $left === 0
                ? __('Kuota sudah penuh.')
                : __('Sisa kuota hanya :n peserta.', ['n' => $left]);

            return redirect()->route('account.orders')->with('error', $msg);
        }

        if (! $this->midtrans->isConfigured()) {
            return redirect()->route('account.orders')->with('error', __('site.checkout.unavailable'));
        }

        $midtransOrderId = $this->nextMidtransOrderId($order);

        try {
            $token = $this->midtrans->createSnapToken($order, $midtransOrderId);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('account.orders')->with('error', __('site.checkout.gateway_error'));
        }

        DB::transaction(function () use ($order, $midtransOrderId, $token) {
            $order->transactions()
                ->where('transaction_status', 'pending')
                ->update(['transaction_status' => 'cancel']);

            Transaction::create([
                'order_id' => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'snap_token' => $token,
                'gross_amount' => $order->total_amount,
                'transaction_status' => 'pending',
            ]);

            if ($order->status !== 'pending') {
                $order->update(['status' => 'pending']);
            }
        });

        return redirect()->route('checkout.pay', $order->order_number)
            ->with('status', __('site.checkout.payment_reset'));
    }

    /**
     * Cancel a pending/expired/failed order (or reset it and return to the checkout form).
     */
    public function cancel(Request $request, string $locale, Order $order)
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 403);

        if ($order->status === 'paid') {
            return redirect()->route('checkout.finish', $order->order_number)
                ->with('status', __('site.checkout.already_paid_redirect'));
        }

        if (in_array($order->status, ['pending', 'expired', 'failed'], true)) {
            DB::transaction(function () use ($order) {
                $order->update(['status' => 'cancelled']);
                $order->transactions()
                    ->where('transaction_status', 'pending')
                    ->update(['transaction_status' => 'cancel']);
            });
        }

        if ($request->boolean('recreate') && $order->service_schedule_id) {
            return redirect()->route('checkout.create', $order->service_schedule_id)
                ->with('status', __('site.checkout.cancelled_recreate'));
        }

        return redirect()->route('account.orders')
            ->with('status', __('site.checkout.cancelled'));
    }

    /** Result page after returning from Snap. */
    public function finish(Request $request, string $locale, Order $order)
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 403);

        // When returning from Midtrans Snap in sandbox, auto-confirm the payment
        // because webhooks cannot reach localhost. Skip if already paid.
        $status = $request->query('transaction_status');
        if ($order->status !== 'paid' && ! config('midtrans.is_production') && in_array($status, ['settlement', 'capture'], true)) {
            $this->confirmSandboxOrder($order, (string) $request->query('payment_type', 'midtrans'));
        }

        return view('pages.checkout.finish', ['order' => $order->fresh()]);
    }

    /**
     * Sandbox only: manually confirm an order as paid. Useful in local dev where
     * the Midtrans webhook cannot reach localhost, so orders stay "pending" even
     * after a successful Snap simulation. Disabled in production.
     */
    public function simulatePaid(Request $request, string $locale, Order $order)
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 403);
        abort_if(config('midtrans.is_production'), 404);

        if ($order->status === 'paid') {
            return redirect()->route('checkout.finish', $order->order_number)
                ->with('status', __('site.checkout.already_paid_redirect'));
        }

        $this->confirmSandboxOrder($order, 'simulation');

        return redirect()->route('checkout.finish', $order->order_number)
            ->with('success', __('site.checkout.paid_simulated'));
    }

    /**
     * Generate a unique Midtrans order_id for retry attempts (e.g. ORD-20261021-ABC123-R2).
     */
    private function nextMidtransOrderId(Order $order): string
    {
        $attempt = $order->transactions()->count() + 1;
        if ($attempt === 1) {
            return $order->order_number;
        }

        $candidate = "{$order->order_number}-R{$attempt}";
        while (Transaction::where('midtrans_order_id', $candidate)->exists()) {
            $attempt++;
            $candidate = "{$order->order_number}-R{$attempt}";
        }

        return $candidate;
    }

    /**
     * Kunci baris pesanan dengan lockForUpdate sebelum mengubah status ke paid,
     * supaya tidak balapan dengan MidtransWebhookController bila sandbox diuji
     * memakai tunnel (ngrok/cloudflared).
     */
    private function confirmSandboxOrder(Order $order, string $paymentType): void
    {
        DB::transaction(function () use ($order, $paymentType) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();
            if (! $locked || $locked->status === 'paid') {
                return;
            }

            $midtransOrderId = $locked->latestTransaction?->midtrans_order_id ?? $locked->order_number;

            Transaction::updateOrCreate(
                ['midtrans_order_id' => $midtransOrderId],
                [
                    'order_id' => $locked->id,
                    'gross_amount' => $locked->total_amount,
                    'transaction_status' => 'settlement',
                    'payment_type' => $paymentType,
                    'transaction_time' => now(),
                    'settlement_time' => now(),
                ],
            );

            $locked->update(['status' => 'paid', 'paid_at' => now()]);

            $service = $locked->service;
            if ($service && $service->quota) {
                $service->increment('seats_taken', $locked->quantity);
            }

            DB::afterCommit(function () use ($locked) {
                Notification::route('mail', $locked->customer_email)
                    ->notify(new OrderPaidNotification($locked->fresh()));
            });
        });
    }
}
