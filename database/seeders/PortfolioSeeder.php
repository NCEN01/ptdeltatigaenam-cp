<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn (string $slug) => ServiceCategory::where('slug', $slug)->value('id');
        $img = fn (string $photo) => "https://images.unsplash.com/{$photo}?auto=format&fit=crop&w=1200&q=80";

        $items = [
            [
                'slug' => 'transformasi-human-capital-nasional',
                'title' => ['id' => 'Transformasi Human Capital Nasional', 'en' => 'National Human Capital Transformation'],
                'short_description' => ['id' => 'Merancang ulang strategi & struktur SDM untuk 1.200+ karyawan di 12 cabang.', 'en' => 'Redesigning HR strategy & structure for 1,200+ employees across 12 branches.'],
                'client_name' => 'PT Nusantara Jaya',
                'cat' => 'konsultasi-manajemen-human-capital',
                'cover' => 'photo-1522071820081-009f0129c71c',
                'date' => '2025-02-15',
            ],
            [
                'slug' => 'program-pelatihan-kepemimpinan',
                'title' => ['id' => 'Program Pelatihan Kepemimpinan', 'en' => 'Leadership Development Program'],
                'short_description' => ['id' => 'Membangun 80 pemimpin lini melalui pelatihan berbasis studi kasus selama 6 bulan.', 'en' => 'Building 80 line leaders through 6 months of case-study based training.'],
                'client_name' => 'Bank Sinar Mas',
                'cat' => 'pelatihan-karyawan',
                'cover' => 'photo-1524178232363-1fb2b075b655',
                'date' => '2025-04-08',
            ],
            [
                'slug' => 'rekrutmen-eksekutif-c-level',
                'title' => ['id' => 'Rekrutmen Eksekutif C-Level', 'en' => 'C-Level Executive Search'],
                'short_description' => ['id' => 'Penempatan CFO & COO melalui proses headhunter yang selektif dan cepat.', 'en' => 'Placing a CFO & COO through a selective, fast executive-search process.'],
                'client_name' => 'Global Energi Group',
                'cat' => 'headhunter',
                'cover' => 'photo-1600880292089-90a7e086ee0c',
                'date' => '2024-11-20',
            ],
            [
                'slug' => 'sertifikasi-kompetensi-500-karyawan',
                'title' => ['id' => 'Sertifikasi Kompetensi 500+ Karyawan', 'en' => 'Competency Certification for 500+ Staff'],
                'short_description' => ['id' => 'Program sertifikasi kompetensi berskala besar dengan tingkat kelulusan 96%.', 'en' => 'A large-scale competency certification program with a 96% pass rate.'],
                'client_name' => 'Manufaktur Prima',
                'cat' => 'sertifikasi-kompetensi',
                'cover' => 'photo-1517048676732-d65bc937f952',
                'date' => '2025-01-30',
            ],
            [
                'slug' => 'konsultasi-restrukturisasi-organisasi',
                'title' => ['id' => 'Konsultasi Restrukturisasi Organisasi', 'en' => 'Organizational Restructuring Consulting'],
                'short_description' => ['id' => 'Menata ulang struktur & alur kerja untuk efisiensi operasional yang lebih tinggi.', 'en' => 'Reshaping structure & workflows for higher operational efficiency.'],
                'client_name' => 'Retail Sukses',
                'cat' => 'konsultasi-manajemen',
                'cover' => 'photo-1542744173-8e7e53415bb0',
                'date' => '2024-09-12',
            ],
            [
                'slug' => 'assessment-center-talent-mapping',
                'title' => ['id' => 'Assessment Center & Talent Mapping', 'en' => 'Assessment Center & Talent Mapping'],
                'short_description' => ['id' => 'Memetakan potensi 300 talenta untuk perencanaan suksesi kepemimpinan.', 'en' => 'Mapping the potential of 300 talents for leadership succession planning.'],
                'client_name' => 'Telko Nusantara',
                'cat' => 'konsultasi-manajemen-human-capital',
                'cover' => 'photo-1521737604893-d14cc237f11d',
                'date' => '2025-05-22',
            ],
        ];

        foreach ($items as $i => $item) {
            Portfolio::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'short_description' => $item['short_description'],
                    'client_name' => $item['client_name'],
                    'service_category_id' => $cat($item['cat']),
                    'cover_image' => $img($item['cover']),
                    'project_date' => Carbon::parse($item['date']),
                    'is_featured' => $i < 2,
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ],
            );
        }
    }
}
