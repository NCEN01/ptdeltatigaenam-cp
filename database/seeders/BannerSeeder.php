<?php

namespace Database\Seeders;

use App\Models\Banner;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;

/**
 * Banner hero beranda dan gambar latar kepala tiap halaman.
 *
 * Komponen x-page-header mencari Banner dengan placement yang cocok; bila tidak
 * ada, ia jatuh ke gambar Unsplash bawaan. Baris-baris ini mengembalikan gambar
 * yang dulu benar-benar diunggah admin, yang berkasnya masih ada di disk.
 */
class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $images = StoredImages::pool('banners', 'services', 'service-categories', 'agenda');

        foreach ($this->banners() as $index => $banner) {
            Banner::updateOrCreate(
                ['placement' => $banner['placement'], 'sort_order' => $banner['sort_order']],
                [
                    'title' => $banner['title'],
                    'subtitle' => $banner['subtitle'],
                    'image' => StoredImages::pick($images, $index),
                    'button_text' => $banner['button_text'],
                    'link_url' => $banner['link_url'],
                    'is_active' => true,
                    // Dibiarkan null: activeNow() memperlakukan banner tanpa
                    // tanggal sebagai selalu tayang, jadi tidak ada yang tiba-tiba
                    // hilang karena jendela waktunya lewat.
                    'starts_at' => null,
                    'ends_at' => null,
                ],
            );
        }

        $this->command?->info('Banner siap: '.Banner::count().' banner ('
            .Banner::where('placement', 'home_hero')->count().' hero beranda).');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function banners(): array
    {
        return [
            [
                'placement' => 'home_hero',
                'sort_order' => 1,
                'title' => [
                    'id' => 'Membangun kompetensi yang terbukti di tempat kerja',
                    'en' => 'Building competency that proves itself at work',
                ],
                'subtitle' => [
                    'id' => 'Pelatihan, sertifikasi, dan konsultasi human capital yang disusun dari kebutuhan nyata organisasi Anda.',
                    'en' => 'Training, certification, and human capital consulting built from your organisation\'s real needs.',
                ],
                'button_text' => ['id' => 'Lihat Layanan Kami', 'en' => 'View Our Services'],
                'link_url' => '/layanan',
            ],
            [
                'placement' => 'home_hero',
                'sort_order' => 2,
                'title' => [
                    'id' => 'Sertifikasi BNSP yang diakui untuk audit dan tender',
                    'en' => 'BNSP certification recognised for audits and tenders',
                ],
                'subtitle' => [
                    'id' => 'Uji kompetensi dapat digelar langsung di lokasi kerja Anda, mengikuti pola sif, tanpa menghentikan produksi.',
                    'en' => 'Assessment can run right at your workplace, around your shift pattern, without stopping production.',
                ],
                'button_text' => ['id' => 'Jadwal Sertifikasi', 'en' => 'Certification Schedule'],
                'link_url' => '/agenda',
            ],
            [
                'placement' => 'home_hero',
                'sort_order' => 3,
                'title' => [
                    'id' => 'Mitra jangka panjang untuk pengembangan karyawan',
                    'en' => 'A long-term partner for employee development',
                ],
                'subtitle' => [
                    'id' => 'Program kemitraan tahunan mencakup pelatihan, asesmen, dan pendampingan konsultan dalam satu kerangka.',
                    'en' => 'Annual partnership programmes covering training, assessment, and consultant support in one framework.',
                ],
                'button_text' => ['id' => 'Pelajari Kemitraan', 'en' => 'Explore Partnership'],
                'link_url' => '/kemitraan',
            ],

            // Latar kepala halaman. Judul & subjudul dibiarkan kosong supaya
            // teks bawaan tiap halaman tetap dipakai — banner ini hanya
            // menyediakan gambarnya.
            ['placement' => 'about', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'services', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'portfolio', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'blog', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'agenda', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'partnership', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'certificate', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
            ['placement' => 'contact', 'sort_order' => 1, 'title' => null, 'subtitle' => null, 'button_text' => null, 'link_url' => null],
        ];
    }
}
