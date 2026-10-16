<?php

namespace App\Support;

use App\Models\Setting;

class Company
{
    /**
     * Data kontak perusahaan, dari Pengaturan Situs.
     *
     * Satu sumber untuk dua tempat yang sebelumnya menulisnya sendiri-sendiri:
     * kartu kontak di halaman Kontak dan kolom Kontak di footer. Alamat surel
     * dan nomor WhatsApp yang sama diketik ulang di kedua berkas Blade itu —
     * dua salinan yang harus diperbarui bersamaan setiap kali nomornya
     * berganti, tanpa ada yang mengingatkan kalau salah satunya terlewat.
     *
     * Nilai bawaannya tetap ditulis di sini, bukan dibiarkan kosong: situs
     * tidak boleh kehilangan alamat surelnya hanya karena satu baris
     * pengaturan belum pernah diisi.
     *
     * Nomor telepon disimpan dua rupa dengan sengaja. Yang tampil ditulis
     * apa adanya untuk dibaca manusia — "(021) 5890 5002" — sedangkan tautan
     * tel: menuntut bentuk yang hanya angka beserta kode negara. Menurunkan
     * yang kedua dari yang pertama tidak bisa diandalkan: kode wilayah 021
     * harus kehilangan nol depannya dan mendapat +62, aturan yang berbeda
     * untuk nomor seluler dan untuk nomor luar negeri.
     *
     * @return array{email: string, phone: string, phone_tel: string, whatsapp: string, whatsapp_label: string, hours: string}
     */
    public static function contact(): array
    {
        $isId = app()->getLocale() === 'id';

        $jam = Setting::get('site_hours');
        if (is_array($jam)) {
            $jam = $jam[app()->getLocale()] ?? ($jam['id'] ?? '');
        }

        return [
            'email' => trim((string) Setting::get('site_email')) ?: 'info@deltatigaenam.com',
            'phone' => trim((string) Setting::get('site_phone')) ?: '(021) 5890 5002',
            'phone_tel' => trim((string) Setting::get('site_phone_tel')) ?: '+622158905002',
            'whatsapp' => trim((string) Setting::get('site_whatsapp')) ?: '62818834766',
            'whatsapp_label' => trim((string) Setting::get('site_whatsapp_label')) ?: '0818 834 766',
            'hours' => trim((string) $jam) ?: ($isId ? 'Senin sampai Jumat · 08.00-17.00' : 'Mon to Fri · 8am-5pm'),
        ];
    }

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

        /* Dibaca dari tabel offices, yang bisa disunting admin lewat
           Pengaturan → Alamat Kantor, paling banyak tiga baris.

           config/company.php tetap dipakai sebagai cadangan ketika tabelnya
           kosong — pada pemasangan baru yang penyemainya belum dijalankan.
           Tanpa cadangan itu, footer kehilangan seluruh kolom alamatnya dan
           halaman Kontak kehilangan peta sematannya, dan yang tampak bukan
           "belum diisi" melainkan halaman yang rusak.

           Bentuk keluarannya sengaja sama persis dengan yang dulu dibaca dari
           config: array biasa dengan nama yang sudah dipilih menurut bahasa.
           Pemakainya di footer dan halaman Kontak tidak perlu diubah
           sedikit pun — dan halaman Kontak khususnya menyisipkan seluruh array
           ini ke atribut x-data Alpine, jadi nama yang tetap berupa array akan
           terbaca "[object Object]" di judul petanya. */
        $dariDb = \App\Models\Office::displayable()->get();

        if ($dariDb->isNotEmpty()) {
            return $dariDb->map(static fn ($o): array => [
                'name' => $o->getTranslation('name', $locale, true) ?: $o->name,
                'address' => (string) $o->address,
                'phone' => (string) $o->phone,
                'tel' => (string) $o->tel,
                'maps' => (string) ($o->maps ?: $o->address),
            ])->all();
        }

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
