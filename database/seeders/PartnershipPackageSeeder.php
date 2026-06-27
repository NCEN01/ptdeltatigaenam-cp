<?php

namespace Database\Seeders;

use App\Models\PartnershipPackage;
use Illuminate\Database\Seeder;

class PartnershipPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['tier' => 'blue', 'highlighted' => false, 'color' => '#1D4ED8'],
            ['tier' => 'silver', 'highlighted' => false, 'color' => '#94A3B8'],
            ['tier' => 'gold', 'highlighted' => true, 'color' => '#D4A017'],
            ['tier' => 'platinum', 'highlighted' => false, 'color' => '#0F172A'],
        ];

        foreach ($packages as $i => $pkg) {
            $label = ucfirst($pkg['tier']);

            PartnershipPackage::updateOrCreate(
                ['tier' => $pkg['tier']],
                [
                    'name' => ['id' => $label, 'en' => $label],
                    'slug' => $pkg['tier'],
                    'color' => $pkg['color'],
                    'is_highlighted' => $pkg['highlighted'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
