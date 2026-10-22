<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = ['id'];

    /** Satu kunci cache untuk seluruh tabel. Lihat alasannya di semua(). */
    private const CACHE_KEY = 'settings:all';

    /**
     * Seluruh baris pengaturan, dipetakan menurut kuncinya.
     *
     * Sebelumnya tiap kunci punya entri cache sendiri lewat
     * Cache::rememberForever("setting:{$key}", …), dan di situ ada lubang yang
     * tidak terlihat: rememberForever TIDAK menyimpan hasil null. Artinya
     * setiap kunci yang belum pernah diisi admin di-query ulang ke basis data
     * pada SETIAP permintaan, selamanya — cache-nya tidak pernah terisi karena
     * tidak ada yang bisa disimpan.
     *
     * Bilah promo saja membaca dua belas kunci, dan halaman yang belum
     * mengisinya membayar dua belas query untuk dua belas kali "tidak ada".
     * Terukur di halaman Kontak: tiga belas query, delapan di antaranya
     * pencarian kunci kosong yang diulang di halaman dan di footer.
     *
     * Satu entri untuk seluruh tabel menutup lubang itu sekaligus: ketiadaan
     * sebuah kunci ikut tersimpan, karena yang disimpan adalah petanya, bukan
     * barisnya. Tabel ini puluhan baris, jadi memuatnya sekaligus lebih murah
     * daripada satu pencarian terpisah.
     */
    private static function semua(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::all()->keyBy('key'));
    }

    /**
     * Get a setting value by key. JSON-typed values are decoded.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::semua()->get($key);

        if (! $setting) {
            return $default;
        }

        if ($setting->type === 'json') {
            // Baris JSON boleh berisi null — terjemahan yang seluruhnya kosong
            // disimpan begitu oleh halaman pengaturan.
            return $setting->value === null ? $default : (json_decode($setting->value, true) ?? $default);
        }

        return $setting->value ?? $default;
    }

    /**
     * Get a localized setting value ({"id":..,"en":..}) for the current locale.
     */
    public static function getLocalized(string $key, ?string $locale = null, mixed $default = null): mixed
    {
        $value = static::get($key);

        if (is_array($value)) {
            $locale = $locale ?? app()->getLocale();

            return $value[$locale] ?? $value['id'] ?? $default;
        }

        return $value ?? $default;
    }

    /**
     * Satu baris berubah berarti seluruh peta dibuang.
     *
     * Terasa boros, tetapi tidak: yang dibangun ulang hanya satu query untuk
     * seluruh tabel, dan pengaturan disimpan beberapa kali sehari sementara
     * dibaca ribuan kali. Membuang per kunci juga tidak mungkin lagi — yang
     * tersimpan sekarang petanya, bukan barisnya.
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
