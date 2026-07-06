<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->string('placement')->default('home_hero')->change();
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->enum('placement', [
                'home_hero', 'home_section', 'service_category', 'service',
                'blog', 'portfolio', 'about', 'global',
            ])->default('home_hero')->change();
        });
    }
};