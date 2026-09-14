<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Partner;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;

/**
 * Logo klien dan mitra yang tampil di beranda serta halaman Portofolio.
 *
 * Berkas logonya masih tersimpan di storage/app/public/clients dan /partners,
 * jadi seeder ini memasangkan kembali berkas itu ke barisnya.
 */
class ClientPartnerSeeder extends Seeder
{
    public function run(): void
    {
        $clientLogos = StoredImages::in('clients');
        $partnerLogos = StoredImages::in('partners');

        foreach ($this->clients() as $i => $name) {
            Client::updateOrCreate(
                ['name' => $name],
                [
                    'logo' => StoredImages::pick($clientLogos, $i),
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        foreach ($this->partners() as $i => $partner) {
            Partner::updateOrCreate(
                ['name' => $partner['name']],
                [
                    'registration_number' => $partner['registration_number'],
                    'logo' => StoredImages::pick($partnerLogos, $i),
                    'website_url' => $partner['website_url'],
                    'description' => $partner['description'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        $this->command?->info('Klien & mitra siap: '.Client::count().' klien, '.Partner::count().' mitra.');
    }

    /** @return list<string> */
    private function clients(): array
    {
        return [
            'PT Nusantara Jaya',
            'Bank Sinar Mas',
            'Global Energi Group',
            'PT Baja Perkasa',
            'PT Cipta Mandiri Manufaktur',
            'PT Tirta Anugerah',
            'PT Lintas Samudra Energi',
            'Bank Pembangunan Daerah Jabar',
            'PT Sumber Daya Elektrik',
            'PT Agro Lestari Nusantara',
            'PT Karya Bangun Sejahtera',
            'PT Ritel Maju Bersama',
            'RS Harapan Sehat',
            'PT Konstruksi Bumi Persada',
            'PT Tekstil Indah Permai',
            'PT Asuransi Wira Sentosa',
            'PT Pelabuhan Niaga Indonesia',
            'PT Mineral Jaya Abadi',
            'PT Fabrikasi Logam Nusantara',
            'PT Properti Cendana Group',
        ];
    }

    /** @return list<array<string, mixed>> */
    private function partners(): array
    {
        return [
            [
                'name' => 'Badan Nasional Sertifikasi Profesi',
                'registration_number' => 'BNSP-LSP-0361',
                'website_url' => 'https://bnsp.go.id',
                'description' => [
                    'id' => 'Lembaga yang menetapkan skema dan menerbitkan sertifikat kompetensi kerja nasional.',
                    'en' => 'The body that sets schemes and issues national work competency certificates.',
                ],
            ],
            [
                'name' => 'LSP Manajemen Sumber Daya Manusia',
                'registration_number' => 'LSP-MSDM-118',
                'website_url' => null,
                'description' => [
                    'id' => 'Lembaga sertifikasi profesi mitra untuk skema kompetensi bidang human capital.',
                    'en' => 'Partner certification body for human capital competency schemes.',
                ],
            ],
            [
                'name' => 'LSP Keselamatan dan Kesehatan Kerja',
                'registration_number' => 'LSP-K3-204',
                'website_url' => null,
                'description' => [
                    'id' => 'Mitra pelaksanaan uji kompetensi bidang keselamatan dan kesehatan kerja.',
                    'en' => 'Partner for competency assessment in occupational safety and health.',
                ],
            ],
            [
                'name' => 'Asosiasi Praktisi Human Capital Indonesia',
                'registration_number' => 'APHCI-2019-77',
                'website_url' => null,
                'description' => [
                    'id' => 'Wadah profesi yang menjadi rujukan standar praktik pengelolaan human capital.',
                    'en' => 'A professional association setting reference standards for human capital practice.',
                ],
            ],
            [
                'name' => 'Kamar Dagang dan Industri Daerah',
                'registration_number' => 'KADIN-JKT-4412',
                'website_url' => null,
                'description' => [
                    'id' => 'Mitra penghubung program pelatihan dengan kebutuhan dunia usaha di daerah.',
                    'en' => 'Partner connecting training programmes to regional business needs.',
                ],
            ],
            [
                'name' => 'Politeknik Industri Nusantara',
                'registration_number' => 'PIN-KERJASAMA-88',
                'website_url' => null,
                'description' => [
                    'id' => 'Mitra akademik untuk penyusunan kurikulum pelatihan berbasis kebutuhan industri.',
                    'en' => 'Academic partner for building industry-driven training curricula.',
                ],
            ],
            [
                'name' => 'Balai Latihan Kerja Regional',
                'registration_number' => 'BLK-REG-0912',
                'website_url' => null,
                'description' => [
                    'id' => 'Mitra penyediaan fasilitas praktik dan tempat uji kompetensi di daerah.',
                    'en' => 'Partner providing practice facilities and regional assessment venues.',
                ],
            ],
            [
                'name' => 'Asosiasi Kontraktor Konstruksi Indonesia',
                'registration_number' => 'AKKI-2021-330',
                'website_url' => null,
                'description' => [
                    'id' => 'Mitra penyaluran program sertifikasi tenaga kerja konstruksi bersertifikat.',
                    'en' => 'Partner channelling certification programmes for construction workers.',
                ],
            ],
        ];
    }
}
