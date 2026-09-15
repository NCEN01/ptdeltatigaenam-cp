<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Anchor tujuan setelah mencari atau berpindah halaman.
     *
     * Pagination dan form pencarian memuat ulang halaman penuh, jadi browser
     * selalu mendarat di puncak dokumen dan pembaca kehilangan tempatnya.
     * Paginator memakai ->fragment(self::RESULTS_ANCHOR) dan seksi hasil di
     * view memakai id yang sama, dibaca dari konstanta ini supaya keduanya
     * tidak bisa berbeda. Kalau berbeda, fragmennya diam-diam tidak menuju ke
     * mana pun dan tidak ada galat apa pun yang muncul.
     */
    public const RESULTS_ANCHOR = 'hasil';

    /**
     * Menyiapkan kata kunci untuk dibandingkan dengan LOWER(kolom) LIKE ?.
     *
     * Dua hal dikerjakan sekaligus. Pertama, wildcard SQL di dalam kata kunci
     * pengguna dilarikan supaya "50%" dicari sebagai teks, bukan sebagai pola.
     * Kedua, hasilnya dihuruf-kecilkan: kolom terjemahan di MariaDB bertipe
     * LONGTEXT bercollation utf8mb4_bin yang peka huruf besar-kecil, sehingga
     * LIKE biasa membuat "supervisor" tidak menemukan "Supervisor".
     *
     * Pasangannya di query harus LOWER(kolom), bukan kolom apa adanya.
     */
    protected function likeNeedle(string $term): string
    {
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);

        return '%'.mb_strtolower($escaped).'%';
    }
}
