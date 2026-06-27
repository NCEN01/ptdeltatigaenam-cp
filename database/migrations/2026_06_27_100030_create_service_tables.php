<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug', 170)->unique();
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'is_featured'], 'service_categories_active_featured_idx');
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->json('title');
            $table->string('slug', 220)->unique();
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->json('price_label')->nullable();
            $table->json('duration')->nullable();
            $table->string('location', 200)->nullable();
            $table->boolean('is_purchasable')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'is_purchasable'], 'services_active_purchasable_idx');
        });

        Schema::create('service_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->json('title');
            $table->json('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->date('start_date')->index('service_schedules_date_idx');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location', 200)->nullable();
            $table->enum('mode', ['offline', 'online', 'hybrid'])->default('offline');
            $table->integer('quota')->nullable();
            $table->integer('seats_taken')->default(0);
            $table->decimal('price_override', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_schedules');
        Schema::dropIfExists('service_activities');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
