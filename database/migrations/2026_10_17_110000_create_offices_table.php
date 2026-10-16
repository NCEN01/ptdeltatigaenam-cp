<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alamat kantor, yang sebelumnya ditulis di config/company.php.
 *
 * Berkas config bukan tempat untuk data yang berubah: memindahkan kantor atau
 * mengganti nomor teleponnya menuntut akses ke peladen dan satu kali
 * `config:clear`, sementara yang mengetahui alamat barunya adalah orang yang
 * tidak pernah membuka editor kode.
 *
 * name bertipe text karena isinya JSON dwibahasa milik Spatie Translatable —
 * "Lokasi Kantor" dan "Head Office" adalah sebutan yang berbeda, bukan
 * terjemahan harfiah, dan materi resmi perusahaan memakai istilah Inggris.
 *
 * phone dan tel dipisah dengan sengaja: yang pertama untuk dibaca manusia
 * ("(021) 5890 5002"), yang kedua untuk tautan tel: yang menuntut bentuk
 * hanya-angka berkode negara. Menurunkan yang kedua dari yang pertama tidak
 * bisa diandalkan — kode wilayah 021 harus kehilangan nol depannya dan
 * mendapat +62, aturan yang berbeda untuk nomor seluler.
 *
 * maps bukan alamat tampilan melainkan kata kunci pencarian untuk peta
 * sematan di halaman Kontak; ia sengaja lebih pendek dan tanpa tanda pisah
 * supaya Google Maps menemukannya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('address');
            $table->string('phone', 50)->nullable();
            $table->string('tel', 30)->nullable();
            $table->text('maps')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
