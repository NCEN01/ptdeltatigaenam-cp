<?php

namespace App\Support;

use App\Models\Setting;

class Company
{
    /**
     * Daftar kantor dari config/company.php, dengan nama yang sudah dipilih
     * menurut bahasa yang sedang aktif.
     *
     * Namanya disimpan dwibahasa di config, tapi dikembalikan di sini sebagai
     * teks biasa. Dengan begitu pemakainya tidak berubah sama sekali: footer
     * mencetaknya langsung, dan halaman kontak menyisipkan seluruh array ini
     * ke atribut x-data Alpine — kalau namanya tetap berupa array, judul peta
     * sematannya akan terbaca "[object Object]".
     *
     * Satu tempat, bukan tiga: sebelum ada ini, ContactController,
     * PageController, dan footer sama-sama memanggil config() langsung,
     * sehingga penyesuaian seperti pemilihan bahasa ini harus ditulis tiga
     * kali dan bisa terlewat di salah satunya.
     *
     * @return array<int, array<string, string>>
     */
    /**
     * Empat angka statistik perusahaan, dari Pengaturan Situs.
     *
     * Satu sumber untuk dua halaman: papan angka di bawah Kategori Layanan di
     * beranda, dan papan angka di halaman Tentang Kami. Sebelumnya beranda
     * membaca hero_stat_1..3 dan Tentang membaca about_stat_1..4 — dua set
     * terpisah berisi angka yang sama tentang perusahaan yang sama, yang akan
     * saling bertentangan begitu salah satunya diperbarui sendirian.
     *
     * Kuncinya tetap bernama about_stat_* karena itu yang sudah tersimpan di
     * basis data berikut isinya yang benar; menggantinya menuntut penulisan
     * ulang baris pengaturan tanpa menambah apa pun bagi pembacanya.
     *
     * Angka yang kosong dilewati, jadi admin yang mengosongkan satu kolom
     * mendapat tiga angka rapi, bukan satu lubang.
     *
     * @return array<int, array{value: string, label: string, number: int, suffix: string}>
     */
    public static function stats(): array
    {
        $out = [];

        for ($i = 1; $i <= 4; $i++) {
            $value = trim((string) Setting::get("about_stat_{$i}_value", ''));
            $label = Setting::getLocalized("about_stat_{$i}_label");

            if ($value === '' || ! $label) {
                continue;
            }

            // Satuannya dipisah dari angkanya supaya tampilan bisa mewarnai
            // keduanya berbeda, dan supaya penghitung animasinya hanya dipasang
            // pada angkanya — JS-nya menimpa seluruh isi elemen yang dipasangi.
            $number = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);

            $out[] = [
                'value' => $value,
                'label' => $label,
                'number' => $number,
                'suffix' => str_replace((string) $number, '', $value),
            ];
        }

        return $out;
    }

    /**
     * Nilai perusahaan dari config/company.php, dengan kedua keterangannya
     * sudah dipilih menurut bahasa yang sedang aktif.
     *
     * 'desc' adalah kalimat pernyataan untuk beranda; 'detail' penjelasan yang
     * lebih dalam untuk halaman Tentang Kami. Keduanya dikembalikan bersama,
     * dan tiap halaman memilih yang dipakainya — pemisahan itu urusan tampilan,
     * bukan urusan data.
     *
     * Namanya sengaja tidak ikut diterjemahkan: huruf depan kelimanya mengeja
     * DELTA, dan terjemahan apa pun menghapus akronim itu.
     *
     * @return array<int, array<string, string>>
     */
    public static function values(): array
    {
        $locale = app()->getLocale();

        return array_map(static function (array $value) use ($locale): array {
            foreach (['desc', 'detail'] as $key) {
                if (\is_array($value[$key] ?? null)) {
                    $value[$key] = $value[$key][$locale] ?? reset($value[$key]);
                }
            }

            return $value;
        }, config('company.values', []));
    }

    /**
     * Nilai perusahaan untuk halaman Tentang.
     *
     * Daftar tersendiri, bukan values() yang sama: values() adalah akronim
     * DELTA yang sudah dipakai beranda, dan menampilkannya lagi di halaman
     * Tentang membuat lima judul yang sama terbaca dua kali dalam satu
     * kunjungan. Judulnya di sini ikut bahasa, karena ia kalimat Indonesia,
     * bukan satu kata Inggris yang harus membentuk akronim.
     */
    public static function aboutValues(): array
    {
        $locale = app()->getLocale();

        return array_map(static function (array $value) use ($locale): array {
            foreach (['title', 'detail'] as $key) {
                if (\is_array($value[$key] ?? null)) {
                    $value[$key] = $value[$key][$locale] ?? reset($value[$key]);
                }
            }

            return $value;
        }, config('company.about_values', []));
    }

    public static function offices(): array
    {
        $locale = app()->getLocale();

        return array_map(static function (array $office) use ($locale): array {
            if (is_array($office['name'] ?? null)) {
                // reset() sebagai cadangan: bahasa baru yang belum punya
                // terjemahan tetap menampilkan nama, bukan kolom kosong.
                $office['name'] = $office['name'][$locale] ?? reset($office['name']);
            }

            return $office;
        }, config('company.offices', []));
    }
}
