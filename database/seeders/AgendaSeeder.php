<?php

namespace Database\Seeders;

use App\Models\Agenda;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'slug' => 'pelatihan-leadership-manajemen-tim',
                'title' => ['id' => 'Pelatihan Leadership & Manajemen Tim', 'en' => 'Leadership & Team Management Training'],
                'excerpt' => ['id' => 'Program pelatihan intensif untuk membangun pemimpin yang mampu menggerakkan tim menuju kinerja tinggi.', 'en' => 'An intensive program to build leaders who drive teams toward high performance.'],
                'location' => 'Jakarta Selatan',
                'starts_at' => Carbon::now()->addDays(10)->setTime(9, 0),
                'ends_at' => Carbon::now()->addDays(10)->setTime(16, 0),
            ],
            [
                'slug' => 'sertifikasi-kompetensi-hr-nasional',
                'title' => ['id' => 'Sertifikasi Kompetensi HR Nasional', 'en' => 'National HR Competency Certification'],
                'excerpt' => ['id' => 'Uji dan raih sertifikasi kompetensi HR yang diakui secara profesional untuk meningkatkan kredibilitas Anda.', 'en' => 'Assess and earn a professionally recognized HR competency certification to boost your credibility.'],
                'location' => 'Bekasi',
                'starts_at' => Carbon::now()->addDays(24)->setTime(8, 30),
                'ends_at' => Carbon::now()->addDays(25)->setTime(15, 0),
            ],
            [
                'slug' => 'seminar-transformasi-human-capital',
                'title' => ['id' => 'Seminar Transformasi Human Capital', 'en' => 'Human Capital Transformation Seminar'],
                'excerpt' => ['id' => 'Diskusi strategi pengembangan SDM di era digital bersama praktisi dan pemimpin industri.', 'en' => 'Discussing HR development strategies in the digital era with practitioners and industry leaders.'],
                'location' => 'Serang, Banten',
                'starts_at' => Carbon::now()->addDays(40)->setTime(13, 0),
                'ends_at' => Carbon::now()->addDays(40)->setTime(17, 0),
            ],
        ];

        foreach ($items as $item) {
            Agenda::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'location' => $item['location'],
                    'starts_at' => $item['starts_at'],
                    'ends_at' => $item['ends_at'],
                    'status' => 'published',
                ],
            );
        }
    }
}
