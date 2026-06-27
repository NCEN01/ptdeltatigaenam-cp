<?php

namespace Database\Seeders;

use App\Models\OfficeLocation;
use Illuminate\Database\Seeder;

class OfficeLocationSeeder extends Seeder
{
    public function run(): void
    {
        $offices = [
            [
                'name' => ['id' => 'Kantor Pusat', 'en' => 'Head Office'],
                'type' => 'pusat',
                'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan, Kebayoran Baru, Jakarta Selatan, DKI Jakarta',
                'phone' => '021-5890 5002',
            ],
            [
                'name' => ['id' => 'Kantor Pemasaran', 'en' => 'Marketing Office'],
                'type' => 'pemasaran',
                'address' => 'Cikarang Technopark, Jalan Inti I Blok C1 No. 7, Cibatu, Cikarang Selatan, Kabupaten Bekasi, Jawa Barat 17530',
                'phone' => '021-8988 1110',
            ],
            [
                'name' => ['id' => 'Kantor Operasional', 'en' => 'Operational Office'],
                'type' => 'operasional',
                'address' => 'Taman Widya Asri Blok GG No. 18, Serang, Kota Serang, Banten 46111',
                'phone' => '0817 018 6104',
            ],
        ];

        foreach ($offices as $i => $office) {
            OfficeLocation::updateOrCreate(
                ['type' => $office['type']],
                array_merge($office, ['sort_order' => $i + 1, 'is_active' => true]),
            );
        }
    }
}
