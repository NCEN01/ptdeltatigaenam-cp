<?php

namespace App\Support;

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
     * Nilai perusahaan dari config/company.php, dengan keterangan yang sudah
     * dipilih menurut bahasa yang sedang aktif.
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
            if (is_array($value['desc'] ?? null)) {
                $value['desc'] = $value['desc'][$locale] ?? reset($value['desc']);
            }

            return $value;
        }, config('company.values', []));
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
