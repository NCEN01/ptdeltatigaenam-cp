<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Isi halaman program
|--------------------------------------------------------------------------
|
| Menambah tiga hal yang belum punya tempat: pustaka mentor, daftar benefit,
| dan spanduk di kepala halaman program.
|
| Lokasi dan mode (luring/daring/bauran) TIDAK ditambahkan di sini — keduanya
| sudah ada sejak awal di kolom services.location dan services.mode, begitu
| pula di tiap jadwalnya. Yang kurang selama ini hanya tampilannya.
|
*/

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Pustaka mentor, bukan mentor milik tiap program. Satu mentor kerap
         * mengajar di beberapa program sekaligus, dan dengan pustaka bersama
         * fotonya cukup diunggah sekali — memperbarui bio pun berlaku di
         * seluruh program yang diampunya.
         */
        Schema::create('mentors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('photo')->nullable();
            // Keahlian singkat yang tampil di bawah nama, misal "Quality
            // Assurance" atau "Ahli K3 Umum".
            $table->string('role', 150)->nullable();
            $table->text('bio')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order'], 'mentors_active_sort_idx');
        });

        Schema::create('mentor_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);

            // Satu mentor tidak bisa terpasang dua kali di program yang sama.
            $table->unique(['mentor_id', 'service_id'], 'mentor_service_unique');
        });

        /*
         * Satu tabel untuk benefit bawaan sekaligus benefit khusus program.
         *
         * service_id kosong berarti benefit bawaan: berlaku untuk setiap
         * program yang tidak punya daftarnya sendiri. Diisi berarti benefit
         * khusus program itu, dan begitu satu program punya minimal satu
         * baris, daftar bawaannya tidak lagi dipakai untuk program tersebut.
         *
         * Dua tabel terpisah sempat dipertimbangkan dan dilepas: resolusinya
         * sama saja, tapi admin harus mengingat dua tempat berbeda untuk satu
         * hal yang dalam kepalanya satu.
         */
        Schema::create('program_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            // Nama ikon dari <x-category-icon>, bukan berkas: ikonnya sudah
            // tersedia sebagai SVG di komponen itu dan tidak perlu diunggah.
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['service_id', 'is_active', 'sort_order'], 'program_benefits_lookup_idx');
        });

        Schema::table('services', function (Blueprint $table) {
            // Spanduk di kepala bagian "Tentang Program". Terpisah dari kolom
            // image yang dipakai kartu daftar: yang satu potret tegak di kisi
            // tiga kolom, yang satu bentang lebar di dalam halaman, dan satu
            // berkas tidak bisa melayani keduanya tanpa terpotong buruk di
            // salah satunya.
            $table->string('banner_image')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('banner_image');
        });

        Schema::dropIfExists('program_benefits');
        Schema::dropIfExists('mentor_service');
        Schema::dropIfExists('mentors');
    }
};
