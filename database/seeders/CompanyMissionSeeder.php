<?php

namespace Database\Seeders;

use App\Models\CompanyMission;
use Illuminate\Database\Seeder;

class CompanyMissionSeeder extends Seeder
{
    public function run(): void
    {
        $missions = [
            ['id' => 'Solusi human capital komprehensif yang disesuaikan kebutuhan klien.', 'en' => 'Comprehensive human capital solutions tailored to client needs.'],
            ['id' => 'Program pelatihan inovatif selaras tren industri.', 'en' => 'Innovative training programs aligned with industry trends.'],
            ['id' => 'Layanan headhunter efektif untuk menghadirkan talenta terbaik.', 'en' => 'Effective headhunter services to deliver top talent.'],
        ];

        foreach ($missions as $i => $mission) {
            CompanyMission::updateOrCreate(
                ['sort_order' => $i + 1],
                ['content' => $mission, 'is_active' => true],
            );
        }
    }
}
