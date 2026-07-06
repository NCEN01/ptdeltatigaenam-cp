<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_updates', function (Blueprint $table) {
            $table->id();
            $table->string('image');
            $table->json('batch_label')->nullable();
            $table->json('title')->nullable();
            $table->json('company')->nullable();
            $table->json('date_range')->nullable();
            $table->string('instagram_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_updates');
    }
};