<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutannya penting: ServiceCategorySeeder harus jalan sebelum layanan dan
     * portofolio (keduanya menunjuk kategori), dan PortfolioSeeder sebelum
     * TestimonialSeeder (testimoni ditautkan ke portofolio lewat slug).
     */
    public function run(): void
    {
        $this->call([
            // Dasar
            RoleSeeder::class,
            AdminUserSeeder::class,
            SettingSeeder::class,
            ServiceCategorySeeder::class,

            // Isi situs
            BannerSeeder::class,
            ServiceSeeder::class,
            PortfolioSeeder::class,
            TestimonialSeeder::class,
            ClientPartnerSeeder::class,
            BlogSeeder::class,
            AgendaSeeder::class,
            CertificateHolderSeeder::class,

            // Kemitraan
            PartnershipPackageSeeder::class,
            PartnershipBenefitSeeder::class,
        ]);
    }
}
