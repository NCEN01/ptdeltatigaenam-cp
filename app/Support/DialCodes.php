<?php

namespace App\Support;

/**
 * Kode panggil negara untuk pemilih nomor telepon.
 *
 * Indonesia didahulukan dan menjadi pilihan bawaan: hampir seluruh pendaftar
 * situs ini berada di Indonesia, dan memaksa mereka menggulir daftar menurut
 * abjad untuk menemukan negaranya sendiri adalah gesekan yang ditanggung
 * mayoritas demi segelintir.
 *
 * Setelah itu enam tetangga ASEAN yang paling sering muncul di pelatihan
 * lintas negara, lalu sisanya menurut abjad.
 *
 * Daftarnya memang tidak memuat setiap wilayah di dunia. Yang dimuat adalah
 * negara yang realistis muncul di formulir perusahaan pelatihan Indonesia —
 * dan setiap barisnya adalah kode yang bisa diperiksa, bukan tebakan.
 * Menambah negara cukup satu baris; urutannya di luar tujuh pertama mengikuti
 * abjad sendiri lewat sortir di bawah.
 */
class DialCodes
{
    /** Kode negara bawaan ketika pengunjung belum memilih apa pun. */
    public const DEFAULT = '+62';

    /** Didahulukan di puncak daftar, menurut urutan di sini. */
    private const UTAMA = ['ID', 'MY', 'SG', 'TH', 'VN', 'PH', 'BN'];

    /** @var array<string, array{0: string, 1: string}> ISO => [nama, kode panggil] */
    private const NEGARA = [
        'ID' => ['Indonesia', '+62'],
        'MY' => ['Malaysia', '+60'],
        'SG' => ['Singapura', '+65'],
        'TH' => ['Thailand', '+66'],
        'VN' => ['Vietnam', '+84'],
        'PH' => ['Filipina', '+63'],
        'BN' => ['Brunei Darussalam', '+673'],

        'AE' => ['Uni Emirat Arab', '+971'],
        'AR' => ['Argentina', '+54'],
        'AT' => ['Austria', '+43'],
        'AU' => ['Australia', '+61'],
        'BD' => ['Bangladesh', '+880'],
        'BE' => ['Belgia', '+32'],
        'BH' => ['Bahrain', '+973'],
        'BR' => ['Brasil', '+55'],
        'CA' => ['Kanada', '+1'],
        'CH' => ['Swiss', '+41'],
        'CL' => ['Cile', '+56'],
        'CN' => ['Tiongkok', '+86'],
        'CZ' => ['Ceko', '+420'],
        'DE' => ['Jerman', '+49'],
        'DK' => ['Denmark', '+45'],
        'EG' => ['Mesir', '+20'],
        'ES' => ['Spanyol', '+34'],
        'FI' => ['Finlandia', '+358'],
        'FR' => ['Prancis', '+33'],
        'GB' => ['Inggris Raya', '+44'],
        'GR' => ['Yunani', '+30'],
        'HK' => ['Hong Kong', '+852'],
        'IE' => ['Irlandia', '+353'],
        'IL' => ['Israel', '+972'],
        'IN' => ['India', '+91'],
        'IQ' => ['Irak', '+964'],
        'IR' => ['Iran', '+98'],
        'IT' => ['Italia', '+39'],
        'JP' => ['Jepang', '+81'],
        'KH' => ['Kamboja', '+855'],
        'KR' => ['Korea Selatan', '+82'],
        'KW' => ['Kuwait', '+965'],
        'LA' => ['Laos', '+856'],
        'LK' => ['Sri Lanka', '+94'],
        'MA' => ['Maroko', '+212'],
        'MM' => ['Myanmar', '+95'],
        'MO' => ['Makau', '+853'],
        'MX' => ['Meksiko', '+52'],
        'NG' => ['Nigeria', '+234'],
        'NL' => ['Belanda', '+31'],
        'NO' => ['Norwegia', '+47'],
        'NP' => ['Nepal', '+977'],
        'NZ' => ['Selandia Baru', '+64'],
        'OM' => ['Oman', '+968'],
        'PK' => ['Pakistan', '+92'],
        'PL' => ['Polandia', '+48'],
        'PT' => ['Portugal', '+351'],
        'QA' => ['Qatar', '+974'],
        'RU' => ['Rusia', '+7'],
        'SA' => ['Arab Saudi', '+966'],
        'SE' => ['Swedia', '+46'],
        'TL' => ['Timor Leste', '+670'],
        'TR' => ['Turki', '+90'],
        'TW' => ['Taiwan', '+886'],
        'UA' => ['Ukraina', '+380'],
        'US' => ['Amerika Serikat', '+1'],
        'ZA' => ['Afrika Selatan', '+27'],
    ];

    /**
     * Daftar siap pakai untuk <select>, negara utama di puncak.
     *
     * @return array<int, array{iso: string, nama: string, kode: string, utama: bool}>
     */
    public static function all(): array
    {
        $utama = [];
        $sisa = [];

        foreach (self::NEGARA as $iso => [$nama, $kode]) {
            $baris = ['iso' => $iso, 'nama' => $nama, 'kode' => $kode, 'utama' => in_array($iso, self::UTAMA, true)];

            if ($baris['utama']) {
                $utama[array_search($iso, self::UTAMA, true)] = $baris;
            } else {
                $sisa[] = $baris;
            }
        }

        ksort($utama);
        usort($sisa, fn ($a, $b) => strcmp($a['nama'], $b['nama']));

        return array_merge(array_values($utama), $sisa);
    }

    /**
     * Memecah nomor tersimpan menjadi kode negara dan sisanya.
     *
     * Dibutuhkan saat formulir dimuat ulang setelah validasi gagal: yang
     * tersimpan di old() adalah nomor utuh "+62 818834766", sedangkan
     * formulirnya punya dua kolom terpisah. Tanpa pemecahan ini, pendaftar
     * yang salah mengisi kolom lain mendapati nomornya menumpuk jadi
     * "+62 +62 818834766" setiap kali formulirnya ditolak.
     *
     * Kode terpanjang dicocokkan lebih dulu: +673 harus menang atas +67x mana
     * pun, dan +62 atas +6.
     *
     * @return array{0: string, 1: string} [kode negara, sisa nomor]
     */
    public static function split(?string $nomor): array
    {
        $nomor = trim((string) $nomor);

        if ($nomor === '') {
            return [self::DEFAULT, ''];
        }

        if (! str_starts_with($nomor, '+')) {
            return [self::DEFAULT, $nomor];
        }

        $kode = array_column(self::all(), 'kode');
        usort($kode, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($kode as $k) {
            if (str_starts_with($nomor, $k)) {
                return [$k, trim(substr($nomor, strlen($k)))];
            }
        }

        return [self::DEFAULT, $nomor];
    }
}
