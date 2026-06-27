<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['id' => 'Konsultasi Manajemen', 'en' => 'Management Consulting', 'slug' => 'konsultasi-manajemen'],
            ['id' => 'Konsultasi Manajemen Human Capital', 'en' => 'Human Capital Management Consulting', 'slug' => 'konsultasi-manajemen-human-capital'],
            ['id' => 'Headhunter', 'en' => 'Headhunter', 'slug' => 'headhunter'],
            ['id' => 'Pelatihan Karyawan', 'en' => 'Employee Training', 'slug' => 'pelatihan-karyawan'],
            ['id' => 'Sertifikasi Kompetensi', 'en' => 'Competency Certification', 'slug' => 'sertifikasi-kompetensi'],
        ];

        foreach ($categories as $i => $cat) {
            ServiceCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => ['id' => $cat['id'], 'en' => $cat['en']],
                    'is_featured' => true,
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
