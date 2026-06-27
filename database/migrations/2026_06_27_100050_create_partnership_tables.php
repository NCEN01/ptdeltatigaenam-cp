<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // (a) Partner logos
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->json('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // (b) Corporate partnership packages
        Schema::create('partnership_packages', function (Blueprint $table) {
            $table->id();
            $table->enum('tier', ['blue', 'silver', 'gold', 'platinum'])->unique('partnership_packages_tier_unique');
            $table->json('name');
            $table->string('slug', 120)->unique();
            $table->json('tagline')->nullable();
            $table->json('description')->nullable();
            $table->json('features')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->json('price_note')->nullable();
            $table->string('color', 20)->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('partnership_benefits', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('partnership_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 50)->unique();
            $table->string('company_name', 200);
            $table->text('company_address');
            $table->string('pic_name', 150);
            $table->string('pic_position', 150)->nullable();
            $table->string('phone', 50);
            $table->string('email', 150);
            $table->foreignId('partnership_package_id')->nullable()
                ->constrained('partnership_packages')->nullOnDelete();
            $table->dateTime('preferred_meeting_at')->nullable();
            $table->dateTime('alternative_meeting_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', [
                'baru', 'dihubungi', 'meeting_dijadwalkan', 'penawaran_dikirim',
                'invoice_diterbitkan', 'lunas', 'selesai', 'dibatalkan',
            ])->default('baru')->index('partnership_registrations_status_idx');
            $table->foreignId('assigned_to')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('locale', 5)->default('id');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->foreignId('partnership_registration_id')->nullable()
                ->constrained('partnership_registrations')->nullOnDelete();
            $table->string('bill_to_company', 200);
            $table->text('bill_to_address')->nullable();
            $table->string('bill_to_pic', 150)->nullable();
            $table->string('bill_to_email', 150)->nullable();
            $table->text('description')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency', 5)->default('IDR');
            $table->enum('status', ['draft', 'terkirim', 'lunas', 'jatuh_tempo', 'dibatalkan'])
                ->default('draft')->index('invoices_status_idx');
            $table->date('issued_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('partnership_registrations');
        Schema::dropIfExists('partnership_benefits');
        Schema::dropIfExists('partnership_packages');
        Schema::dropIfExists('partners');
    }
};
