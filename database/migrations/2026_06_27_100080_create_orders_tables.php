<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('customer_id')->nullable()
                ->constrained('customers')->nullOnDelete();
            $table->foreignId('service_id')->nullable()
                ->constrained('services')->nullOnDelete();
            $table->foreignId('service_schedule_id')->nullable()
                ->constrained('service_schedules')->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('customer_email', 150)->index('orders_email_idx');
            $table->string('customer_phone', 50);
            $table->string('customer_company', 200)->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled', 'failed', 'refunded'])
                ->default('pending')->index('orders_status_idx');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('midtrans_order_id', 100)->index('transactions_midtrans_order_idx');
            $table->string('transaction_id', 100)->nullable();
            $table->string('snap_token')->nullable();
            $table->string('payment_type', 50)->nullable();
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->string('transaction_status', 50)->nullable()->index('transactions_status_idx');
            $table->string('fraud_status', 50)->nullable();
            $table->string('status_code', 10)->nullable();
            $table->string('va_number', 100)->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('payment_code', 100)->nullable();
            $table->string('signature_key')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->timestamp('expiry_time')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('orders');
    }
};
