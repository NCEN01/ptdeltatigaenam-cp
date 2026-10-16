<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Menolak karakter kendali di alamat surel — penangkal injeksi header surat.
 *
 * Dipasang menanggapi GHSA-5vg9-5847-vvmq (keparahan HIGH) pada aturan 'email'
 * bawaan Laravel. Advisory itu menyebut SELURUH jalur Laravel 11 terdampak
 * tanpa rilis tambalan — perbaikan resminya baru ada di 12.60 — sementara
 * proyek ini di 11.57 dan naik ke Laravel 12 adalah keputusan tersendiri yang
 * tidak diambil diam-diam menjelang produksi.
 *
 * Satu catatan yang harus jujur: kerentanannya TIDAK berhasil direproduksi di
 * 11.57. Enam varian CRLF dicoba — di ujung, di awal, di tengah, beserta
 * bentuk terkutipnya — dan aturan bawaan menolak keenamnya. Jadi aturan ini
 * bukan menambal lubang yang terbukti menganga di sini, melainkan menjamin
 * sifat yang dibutuhkan apa pun yang dilakukan pengurai di bawahnya, termasuk
 * ketika versinya berganti.
 *
 * Mengapa sifat itu dibutuhkan secara khusus di proyek ini: alamat yang lolos
 * validasi dipakai langsung sebagai tujuan surat, misalnya
 *
 *     Notification::route('mail', $order->customer_email)
 *
 * Header surat dipisahkan oleh CRLF. Alamat yang menyelipkan CRLF di
 * tengahnya bisa menambahkan header baru — Bcc ke alamat penyerang, Reply-To
 * palsu, bahkan badan surat kedua — dan peladen surat akan mengirimkannya
 * sebagai surat yang sah atas nama domain ini.
 *
 * Yang ditolak bukan hanya CR dan LF melainkan seluruh karakter kendali C0
 * beserta DEL. Tidak satu pun punya tempat di alamat surel, dan menolak
 * seluruh kelompoknya lebih kokoh daripada mendaftar satu per satu yang
 * kebetulan sudah diketahui berbahaya.
 */
class SafeEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return; // 'required' dan 'email' yang mengurus keduanya.
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $value)) {
            $fail('Kolom :attribute mengandung karakter yang tidak diizinkan.');
        }
    }
}
