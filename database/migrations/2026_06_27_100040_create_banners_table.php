<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->string('image');
            $table->string('image_mobile')->nullable();
            $table->string('link_url')->nullable();
            $table->json('button_text')->nullable();
            $table->enum('placement', [
                'home_hero', 'home_section', 'service_category', 'service',
                'blog', 'portfolio', 'about', 'global',
            ])->default('home_hero');
            $table->foreignId('service_category_id')->nullable()
                ->constrained('service_categories')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->index(['placement', 'is_active'], 'banners_placement_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
