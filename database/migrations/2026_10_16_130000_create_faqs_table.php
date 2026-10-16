<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tanya-jawab di beranda, yang sebelumnya ditulis langsung di
 * resources/views/pages/home.blade.php sebagai array PHP.
 *
 * Keenamnya adalah pertanyaan yang paling sering benar-benar ditanyakan calon
 * peserta, dan jawabannya ikut berubah setiap kali ketentuan program berubah —
 * durasi pelatihan, cara mendaftar, lembaga yang menerbitkan sertifikat. Isi
 * seperti itu tidak punya tempat di berkas Blade: mengubah satu kalimatnya
 * menuntut akses ke kode dan satu kali bangun ulang aset, padahal yang
 * mengetahui kalimat barunya adalah orang yang tidak pernah membuka editor.
 *
 * Kolom pertanyaan dan jawaban bertipe text, bukan varchar: isinya JSON
 * dwibahasa milik Spatie Translatable, dan satu jawaban panjang dalam dua
 * bahasa beserta tanda kutip JSON-nya sudah melewati 255 karakter.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');

            /* Urutannya ditentukan admin, bukan waktu pembuatan: pertanyaan
               yang paling sering ditanyakan harus bisa dipindah ke atas tanpa
               perlu dihapus dan diketik ulang. Diberi indeks karena setiap
               kunjungan beranda mengurutkan tabel ini. */
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
