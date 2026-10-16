<?php

namespace App\Support;

/**
 * Ikon kategori layanan: satu sumber untuk gambarnya sekaligus daftar
 * namanya.
 *
 * Sebelumnya lariknya hidup di dalam <x-category-icon>. Begitu panel admin
 * perlu menawarkan pilihan ikon, daftar namanya harus ada di PHP — dan
 * menyalinnya ke sana berarti dua daftar yang pasti menyimpang: ikon baru
 * ditambahkan di komponen, lupa ditambahkan di CMS, atau sebaliknya CMS
 * menawarkan nama yang tidak ada gambarnya dan tampil sebagai ikon cadangan
 * tanpa ada yang tahu kenapa.
 *
 * Kuncinya bernama makna, bukan bentuk ("certificate", bukan "medal"):
 * admin memilih berdasarkan isi kategorinya, dan bentuk gambarnya boleh
 * berubah tanpa membuat namanya jadi bohong.
 */
class CategoryIcons
{
    /** Nama ikon => goresan SVG-nya (tanpa elemen <svg> pembungkusnya). */
    public static function paths(): array
    {
        return [
        // Bagan organisasi — konsultasi manajemen
        'structure' => '<rect x="9.5" y="3" width="5" height="4.5" rx="1"/><rect x="3" y="16.5" width="5" height="4.5" rx="1"/><rect x="16" y="16.5" width="5" height="4.5" rx="1"/><path d="M12 7.5v4M5.5 16.5v-2a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v2"/>',
        // Tiga orang — human capital
        'people' => '<circle cx="9" cy="8" r="3"/><path d="M3.5 19.5a5.5 5.5 0 0 1 11 0"/><circle cx="17" cy="9.5" r="2.25"/><path d="M15.2 14.6a4.5 4.5 0 0 1 5.3 4.9"/>',
        // Orang dalam lensa — headhunter
        'search-user' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M19.5 19.5 15.2 15.2"/><circle cx="10.5" cy="9" r="1.9"/><path d="M7.6 14.4a3.4 3.4 0 0 1 5.8 0"/>',
        // Papan presentasi — pelatihan
        'presentation' => '<rect x="3" y="4" width="18" height="11.5" rx="1.5"/><path d="M12 15.5V20M8.5 20h7M7.5 11.8l2.6-2.9 2.2 2 3.2-3.6"/>',
        // Lencana bersertifikat — sertifikasi kompetensi
        'certificate' => '<circle cx="12" cy="9.5" r="5.5"/><path d="m9.2 14.2-1.1 6 3.9-2 3.9 2-1.1-6"/><path d="m9.9 9.4 1.5 1.5 2.9-3"/>',
        // Cadangan umum, dipakai bila kategori baru belum dipilihkan ikonnya
        'briefcase' => '<rect x="3" y="7.5" width="18" height="12" rx="1.8"/><path d="M9 7.5V6a1.8 1.8 0 0 1 1.8-1.8h2.4A1.8 1.8 0 0 1 15 6v1.5M3 12.5h18"/>',
        'chart' => '<path d="M4 20V4"/><path d="M4 20h16"/><path d="m7.5 15.5 3.5-4 3 2.5 4.5-6"/>',
        'shield' => '<path d="M12 3.2 5 6v5.5c0 4.2 2.8 7.6 7 9.3 4.2-1.7 7-5.1 7-9.3V6z"/><path d="m9.4 11.9 1.9 1.9 3.6-3.8"/>',
        'target' => '<circle cx="12" cy="12" r="8.2"/><circle cx="12" cy="12" r="4.4"/><circle cx="12" cy="12" r="0.9"/>',
        'book' => '<path d="M5 4.5h9.5a2.5 2.5 0 0 1 2.5 2.5v12.5H7.5A2.5 2.5 0 0 1 5 17z"/><path d="M19 4.5v15M7.5 19.5H19"/>',
        ];
    }

    /** Nama yang tersedia, untuk daftar pilihan di panel admin. */
    public static function names(): array
    {
        return array_keys(static::paths());
    }

    /** Goresan satu ikon; jatuh ke cadangan bila namanya tidak dikenal. */
    public static function path(?string $name): string
    {
        $paths = static::paths();

        return $paths[$name] ?? $paths["briefcase"];
    }
}
