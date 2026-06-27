<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('slug', 280)->unique();
            $table->string('client_name', 200)->nullable();
            $table->foreignId('service_category_id')->nullable()
                ->constrained('service_categories')->nullOnDelete();
            $table->json('short_description')->nullable();
            $table->json('content')->nullable();
            $table->string('cover_image')->nullable();
            $table->date('project_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('portfolio_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('portfolios')->cascadeOnDelete();
            $table->string('image');
            $table->json('caption')->nullable();
            $table->integer('sort_order')->default(0);
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('logo')->nullable();
            $table->string('website_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()
                ->constrained('clients')->nullOnDelete();
            $table->foreignId('portfolio_id')->nullable()
                ->constrained('portfolios')->nullOnDelete();
            $table->string('author_name', 150);
            $table->json('author_position')->nullable();
            $table->string('author_company', 200)->nullable();
            $table->string('author_photo')->nullable();
            $table->json('content');
            $table->tinyInteger('rating')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('portfolio_images');
        Schema::dropIfExists('portfolios');
    }
};
