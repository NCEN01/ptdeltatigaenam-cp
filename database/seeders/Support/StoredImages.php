<?php

namespace Database\Seeders\Support;

/**
 * Memungut kembali berkas gambar yang sudah ada di storage/app/public.
 *
 * Berkasnya selamat ketika isi database terhapus, jadi seeder memakainya lagi
 * alih-alih menarik foto dari internet: situs tetap hidup tanpa koneksi, dan
 * gambar yang dulu diunggah admin kembali terpakai.
 *
 * MediaService menyimpan satu berkas induk plus varian responsif bernama
 * `<nama>-480.webp`, `-768`, `-1200`, `-1280`. Hanya nama induk yang masuk
 * kolom database — varian dirakit ulang oleh MediaService::srcset().
 */
class StoredImages
{
    /** Varian responsif yang harus diabaikan saat memindai folder. */
    private const VARIANT_SUFFIX = '/-\d{3,4}\.(webp|jpe?g|png)$/i';

    private const EXTENSIONS = ['webp', 'jpg', 'jpeg', 'png'];

    /** @var array<string, list<string>> */
    private static array $cache = [];

    /**
     * Daftar berkas induk di satu folder, relatif terhadap disk `public`.
     *
     * @return list<string>
     */
    public static function in(string $directory): array
    {
        $directory = trim($directory, '/');

        if (isset(self::$cache[$directory])) {
            return self::$cache[$directory];
        }

        $absolute = storage_path('app/public/'.$directory);

        if (! is_dir($absolute)) {
            return self::$cache[$directory] = [];
        }

        $files = [];

        foreach (scandir($absolute) ?: [] as $name) {
            if ($name === '.' || $name === '..' || is_dir($absolute.'/'.$name)) {
                continue;
            }

            if (preg_match(self::VARIANT_SUFFIX, $name)) {
                continue;
            }

            if (! in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), self::EXTENSIONS, true)) {
                continue;
            }

            $files[] = $directory.'/'.$name;
        }

        // Diurutkan supaya seeder yang dijalankan ulang memberi hasil yang sama.
        sort($files);

        return self::$cache[$directory] = $files;
    }

    /**
     * Gabungan beberapa folder, dipakai saat satu folder tidak cukup banyak
     * untuk memberi variasi (mis. 20 portofolio tapi cuma ada 1 sampul lama).
     *
     * @return list<string>
     */
    public static function pool(string ...$directories): array
    {
        return array_values(array_unique(array_merge(
            ...array_map(self::in(...), $directories),
        )));
    }

    /**
     * Ambil berkas ke-$index secara berputar. Deterministik: indeks yang sama
     * selalu menghasilkan berkas yang sama, jadi seeder aman dijalankan ulang.
     */
    public static function pick(array $pool, int $index): ?string
    {
        if ($pool === []) {
            return null;
        }

        return $pool[$index % count($pool)];
    }

    /**
     * Ambil $count berkas berurutan mulai dari $offset, tanpa pengulangan
     * selama jumlah yang diminta tidak melebihi isi kolam.
     *
     * @return list<string>
     */
    public static function take(array $pool, int $offset, int $count): array
    {
        if ($pool === [] || $count < 1) {
            return [];
        }

        $picked = [];

        for ($i = 0; $i < $count; $i++) {
            $picked[] = $pool[($offset + $i) % count($pool)];
        }

        return $picked;
    }
}
