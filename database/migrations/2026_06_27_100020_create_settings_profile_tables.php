<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->default('general');
            $table->string('key', 150)->unique();
            $table->longText('value')->nullable();
            $table->string('type', 50)->default('text');
            $table->timestamps();
        });

        Schema::create('company_missions', function (Blueprint $table) {
            $table->id();
            $table->json('content');
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->enum('type', ['pusat', 'pemasaran', 'operasional', 'lainnya'])->default('lainnya');
            $table->text('address');
            $table->string('phone', 100)->nullable();
            $table->string('whatsapp', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('map_embed')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_locations');
        Schema::dropIfExists('company_missions');
        Schema::dropIfExists('settings');
    }
};
