<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Saklar "tampilkan deskripsi" untuk banner kepala halaman.
 *
 * Sebelum ini, mengosongkan kolom subjudul di panel admin tidak menghilangkan
 * deskripsinya: <x-page-header> hanya menimpa teks bawaan halaman bila subjudul
 * banner terisi, sehingga kolom yang dikosongkan justru memunculkan kembali
 * kalimat yang ditulis di Blade. Admin tidak punya cara apa pun untuk membuat
 * kepala halaman tampil tanpa deskripsi.
 *
 * Satu kolom boolean memisahkan dua maksud yang sebelumnya tertumpuk pada satu
 * kolom kosong: "pakai bawaan halaman" (terisi true, subjudul kosong) dan
 * "jangan tampilkan apa pun" (false).
 *
 * default(true) supaya seluruh banner yang sudah ada berperilaku persis seperti
 * sebelumnya setelah migrasi ini jalan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->boolean('show_subtitle')->default(true)->after('subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('show_subtitle');
        });
    }
};
