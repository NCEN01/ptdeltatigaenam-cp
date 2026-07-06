<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ServiceSchedule;
use App\Models\Transaction;
use App\Notifications\OrderPaidNotification;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CheckoutController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    /** Confirm page for a chosen schedule. */
    public function create(Request $request, string $locale, ServiceSchedule $schedule)
    {
        $schedule->load('service');
        abort_unless($schedule->service && $schedule->service->is_purchasable, 404);

        return view('pages.checkout.create', [
            'schedule' => $schedule,
            'service' => $schedule->service,
            'customer' => $request->user('customer'),
            'unitPrice' => $schedule->effectivePrice(),
        ]);
    }

    /** Create the pending order + Snap token, then go to the pay page. */
    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'service_schedule_id' => ['required', 'exists:service_schedules,id'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_company' => ['nullable', 'string', 'max:200'],
            'participants' => ['required', 'array', 'min:1', 'max:50'],
            'participants.*.name' => ['required', 'string', 'max:150'],
            'participants.*.phone' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $schedule = ServiceSchedule::with('service')->findOrFail($data['service_schedule_id']);
        abort_unless($schedule->service && $schedule->service->is_purchasable, 404);

        // Check quota: if quota is set and full, reject.
        $service = $schedule->service;
        if ($service->quota && $service->seats_taken >= $service->quota) {
            return back()->withInput()->with('error', __('Kuota sudah penuh.'));
        }

        if (! $this->midtrans->isConfigured()) {
            return back()->withInput()->with('error', __('site.checkout.unavailable'));
        }

        // Participant list is the source of truth for the quantity.
        $participants = array_values($data['participants']);
        $unit = $schedule->effectivePrice();
        $qty = count($participants);
        $subtotal = $unit * $qty;

        $order = DB::transaction(function () use ($request, $schedule, $data, $participants, $unit, $qty, $subtotal) {
            $order = Order::create([
                'order_number' => Order::generateNumber(),
                'customer_id' => $request->user('customer')->id,
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
            return redirect()->route('checkout.finish', $order->order_number);
        }

        if (! $this->midtrans->isConfigured()) {
            return redirect()->route('services.index')->with('error', __('site.checkout.unavailable'));
        }

        try {
            $token = $order->latestTransaction?->snap_token
                ?? $this->midtrans->createSnapToken($order->load('service'));
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

    /** Result page after returning from Snap. */
    public function finish(Request $request, string $locale, Order $order)
    {
        abort_unless($order->customer_id === $request->user('customer')->id, 403);

        // When returning from Midtrans Snap in sandbox, auto-confirm the payment
        // because webhooks cannot reach localhost.
        $status = $request->query('transaction_status');
        if (!config('midtrans.is_production') && in_array($status, ['settlement', 'capture'])) {
            if ($order->status !== 'paid') {
                DB::transaction(function () use ($order, $request) {
                    Transaction::updateOrCreate(
                        ['midtrans_order_id' => $order->order_number],
                        [
                            'order_id' => $order->id,
                            'gross_amount' => $order->total_amount,
                            'transaction_status' => 'settlement',
                            'payment_type' => $request->query('payment_type', 'midtrans'),
                            'transaction_time' => now(),
                            'settlement_time' => now(),
                        ],
                    );

                    $order->update(['status' => 'paid', 'paid_at' => now()]);

                    // Increment seats_taken
                    $service = $order->service;
                    if ($service && $service->quota) {
                        $service->increment('seats_taken', $order->quantity);
                    }
                });

                Notification::route('mail', $order->customer_email)
                    ->notify(new OrderPaidNotification($order->fresh()));
            }
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

        if ($order->status !== 'paid') {
            DB::transaction(function () use ($order) {
                Transaction::updateOrCreate(
                    ['midtrans_order_id' => $order->order_number],
                    [
                        'order_id' => $order->id,
                        'gross_amount' => $order->total_amount,
                        'transaction_status' => 'settlement',
                        'payment_type' => 'simulation',
                        'transaction_time' => now(),
                        'settlement_time' => now(),
                    ],
                );

                $order->update(['status' => 'paid', 'paid_at' => now()]);

                // Increment seats_taken on the service
                $service = $order->service;
                if ($service && $service->quota) {
                    $service->increment('seats_taken', $order->quantity);
                }
            });

            Notification::route('mail', $order->customer_email)
                ->notify(new OrderPaidNotification($order->fresh()));
        }

        return redirect()->route('checkout.finish', $order->order_number)
            ->with('success', __('site.checkout.paid_simulated'));
    }
}
