<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->enum('mode', ['offline', 'online', 'hybrid'])->default('offline')->after('location');
            $table->integer('quota')->nullable()->after('mode');
            $table->integer('seats_taken')->default(0)->after('quota');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['mode', 'quota', 'seats_taken']);
        });
    }
};