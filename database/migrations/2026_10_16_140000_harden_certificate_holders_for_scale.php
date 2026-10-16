<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyiapkan daftar pemegang sertifikat untuk jumlah ribuan.
 *
 * Tabel ini satu-satunya di situs yang isinya tumbuh terus tanpa pernah
 * dibuang: setiap peserta yang pernah lulus uji kompetensi menambah satu baris,
 * dan tidak ada alasan menghapusnya karena justru riwayat itulah yang dicari
 * pengunjung. Saat migrasi ini ditulis isinya 25 baris; yang disiapkan di sini
 * keadaan ketika sudah 5.000.
 *
 * Tiga hal yang ditambahkan:
 *
 * 1. certificate_number dijadikan unik. Daftar sertifikat adalah daftar yang
 *    dipakai orang untuk memeriksa keaslian, dan satu nomor sertifikat yang
 *    muncul dua kali membuat seluruh daftarnya kehilangan guna. Sebelumnya
 *    tidak ada apa pun yang mencegah admin memasukkan nomor yang sama dua
 *    kali — dan pada 5.000 baris, kekeliruan itu tidak akan pernah terlihat
 *    dengan mata. MySQL mengizinkan banyak NULL dalam indeks unik, jadi nomor
 *    yang memang belum diisi tetap bisa disimpan.
 *
 *    ujk_number sengaja TIDAK dijadikan unik. UJK adalah nomor sesi uji
 *    kompetensi, dan satu sesi diikuti banyak peserta — membuatnya unik akan
 *    menolak data yang justru benar. Ia hanya diberi indeks biasa karena
 *    ikut dicari di halaman publik.
 *
 * 2. Indeks (is_active, sort_order, id) untuk urutan daftarnya. Halaman
 *    publik mengurutkan dengan sort_order lalu id menurun pada setiap
 *    kunjungan; tanpa indeks ini MySQL menyusun ulang seluruh baris aktif
 *    di memori setiap kali, termasuk untuk pengunjung yang hanya membuka
 *    halaman kedua.
 *
 * 3. Indeks pada qualification untuk pintasan pencarian di halaman itu, yang
 *    mengelompokkan seluruh baris aktif menurut kualifikasi lalu mengambil
 *    empat terbanyak.
 *
 * Yang sengaja tidak dikejar: pencarian LIKE '%kata%' tidak bisa memakai
 * indeks B-tree sama sekali, apa pun indeks yang dipasang. Pada 5.000 baris
 * pemindaian penuhnya masih di bawah sepuluh milidetik, jadi menukarnya
 * dengan indeks FULLTEXT — yang punya aturan panjang kata minimum dan daftar
 * kata henti sendiri, dan akan diam-diam berhenti menemukan nama pendek —
 * menambah hal yang bisa rusak tanpa menyelesaikan masalah yang ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_holders', function (Blueprint $table) {
            $table->unique('certificate_number', 'cert_holders_number_unique');
            $table->index('ujk_number', 'cert_holders_ujk_idx');
            $table->index(['is_active', 'sort_order', 'id'], 'cert_holders_listing_idx');
            $table->index('qualification', 'cert_holders_qualification_idx');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_holders', function (Blueprint $table) {
            $table->dropUnique('cert_holders_number_unique');
            $table->dropIndex('cert_holders_ujk_idx');
            $table->dropIndex('cert_holders_listing_idx');
            $table->dropIndex('cert_holders_qualification_idx');
        });
    }
};
