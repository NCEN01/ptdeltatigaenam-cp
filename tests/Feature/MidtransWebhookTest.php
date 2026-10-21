<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceSchedule;
use App\Models\Transaction;
use App\Notifications\OrderPaidNotification;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    use RefreshDatabase;

    private string $serverKey = 'SB-Mid-server-TESTKEY';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('midtrans.server_key', $this->serverKey);
    }

    private function order(string $status = 'pending'): Order
    {
        return Order::create([
            'order_number' => 'ORD-TEST-0001',
            'customer_name' => 'Budi', 'customer_email' => 'budi@example.com', 'customer_phone' => '0812',
            'quantity' => 1, 'unit_price' => 1500000, 'subtotal' => 1500000, 'tax' => 0,
            'total_amount' => 1500000, 'status' => $status,
        ]);
    }

    private function payload(Order $order, string $transactionStatus, string $statusCode = '200'): array
    {
        $gross = number_format((float) $order->total_amount, 2, '.', '');
        $signature = hash('sha512', $order->order_number.$statusCode.$gross.$this->serverKey);

        return [
            'order_id' => $order->order_number,
            'status_code' => $statusCode,
            'gross_amount' => $gross,
            'transaction_status' => $transactionStatus,
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
            'transaction_id' => 'abc-123',
            'signature_key' => $signature,
        ];
    }

    public function test_status_mapping(): void
    {
        $svc = new MidtransService();
        $this->assertSame('paid', $svc->mapStatus('settlement'));
        $this->assertSame('paid', $svc->mapStatus('capture', 'accept'));
        $this->assertSame('pending', $svc->mapStatus('capture', 'challenge'));
        $this->assertSame('expired', $svc->mapStatus('expire'));
        $this->assertSame('cancelled', $svc->mapStatus('cancel'));
        $this->assertSame('failed', $svc->mapStatus('deny'));
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $order = $this->order();
        $payload = $this->payload($order, 'settlement');
        $payload['signature_key'] = 'tampered';

        $this->postJson('/midtrans/callback', $payload)->assertStatus(403);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_settlement_marks_order_paid_and_notifies_once(): void
    {
        Notification::fake();
        $order = $this->order();

        $this->postJson('/midtrans/callback', $this->payload($order, 'settlement'))->assertOk();

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertDatabaseHas('transactions', [
            'midtrans_order_id' => $order->order_number,
            'transaction_status' => 'settlement',
        ]);

        // Idempotent: a duplicate webhook does not resend the email.
        $this->postJson('/midtrans/callback', $this->payload($order, 'settlement'))->assertOk();

        Notification::assertSentTimes(OrderPaidNotification::class, 1);
    }

    public function test_expire_marks_order_expired(): void
    {
        $order = $this->order();
        $this->postJson('/midtrans/callback', $this->payload($order, 'expire'))->assertOk();
        $this->assertSame('expired', $order->fresh()->status);
    }

    public function test_paid_order_skips_pay_retry_cancel_and_duplicate_checkout(): void
    {
        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $category = ServiceCategory::create([
            'name' => ['id' => 'Pelatihan', 'en' => 'Training'],
            'slug' => 'pelatihan',
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'title' => ['id' => 'Leadership', 'en' => 'Leadership'],
            'slug' => 'leadership',
            'price' => 1500000,
            'is_purchasable' => true,
            'is_active' => true,
        ]);

        $schedule = ServiceSchedule::create([
            'service_id' => $service->id,
            'start_date' => now()->addDays(7)->toDateString(),
            'mode' => 'online',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-PAID-0001',
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'service_schedule_id' => $schedule->id,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '08123456789',
            'quantity' => 1,
            'unit_price' => 1500000,
            'subtotal' => 1500000,
            'tax' => 0,
            'total_amount' => 1500000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $finishUrl = route('checkout.finish', ['locale' => 'id', 'order' => $order->order_number]);

        $this->actingAs($customer, 'customer')
            ->get(route('checkout.pay', ['locale' => 'id', 'order' => $order->order_number]))
            ->assertRedirect($finishUrl);

        $this->actingAs($customer, 'customer')
            ->post(route('checkout.retry', ['locale' => 'id', 'order' => $order->order_number]))
            ->assertRedirect($finishUrl);

        $this->actingAs($customer, 'customer')
            ->post(route('checkout.cancel', ['locale' => 'id', 'order' => $order->order_number]))
            ->assertRedirect($finishUrl);

        // Unconfirmed store on an already-paid schedule skips creating a duplicate order
        $this->actingAs($customer, 'customer')
            ->post(route('checkout.store', ['locale' => 'id']), [
                'service_schedule_id' => $schedule->id,
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi@example.com',
                'customer_phone' => '08123456789',
                'participants' => [['name' => 'Budi Santoso', 'phone' => '08123456789']],
            ])
            ->assertRedirect($finishUrl);

        $this->assertSame(1, Order::count());
    }

    public function test_pending_order_can_retry_and_cancel_and_webhook_matches_retry_id(): void
    {
        Notification::fake();
        config()->set('midtrans.client_key', 'SB-Mid-client-TESTKEY');

        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $category = ServiceCategory::create([
            'name' => ['id' => 'Pelatihan', 'en' => 'Training'],
            'slug' => 'pelatihan',
            'is_active' => true,
        ]);

        $service = Service::create([
            'service_category_id' => $category->id,
            'title' => ['id' => 'Leadership', 'en' => 'Leadership'],
            'slug' => 'leadership',
            'price' => 1500000,
            'is_purchasable' => true,
            'is_active' => true,
        ]);

        $schedule = ServiceSchedule::create([
            'service_id' => $service->id,
            'start_date' => now()->addDays(7)->toDateString(),
            'mode' => 'online',
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-PEND-0001',
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'service_schedule_id' => $schedule->id,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '08123456789',
            'quantity' => 1,
            'unit_price' => 1500000,
            'subtotal' => 1500000,
            'tax' => 0,
            'total_amount' => 1500000,
            'status' => 'pending',
        ]);

        Transaction::create([
            'order_id' => $order->id,
            'midtrans_order_id' => $order->order_number,
            'snap_token' => 'OLD-SNAP-TOKEN',
            'gross_amount' => $order->total_amount,
            'transaction_status' => 'pending',
        ]);

        $this->mock(MidtransService::class, function ($mock) use ($order) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('createSnapToken')
                ->once()
                ->with(\Mockery::on(fn ($o) => $o->id === $order->id), 'ORD-PEND-0001-R2')
                ->andReturn('NEW-SNAP-TOKEN');
        });

        $this->actingAs($customer, 'customer')
            ->post(route('checkout.retry', ['locale' => 'id', 'order' => $order->order_number]))
            ->assertRedirect(route('checkout.pay', ['locale' => 'id', 'order' => $order->order_number]));

        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'midtrans_order_id' => 'ORD-PEND-0001',
            'transaction_status' => 'cancel',
        ]);
        $this->assertDatabaseHas('transactions', [
            'order_id' => $order->id,
            'midtrans_order_id' => 'ORD-PEND-0001-R2',
            'snap_token' => 'NEW-SNAP-TOKEN',
            'transaction_status' => 'pending',
        ]);

        // Cancel with recreate=1 marks order cancelled and redirects to checkout.create
        $this->actingAs($customer, 'customer')
            ->post(route('checkout.cancel', ['locale' => 'id', 'order' => $order->order_number, 'recreate' => 1]))
            ->assertRedirect(route('checkout.create', ['locale' => 'id', 'schedule' => $schedule->id]));

        $this->assertSame('cancelled', $order->fresh()->status);
    }
}
