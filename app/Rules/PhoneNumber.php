<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nomor telepon: angka beserta tanda pemisah yang lazim dipakai orang.
 *
 * Pasangan dari App\Rules\PersonName. Kalau kolom nama tidak boleh berangka,
 * kolom nomor tidak boleh berhuruf — dan sebelum aturan ini ada, lima dari
 * enam kolom telepon di situs ini menerima string apa pun sepanjang lima
 * puluh karakter, termasuk kalimat utuh.
 *
 * Yang diizinkan selain angka: tanda plus untuk kode negara, spasi, tanda
 * hubung, titik, dan tanda kurung untuk kode wilayah. Orang mengetikkan
 * nomornya dengan cara yang berbeda-beda — "0818 834 766", "+62 818-834-766",
 * "(021) 5890 5002" — dan ketiganya nomor yang sah.
 *
 * Tanda plus hanya boleh di awal: "62+818" bukan nomor.
 *
 * Minimal tujuh angka. Nomor Indonesia terpendek yang bisa dihubungi dari
 * luar jaringan lokal ada di kisaran itu, dan ambang ini yang menolak isian
 * seperti "0" atau "123" tanpa ikut menolak nomor tetap berkode wilayah.
 *
 * Panjang maksimum lima belas angka mengikuti E.164, standar yang dipakai
 * seluruh jaringan telepon dunia — lebih dari itu bukan nomor yang bisa
 * dirutekan ke mana pun.
 */
class PhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nilai = trim((string) $value);

        if ($nilai === '') {
            return; // 'required' yang mengurus kolom kosong.
        }

        if (! preg_match('/^\+?[0-9 ().-]+$/', $nilai)) {
            $fail('Kolom :attribute hanya boleh berisi angka, spasi, dan tanda + ( ) - yang lazim pada nomor telepon.');

            return;
        }

        $angka = preg_replace('/\D/', '', $nilai);

        if (strlen($angka) < 7) {
            $fail('Kolom :attribute terlalu pendek untuk sebuah nomor telepon.');

            return;
        }

        if (strlen($angka) > 15) {
            $fail('Kolom :attribute terlalu panjang. Nomor telepon paling banyak 15 angka.');
        }
    }
}
