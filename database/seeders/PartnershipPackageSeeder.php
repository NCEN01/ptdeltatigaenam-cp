<?php

namespace Database\Seeders;

use App\Models\PartnershipPackage;
use Illuminate\Database\Seeder;

class PartnershipPackageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->packages() as $i => $pkg) {
            PartnershipPackage::updateOrCreate(
                ['tier' => $pkg['tier']],
                [
                    'name' => $pkg['name'],
                    'slug' => $pkg['tier'],
                    'tagline' => $pkg['tagline'],
                    'description' => $pkg['description'],
                    'features' => $pkg['features'],
                    'price' => $pkg['price'],
                    'price_note' => $pkg['price_note'],
                    'color' => $pkg['color'],
                    'is_highlighted' => $pkg['highlighted'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        $this->command?->info('Paket kemitraan siap: '.PartnershipPackage::count().' paket.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function packages(): array
    {
        return [
            [
                'tier' => 'blue',
                'color' => '#1D4ED8',
                'highlighted' => false,
                'price' => 25000000,
                'name' => ['id' => 'Blue', 'en' => 'Blue'],
                'tagline' => [
                    'id' => 'Langkah awal bagi perusahaan yang baru mulai membangun program pelatihan internal.',
                    'en' => 'A first step for companies beginning to build an internal training programme.',
                ],
                'description' => [
                    'id' => 'Paket masuk yang mencakup dua program pelatihan in-house dalam setahun beserta laporan hasilnya. Cocok untuk organisasi dengan kebutuhan pengembangan yang masih terfokus pada satu atau dua unit kerja.',
                    'en' => 'An entry package covering two in-house training programmes a year plus outcome reports. Suited to organisations whose development needs still centre on one or two units.',
                ],
                'price_note' => ['id' => 'per tahun', 'en' => 'per year'],
                'features' => [
                    'id' => [
                        '2 program pelatihan in-house per tahun',
                        'Maksimal 25 peserta per program',
                        'Analisis kebutuhan pelatihan awal',
                        'Laporan hasil pelatihan per program',
                        'Sertifikat kepesertaan untuk seluruh peserta',
                    ],
                    'en' => [
                        'Two in-house training programmes per year',
                        'Up to 25 participants per programme',
                        'Initial training needs analysis',
                        'Outcome report for each programme',
                        'Attendance certificates for all participants',
                    ],
                ],
            ],
            [
                'tier' => 'silver',
                'color' => '#94A3B8',
                'highlighted' => false,
                'price' => 55000000,
                'name' => ['id' => 'Silver', 'en' => 'Silver'],
                'tagline' => [
                    'id' => 'Untuk perusahaan yang menjalankan pelatihan rutin di beberapa departemen sekaligus.',
                    'en' => 'For companies running regular training across several departments.',
                ],
                'description' => [
                    'id' => 'Menambah jumlah program, membuka akses uji kompetensi bersertifikat BNSP, dan menyertakan asesmen kompetensi awal sebagai dasar penyusunan rencana pelatihan tahunan.',
                    'en' => 'Adds more programmes, opens access to BNSP-certified assessment, and includes an initial competency assessment to ground the annual training plan.',
                ],
                'price_note' => ['id' => 'per tahun', 'en' => 'per year'],
                'features' => [
                    'id' => [
                        '5 program pelatihan in-house per tahun',
                        'Maksimal 30 peserta per program',
                        'Asesmen kompetensi awal untuk 50 karyawan',
                        '1 batch uji kompetensi BNSP',
                        'Penyusunan rencana pelatihan tahunan',
                        'Laporan perkembangan kompetensi per semester',
                    ],
                    'en' => [
                        'Five in-house training programmes per year',
                        'Up to 30 participants per programme',
                        'Initial competency assessment for 50 employees',
                        'One BNSP assessment batch',
                        'Annual training plan development',
                        'Half-yearly competency progress reports',
                    ],
                ],
            ],
            [
                'tier' => 'gold',
                'color' => '#D4A017',
                'highlighted' => true,
                'price' => 120000000,
                'name' => ['id' => 'Gold', 'en' => 'Gold'],
                'tagline' => [
                    'id' => 'Pilihan paling banyak diambil: pelatihan, sertifikasi, dan pendampingan dalam satu kerangka.',
                    'en' => 'The most-chosen option: training, certification, and advisory in one framework.',
                ],
                'description' => [
                    'id' => 'Mencakup program pelatihan sepanjang tahun, dua batch uji kompetensi, serta pendampingan konsultan untuk menyusun peta kompetensi dan jalur pengembangan karyawan.',
                    'en' => 'Covers year-round training, two assessment batches, and consultant support in building a competency map and employee development paths.',
                ],
                'price_note' => ['id' => 'per tahun', 'en' => 'per year'],
                'features' => [
                    'id' => [
                        '12 program pelatihan in-house per tahun',
                        'Maksimal 35 peserta per program',
                        'Asesmen kompetensi untuk 150 karyawan',
                        '2 batch uji kompetensi BNSP',
                        'Penyusunan peta kompetensi dan jalur karier',
                        'Pendampingan konsultan 4 hari per bulan',
                        'Prioritas penjadwalan program',
                    ],
                    'en' => [
                        '12 in-house training programmes per year',
                        'Up to 35 participants per programme',
                        'Competency assessment for 150 employees',
                        'Two BNSP assessment batches',
                        'Competency map and career path development',
                        'Four consultant days per month',
                        'Priority programme scheduling',
                    ],
                ],
            ],
            [
                'tier' => 'platinum',
                'color' => '#0F172A',
                'highlighted' => false,
                'price' => null,
                'name' => ['id' => 'Platinum', 'en' => 'Platinum'],
                'tagline' => [
                    'id' => 'Kemitraan jangka panjang dengan lingkup yang disusun khusus untuk organisasi besar.',
                    'en' => 'A long-term partnership with scope built specifically for large organisations.',
                ],
                'description' => [
                    'id' => 'Lingkup, jumlah program, dan tingkat pendampingan disusun bersama berdasarkan sebaran lokasi dan jumlah karyawan. Ditujukan bagi organisasi multi-cabang yang membutuhkan standar kompetensi seragam.',
                    'en' => 'Scope, programme volume, and advisory depth are designed together based on site spread and headcount. Aimed at multi-branch organisations needing uniform competency standards.',
                ],
                'price_note' => ['id' => 'sesuai kesepakatan', 'en' => 'by agreement'],
                'features' => [
                    'id' => [
                        'Jumlah program pelatihan tanpa batas kuota tahunan',
                        'Asesmen kompetensi seluruh karyawan',
                        'Uji kompetensi BNSP sesuai kebutuhan',
                        'Penyiapan tempat uji kompetensi di lokasi Anda',
                        'Pendampingan konsultan penuh waktu',
                        'Pelatihan pelatih internal (train the trainer)',
                        'Tinjauan kemitraan bersama direksi setiap triwulan',
                    ],
                    'en' => [
                        'Unlimited annual training programme quota',
                        'Competency assessment for the entire workforce',
                        'BNSP assessment as required',
                        'On-site assessment venue preparation',
                        'Full-time consultant support',
                        'Train-the-trainer programme for internal trainers',
                        'Quarterly partnership review with the board',
                    ],
                ],
            ],
        ];
    }
}
