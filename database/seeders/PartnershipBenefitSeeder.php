<?php

namespace Database\Seeders;

use App\Models\PartnershipBenefit;
use Illuminate\Database\Seeder;

/**
 * Mengisi daftar "Mengapa menjadi mitra kami" di halaman Kemitraan.
 *
 * Halaman itu punya teks cadangan bila tabelnya kosong, tapi cadangan tersebut
 * tidak bisa disunting lewat CMS — jadi datanya tetap perlu ada di database.
 */
class PartnershipBenefitSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->benefits() as $i => $benefit) {
            PartnershipBenefit::updateOrCreate(
                ['icon' => $benefit['icon']],
                [
                    'title' => $benefit['title'],
                    'description' => $benefit['description'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        $this->command?->info('Manfaat kemitraan siap: '.PartnershipBenefit::count().' butir.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function benefits(): array
    {
        return [
            [
                'icon' => 'academic-cap',
                'title' => ['id' => 'Program disusun dari kebutuhan Anda', 'en' => 'Programmes built from your needs'],
                'description' => [
                    'id' => 'Setiap kemitraan dimulai dari analisis kebutuhan pelatihan di tempat Anda, bukan dari katalog yang sudah jadi. Materi, studi kasus, dan contoh yang dipakai diambil dari pekerjaan peserta sehari-hari.',
                    'en' => 'Every partnership starts with a training needs analysis at your site, not from an off-the-shelf catalogue. Material, cases, and examples come from participants\' own daily work.',
                ],
            ],
            [
                'icon' => 'badge-check',
                'title' => ['id' => 'Sertifikasi yang diakui secara nasional', 'en' => 'Nationally recognised certification'],
                'description' => [
                    'id' => 'Uji kompetensi dijalankan mengikuti skema BNSP, sehingga sertifikat yang diterima karyawan Anda berlaku untuk keperluan audit pelanggan, tender, dan persyaratan regulasi.',
                    'en' => 'Assessment follows the BNSP scheme, so the certificates your employees receive hold up for customer audits, tenders, and regulatory requirements.',
                ],
            ],
            [
                'icon' => 'users',
                'title' => ['id' => 'Pengajar yang berasal dari industri', 'en' => 'Instructors who come from industry'],
                'description' => [
                    'id' => 'Pengajar kami adalah praktisi yang pernah menjalankan pekerjaan yang mereka ajarkan. Pertanyaan teknis dari peserta dijawab dari pengalaman, bukan dari salindia.',
                    'en' => 'Our instructors are practitioners who have done the work they teach. Technical questions get answered from experience, not from slides.',
                ],
            ],
            [
                'icon' => 'chart-bar',
                'title' => ['id' => 'Hasil yang bisa Anda ukur', 'en' => 'Results you can measure'],
                'description' => [
                    'id' => 'Setiap program ditutup dengan laporan berisi nilai sebelum dan sesudah, tingkat kelulusan, serta catatan tindak lanjut per peserta, bukan sekadar daftar hadir.',
                    'en' => 'Every programme closes with a report showing before-and-after scores, pass rates, and per-participant follow-up notes, not just an attendance list.',
                ],
            ],
            [
                'icon' => 'calendar',
                'title' => ['id' => 'Jadwal yang menyesuaikan operasional', 'en' => 'Scheduling that fits operations'],
                'description' => [
                    'id' => 'Pelatihan dan uji kompetensi dapat digelar di lokasi Anda, termasuk mengikuti pola sif, agar tidak ada lini kerja yang harus berhenti selama program berjalan.',
                    'en' => 'Training and assessment can run at your site, including around shift patterns, so no work line has to stop while the programme runs.',
                ],
            ],
            [
                'icon' => 'shield-check',
                'title' => ['id' => 'Pendampingan setelah program selesai', 'en' => 'Support after the programme ends'],
                'description' => [
                    'id' => 'Kemitraan tidak berhenti di hari terakhir kelas. Kami mendampingi penerapan di tempat kerja dan meninjau perkembangannya bersama tim Anda secara berkala.',
                    'en' => 'The partnership does not end on the last day of class. We support workplace application and review progress with your team at regular intervals.',
                ],
            ],
        ];
    }
}
