<?php

namespace Tests\Feature;

use App\Models\Order;
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
}
