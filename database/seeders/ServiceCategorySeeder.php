<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Berkas gambar kategori selamat di disk ketika isi database terhapus,
        // jadi dipasangkan kembali di sini. Tanpa kolom image terisi, carousel
        // "Kategori Layanan" di beranda jatuh ke gradien biru polos.
        $images = StoredImages::pool('service-categories', 'services', 'banners');

        foreach ($this->categories() as $i => $cat) {
            ServiceCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'short_description' => $cat['summary'],
                    'image' => StoredImages::pick($images, $i),
                    'is_featured' => true,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        $this->command?->info('Kategori layanan siap: '.ServiceCategory::count().' kategori, '
            .ServiceCategory::whereNotNull('image')->count().' bergambar.');
    }

    /**
     * Kolom icon sengaja tidak diisi: ada di tabel tetapi tidak dibaca satu pun
     * view — hanya name, short_description, dan image yang benar-benar tampil.
     *
     * @return list<array<string, mixed>>
     */
    private function categories(): array
    {
        return [
            [
                'slug' => 'konsultasi-manajemen',
                'name' => ['id' => 'Konsultasi Manajemen', 'en' => 'Management Consulting'],
                'summary' => [
                    'id' => 'Menata struktur organisasi, alur wewenang, dan uraian tugas agar keputusan tidak tersendat di banyak lapis.',
                    'en' => 'Reshaping structure, authority flow, and job descriptions so decisions stop stalling across layers.',
                ],
            ],
            [
                'slug' => 'konsultasi-manajemen-human-capital',
                'name' => ['id' => 'Konsultasi Manajemen Human Capital', 'en' => 'Human Capital Management Consulting'],
                'summary' => [
                    'id' => 'Memetakan kompetensi, menyiapkan suksesi jabatan kunci, dan menyusun KPI beserta struktur remunerasinya.',
                    'en' => 'Mapping competencies, preparing succession for key roles, and designing KPIs with their pay structure.',
                ],
            ],
            [
                'slug' => 'headhunter',
                'name' => ['id' => 'Headhunter', 'en' => 'Headhunter'],
                'summary' => [
                    'id' => 'Pencarian tertutup untuk posisi manajerial dan direksi yang sulit diisi lewat lowongan terbuka.',
                    'en' => 'Confidential search for managerial and board roles that open advertising cannot fill.',
                ],
            ],
            [
                'slug' => 'pelatihan-karyawan',
                'name' => ['id' => 'Pelatihan Karyawan', 'en' => 'Employee Training'],
                'summary' => [
                    'id' => 'Program in-house yang materinya disusun dari pekerjaan peserta sehari-hari, bukan dari katalog siap pakai.',
                    'en' => 'In-house programmes built from participants\' own daily work rather than an off-the-shelf catalogue.',
                ],
            ],
            [
                'slug' => 'sertifikasi-kompetensi',
                'name' => ['id' => 'Sertifikasi Kompetensi', 'en' => 'Competency Certification'],
                'summary' => [
                    'id' => 'Uji kompetensi skema BNSP yang dapat digelar di tempat kerja Anda, mengikuti pola sif, tanpa menghentikan produksi.',
                    'en' => 'BNSP-scheme assessment that can run at your workplace, around your shift pattern, without stopping production.',
                ],
            ],
        ];
    }
}
