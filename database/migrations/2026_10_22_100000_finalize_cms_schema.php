<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Penyesuaian skema terakhir sebelum produksi: melepas kolom yang diisi admin
 * tetapi tidak pernah dibaca halaman mana pun, dan menyatukan kolom yang
 * tercatat dua kali di dua tempat.
 *
 * 1. service_schedules.start_date yang rusak diperbaiki LEBIH DULU.
 *    Repeater jadwal di ServiceResource memakai ->orderColumn('start_date').
 *    orderColumn() menunjuk kolom tempat Filament menyimpan NOMOR URUT item
 *    (1, 2, 3…), jadi setiap kali admin menekan Simpan di form Layanan, nomor
 *    urut ditulis ke kolom tanggal dan MySQL menyimpannya sebagai 1970-01-01.
 *    Keempat jadwal di basis data pengembangan rusak persis begitu pada menit
 *    yang sama. Tanggal aslinya tidak tersimpan di mana pun, jadi yang bisa
 *    dilakukan secara umum hanyalah menyamakannya dengan tanggal selesai —
 *    jadwalnya kembali terbaca "akan datang" alih-alih "sudah dimulai sejak
 *    1970", dan admin bisa membetulkan tanggal mulainya sendiri.
 *
 * 2. Lokasi, mode, dan harga khusus dilepas dari jadwal. Ketiganya juga ada
 *    di layanannya, dan di seluruh data tidak ada satu jadwal pun yang isinya
 *    berbeda dari layanannya; harga khusus bahkan tidak pernah terisi. Dua
 *    kolom berisi nilai yang sama hanya menyisakan pertanyaan bagi admin:
 *    yang mana yang dipakai halaman depan.
 *
 * 3. Kuota dan kursi terisi dilepas dari LAYANAN dan dipertahankan di JADWAL.
 *    Kursi memang milik satu angkatan, bukan milik program secara umum — dan
 *    ketika keduanya ada, setiap pembayaran lunas menambah dua penghitung
 *    sekaligus: Order::booted() menambah milik jadwal, sementara webhook
 *    Midtrans dan CheckoutController menambah milik layanan. Halaman program
 *    lalu menampilkan sisa kursi layanan di setiap kartu jadwal, sehingga tiga
 *    angkatan berbeda menunjukkan angka yang sama.
 *
 * 4. Gambar kedua dilepas dari layanan dan artikel. services.banner_image dan
 *    blog_posts.banner_image sama-sama berarti admin mengunggah dua gambar
 *    untuk satu tulisan; banner artikel bahkan tidak dibaca halaman mana pun.
 *    Gambar utamanya kini dipakai untuk kartu sekaligus kepala halaman.
 *
 * 5. Gambar dan deskripsi panjang dilepas dari kategori layanan. Kartu
 *    kategori memakai ikon, dan yang tampil di halaman Layanan hanya
 *    deskripsi singkatnya.
 *
 * 6. Tag blog dihapus seluruhnya; artikel cukup dikelompokkan lewat kategori.
 *
 * 7. blog_posts.author_name ditambahkan: penulis yang tercantum di akhir
 *    artikel, diketik bebas ("Admin", "Dani Ramdani, S.Psi.") — bukan relasi
 *    ke akun admin, karena yang menulis artikel tidak selalu orang yang
 *    memegang akun panelnya.
 *
 * Berkas gambar di storage TIDAK dihapus; hanya rujukannya di tabel.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Perbaikan data dulu, selagi kolom lain masih utuh.
        DB::table('service_schedules')
            ->where('start_date', '<', '2000-01-01')
            ->whereNotNull('end_date')
            ->update(['start_date' => DB::raw('end_date')]);

        // 2.
        Schema::table('service_schedules', function (Blueprint $table) {
            $table->dropColumn(['location', 'mode', 'price_override']);
        });

        // 3 & 4.
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['banner_image', 'quota', 'seats_taken']);
        });

        // 5.
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn(['image', 'description']);
        });

        // 4 & 7.
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('banner_image');
            $table->string('author_name', 120)->nullable()->after('author_id');
        });

        // 6.
        Schema::dropIfExists('blog_post_tag');
        Schema::dropIfExists('blog_tags');
    }

    public function down(): void
    {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        Schema::create('blog_post_tag', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn('author_name');
            $table->string('banner_image')->nullable();
        });

        Schema::table('service_categories', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->longText('description')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('banner_image')->nullable();
            $table->integer('quota')->nullable();
            $table->integer('seats_taken')->default(0);
        });

        Schema::table('service_schedules', function (Blueprint $table) {
            $table->string('location', 200)->nullable();
            $table->enum('mode', ['offline', 'online', 'hybrid'])->nullable();
            $table->decimal('price_override', 15, 2)->nullable();
        });
    }
};
