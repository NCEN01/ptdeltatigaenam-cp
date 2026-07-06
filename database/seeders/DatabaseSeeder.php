<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            ServiceCategorySeeder::class,
            PartnershipPackageSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
