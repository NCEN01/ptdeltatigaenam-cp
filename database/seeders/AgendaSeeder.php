<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Agenda kegiatan: sebagian sudah lewat, sebagian akan datang.
 *
 * Tanggalnya dihitung relatif terhadap hari ini, bukan ditulis mati. Dengan
 * begitu halaman Agenda selalu punya campuran "akan datang" dan "selesai"
 * berapa pun lama seeder ini tidak dijalankan ulang — kalau tanggalnya
 * dipatok, seluruh agenda akan berubah menjadi masa lalu dalam hitungan bulan.
 */
class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $images = StoredImages::pool('agenda', 'services', 'banners', 'blog');

        foreach ($this->items() as $index => $item) {
            $starts = Carbon::today()->addDays($item['offset'])->setTime(8, 30);

            Agenda::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'content' => [
                        'id' => $this->body($item, 'id'),
                        'en' => $this->body($item, 'en'),
                    ],
                    'location' => $item['location'],
                    'image' => StoredImages::pick($images, $index),
                    'status' => 'published',
                    'starts_at' => $starts,
                    'ends_at' => $starts->copy()->addDays($item['days'] - 1)->setTime(16, 30),
                ],
            );
        }

        $upcoming = Agenda::published()->where('starts_at', '>=', now())->count();
        $past = Agenda::published()->where('starts_at', '<', now())->count();

        $this->command?->info("Agenda siap: {$upcoming} akan datang, {$past} sudah selesai.");
    }

    private function body(array $item, string $locale): string
    {
        $isId = $locale === 'id';

        return implode('', [
            '<p>'.$item['intro'][$locale].'</p>',
            '<h2>'.($isId ? 'Materi yang dibahas' : 'What will be covered').'</h2>',
            '<ul>'.implode('', array_map(
                fn ($topic) => '<li>'.$topic[$locale].'</li>',
                $item['topics'],
            )).'</ul>',
            '<h2>'.($isId ? 'Siapa yang sebaiknya hadir' : 'Who should attend').'</h2>',
            '<p>'.$item['audience'][$locale].'</p>',
            '<h2>'.($isId ? 'Keterangan tambahan' : 'Additional information').'</h2>',
            '<p>'.($isId
                ? 'Peserta memperoleh modul, konsumsi selama kegiatan, dan sertifikat kepesertaan. Untuk pendaftaran kelompok di atas lima orang, silakan hubungi tim kami lebih dulu.'
                : 'Participants receive modules, refreshments during the event, and a certificate of attendance. For group registration above five people, please contact our team in advance.'
            ).'</p>',
        ]);
    }

    /**
     * offset: hari dari sekarang. Negatif = sudah lewat, positif = akan datang.
     *
     * @return list<array<string, mixed>>
     */
    private function items(): array
    {
        $raw = [
            // ---- Akan datang (8) ----
            [7, 3, 'Pelatihan Ahli K3 Umum Angkatan 24', 'General Safety Expert Training Batch 24', 'Jakarta Selatan, DKI Jakarta'],
            [14, 2, 'Workshop Penyusunan KPI dan Sasaran Kerja', 'KPI and Work Target Design Workshop', 'Jakarta Pusat, DKI Jakarta'],
            [21, 4, 'Sertifikasi BNSP Bidang Human Resource', 'BNSP Human Resource Certification', 'Bandung, Jawa Barat'],
            [30, 3, 'Pelatihan Kepemimpinan untuk Supervisor Baru', 'Leadership Training for New Supervisors', 'Surabaya, Jawa Timur'],
            [42, 2, 'Seminar Tren Pengelolaan Talenta 2027', '2027 Talent Management Trends Seminar', 'Jakarta Selatan, DKI Jakarta'],
            [55, 5, 'Uji Kompetensi Juru Las Angkatan 11', 'Welder Competency Assessment Batch 11', 'Batam, Kepulauan Riau'],
            [68, 3, 'Pelatihan Wawancara Berbasis Perilaku', 'Behaviour-Based Interview Training', 'Semarang, Jawa Tengah'],
            [84, 2, 'Workshop Assessment Center untuk Praktisi SDM', 'Assessment Centre Workshop for HR Practitioners', 'Jakarta Selatan, DKI Jakarta'],

            // ---- Sudah selesai (12) ----
            [-9, 3, 'Pelatihan Layanan Pelanggan Ritel Angkatan 8', 'Retail Customer Service Training Batch 8', 'Tangerang, Banten'],
            [-21, 2, 'Workshop Analisis Kebutuhan Pelatihan', 'Training Needs Analysis Workshop', 'Jakarta Pusat, DKI Jakarta'],
            [-34, 4, 'Sertifikasi BNSP Teknisi Listrik Angkatan 6', 'BNSP Electrical Technician Certification Batch 6', 'Surabaya, Jawa Timur'],
            [-47, 3, 'Pelatihan K3 dan Tanggap Darurat Migas', 'Oil & Gas Safety and Emergency Response Training', 'Balikpapan, Kalimantan Timur'],
            [-58, 2, 'Seminar Manajemen Risiko Operasional', 'Operational Risk Management Seminar', 'Samarinda, Kalimantan Timur'],
            [-70, 5, 'Uji Kompetensi Operator Produksi Angkatan 9', 'Production Operator Assessment Batch 9', 'Cilegon, Banten'],
            [-85, 3, 'Pelatihan Pola Pikir Digital untuk Asuransi', 'Digital Mindset Training for Insurance', 'Jakarta Barat, DKI Jakarta'],
            [-98, 2, 'Workshop Penyusunan Jalur Karier', 'Career Path Design Workshop', 'Jakarta Selatan, DKI Jakarta'],
            [-112, 4, 'Sertifikasi Ahli K3 Umum Angkatan 22', 'General Safety Expert Certification Batch 22', 'Makassar, Sulawesi Selatan'],
            [-128, 2, 'Seminar Transformasi Human Capital', 'Human Capital Transformation Seminar', 'Jakarta Selatan, DKI Jakarta'],
            [-145, 3, 'Pelatihan Manajemen Kinerja Karyawan', 'Employee Performance Management Training', 'Yogyakarta, DI Yogyakarta'],
            [-162, 5, 'Uji Kompetensi Juru Las Angkatan 10', 'Welder Competency Assessment Batch 10', 'Batam, Kepulauan Riau'],
        ];

        return array_map(fn ($row) => $this->expand($row), $raw);
    }

    /**
     * @param  array{0:int,1:int,2:string,3:string,4:string}  $row
     */
    private function expand(array $row): array
    {
        [$offset, $days, $titleId, $titleEn, $location] = $row;

        return [
            'slug' => Str::slug($titleId),
            'title' => ['id' => $titleId, 'en' => $titleEn],
            'location' => $location,
            'offset' => $offset,
            'days' => $days,
            'excerpt' => [
                'id' => "Kegiatan {$days} hari di {$location}, terbuka untuk peserta umum dan kelompok perusahaan.",
                'en' => "A {$days}-day programme in {$location}, open to individual and corporate participants.",
            ],
            'intro' => [
                'id' => 'Kegiatan ini dirancang agar peserta tidak hanya menerima penjelasan, tetapi langsung mempraktikkannya pada kasus yang mereka bawa dari tempat kerja masing-masing. Porsi latihan lebih besar dibanding pemaparan.',
                'en' => 'This programme is designed so participants do more than listen: they practise directly on cases brought from their own workplaces. Exercise time outweighs presentation time.',
            ],
            'topics' => [
                ['id' => 'Dasar dan kerangka kerja yang dipakai di lapangan.', 'en' => 'The foundations and framework used in the field.'],
                ['id' => 'Studi kasus dari industri peserta sendiri.', 'en' => 'Case studies from participants\' own industries.'],
                ['id' => 'Latihan terbimbing dengan umpan balik langsung dari pengajar.', 'en' => 'Guided practice with direct instructor feedback.'],
                ['id' => 'Penyusunan rencana tindak lanjut untuk dibawa pulang.', 'en' => 'Building a follow-up action plan to take home.'],
            ],
            'audience' => [
                'id' => 'Praktisi, pengawas, dan staf yang menjalankan fungsi terkait secara langsung, termasuk mereka yang sedang disiapkan untuk peran tersebut.',
                'en' => 'Practitioners, supervisors, and staff who run the related function directly, including those being prepared for such a role.',
            ],
        ];
    }
}
