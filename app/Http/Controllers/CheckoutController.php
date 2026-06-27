<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ServiceSchedule;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_company' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $schedule = ServiceSchedule::with('service')->findOrFail($data['service_schedule_id']);
        abort_unless($schedule->service && $schedule->service->is_purchasable, 404);

        $unit = $schedule->effectivePrice();
        $qty = (int) $data['quantity'];
        $subtotal = $unit * $qty;

        $order = DB::transaction(function () use ($request, $schedule, $data, $unit, $qty, $subtotal) {
            return Order::create([
                'order_number' => Order::generateNumber(),
                'customer_id' => $request->user('customer')->id,
                'service_id' => $schedule->service_id,
                'service_schedule_id' => $schedule->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_company' => $data['customer_company'] ?? null,
                'quantity' => $qty,
                'unit_price' => $unit,
                'subtotal' => $subtotal,
                'tax' => 0,
                'total_amount' => $subtotal,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
            ]);
        });

        $token = $this->midtrans->createSnapToken($order->load('service'));

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

        $token = $order->latestTransaction?->snap_token
            ?? $this->midtrans->createSnapToken($order->load('service'));

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

        return view('pages.checkout.finish', ['order' => $order->fresh()]);
    }
}
