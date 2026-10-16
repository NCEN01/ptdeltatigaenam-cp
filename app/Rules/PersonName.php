<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Nama orang: huruf, spasi, dan beberapa tanda yang memang muncul di nama.
 *
 * Dipakai untuk nama peserta, nama penanggung jawab, dan nama pendaftar —
 * BUKAN untuk nama perusahaan. Nama perusahaan sah mengandung angka: "PT 3M
 * Indonesia", "Bank BTPN Syariah", "Grup 88". Menerapkan aturan ini di sana
 * akan menolak pelanggan yang datanya benar.
 *
 * Yang diizinkan selain huruf:
 *
 *   titik      "Moh. Rizky", dan gelar seperti "S.Kom" yang kerap ikut diketik
 *   apostrof   "Fa'izah", "D'Souza"
 *   hubung     "Ayu-Ningsih"
 *   koma       "Budi Santoso, S.T."
 *
 * Hurufnya dicocokkan dengan \p{L}, bukan a-z: nama Indonesia ditulis dengan
 * huruf Latin, tetapi pelanggan berbahasa lain mengetikkan namanya dengan
 * huruf yang bukan ASCII — menolaknya berarti menolak orangnya.
 *
 * Minimal dua huruf sungguhan supaya isian seperti "." atau "-" tidak lolos
 * hanya karena tidak mengandung angka.
 */
class PersonName implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $nilai = trim((string) $value);

        if ($nilai === '') {
            return; // 'required' yang mengurus kolom kosong, bukan aturan ini.
        }

        if (preg_match('/\d/u', $nilai)) {
            $fail('Kolom :attribute tidak boleh mengandung angka.');

            return;
        }

        if (! preg_match("/^[\p{L}\p{M}\s.'’,-]+$/u", $nilai)) {
            $fail('Kolom :attribute hanya boleh berisi huruf, spasi, dan tanda titik, koma, apostrof, atau hubung.');

            return;
        }

        if (preg_match_all('/\p{L}/u', $nilai) < 2) {
            $fail('Kolom :attribute harus berisi setidaknya dua huruf.');
        }
    }
}
