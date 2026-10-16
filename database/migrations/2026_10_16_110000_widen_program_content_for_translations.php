<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Melebarkan kolom isi program agar muat dua bahasa
|--------------------------------------------------------------------------
|
| Situs ini dwibahasa, dan Spatie Translatable menyimpan kedua bahasa
| sebagai satu JSON di dalam kolomnya: {"id":"…","en":"…"}. Kolom yang
| dibuat varchar(200) di migrasi sebelumnya menampung satu kalimat
| Indonesia dengan pas, tapi tidak dua kalimat beserta tanda kurung
| kurawal dan nama kuncinya.
|
| Disamakan dengan partnership_benefits, yang memang sudah longtext
| untuk alasan yang sama.
|
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_benefits', function (Blueprint $table) {
            $table->text('title')->change();
        });

        Schema::table('mentors', function (Blueprint $table) {
            $table->text('role')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('program_benefits', function (Blueprint $table) {
            $table->string('title', 200)->change();
        });

        Schema::table('mentors', function (Blueprint $table) {
            $table->string('role', 150)->nullable()->change();
        });
    }
};
