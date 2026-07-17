<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Display-only discount. `price` stays the real (charged) price; when active,
            // `discount_original_price` (a higher "before" price) is shown struck-through.
            $table->boolean('discount_active')->default(false)->after('price');
            $table->decimal('discount_original_price', 14, 2)->nullable()->after('discount_active');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['discount_active', 'discount_original_price']);
        });
    }
};
