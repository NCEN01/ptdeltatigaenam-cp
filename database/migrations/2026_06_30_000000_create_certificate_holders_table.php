<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_holders', function (Blueprint $table) {
            $table->id();
            $table->string('ujk_number', 50)->nullable();       // NO. UJK
            $table->string('participant_name', 200);            // PESERTA
            $table->string('company_name', 200)->nullable();    // NAMA PERUSAHAAN
            $table->string('certificate_number', 100)->nullable(); // NO. SERTIFIKAT
            $table->string('qualification', 200)->nullable();   // KUALIFIKASI
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();             // TGL BERAKHIR
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'expires_at'], 'cert_holders_active_expires_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_holders');
    }
};
