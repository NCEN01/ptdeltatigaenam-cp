<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catatan aktivitas panel admin: siapa mengubah apa, kapan.
 *
 * Dibangun sendiri alih-alih memasang spatie/laravel-activitylog. Yang
 * dibutuhkan di sini satu tabel, satu pendengar, dan satu halaman daftar —
 * sementara paket itu membawa konfigurasi, batch, dan relasi sebab-akibat yang
 * tidak satu pun dipakai. Menambah dependensi menjelang produksi juga berarti
 * menambah satu hal lagi yang harus diikuti pembaruannya.
 *
 * user_name disimpan sebagai salinan, bukan hanya user_id. Catatan audit harus
 * tetap terbaca setelah akunnya dihapus; dengan relasi saja, baris lamanya
 * berubah menjadi "—" persis ketika ia paling dibutuhkan. Karena itu pula
 * user_id memakai nullOnDelete, bukan cascadeOnDelete: menghapus admin tidak
 * boleh ikut menghapus jejak perbuatannya.
 *
 * subject_label juga salinan, dengan alasan yang sama: nama banner atau judul
 * artikel yang sudah dihapus tidak bisa dibaca lagi dari tabel asalnya, dan
 * baris "menghapus Banner #42" tanpa namanya tidak memberi tahu apa pun.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name', 150);

            $table->string('event', 20);                  // created | updated | deleted
            $table->string('subject_type', 150);          // nama kelas model
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label', 200)->nullable();

            // Hanya kolom yang benar-benar berubah, beserta nilai lama dan barunya.
            $table->json('changes')->nullable();

            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            /* Halaman daftarnya selalu mengurutkan terbaru lebih dulu, dan
               penyaringnya menurut jenis data atau menurut admin. Ketiga
               indeks ini yang dipakai ketiga jalur itu. */
            $table->index('created_at');
            $table->index(['subject_type', 'subject_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
