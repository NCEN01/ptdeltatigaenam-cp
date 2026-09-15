<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Partner;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;

/**
 * Logo klien dan mitra yang tampil di beranda serta halaman Portofolio.
 *
 * Klien memakai logo asli dengan nama berkas tetap, jadi barisnya menunjuk
 * langsung ke berkasnya. Mitra masih memakai gambar contoh, sehingga tetap
 * mengambil apa pun yang tersedia di storage/app/public/partners.
 */
class ClientPartnerSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->clients() as $i => $client) {
            Client::updateOrCreate(
                ['name' => $client['name']],
                [
                    'logo' => $client['logo'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ],
            );
        }

        // Mitra sudah diisi admin lewat CMS; daftar di bawah cuma contoh untuk
        // pemasangan baru. Dulu barisnya ditulis ulang setiap kali seeder jalan,
        // sehingga mitra contoh yang sudah sengaja dihapus admin hidup lagi dan
        // harus dihapus manual sekali lagi. Sekarang hanya diisi saat kosong.
        if (Partner::query()->doesntExist()) {
            $partnerLogos = StoredImages::in('partners');

            foreach ($this->partners() as $i => $partner) {
                Partner::create([
                    'name' => $partner['name'],
                    'registration_number' => $partner['registration_number'],
                    'logo' => StoredImages::pick($partnerLogos, $i),
                    'website_url' => $partner['website_url'],
                    'description' => $partner['description'],
                    'sort_order' => $i + 1,
                    'is_active' => true,
                ]);
            }
        }

        $this->command?->info('Klien & mitra siap: '.Client::count().' klien, '.Partner::count().' mitra.');
    }

    /**
     * Klien asli. Nama diambil persis seperti tertulis pada logonya, dan
     * dipakai juga sebagai teks alternatif gambar di pita logo.
     *
     * @return list<array{name: string, logo: string}>
     */
    private function clients(): array
    {
        return [
            ['name' => 'Pertamina Training & Consulting', 'logo' => 'clients/pertamina-training-and-consulting.webp'],
            ['name' => 'Pertamina International Shipping', 'logo' => 'clients/pertamina-international-shipping.webp'],
            ['name' => 'Pertamina Gas Negara', 'logo' => 'clients/pertamina-gas-negara.webp'],
            ['name' => 'Pertamina Energy Terminal', 'logo' => 'clients/pertamina-energy-terminal.webp'],
            ['name' => 'Pertamina Trans Kontinental', 'logo' => 'clients/pertamina-trans-kontinental.webp'],
            ['name' => 'Pertamina Port and Logistics', 'logo' => 'clients/pertamina-port-and-logistics.webp'],
            ['name' => 'Pertamina Marine Solutions', 'logo' => 'clients/pertamina-marine-solutions.webp'],
            ['name' => 'Pertamina Marine Engineering', 'logo' => 'clients/pertamina-marine-engineering.webp'],
            ['name' => 'Pertamina PDC', 'logo' => 'clients/pertamina-pdc.webp'],
            ['name' => 'Universitas Indonesia', 'logo' => 'clients/universitas-indonesia.webp'],
            ['name' => 'Pupuk Sriwidjaja Palembang', 'logo' => 'clients/pupuk-sriwidjaja-pusri.webp'],
            ['name' => 'Bank Indonesia', 'logo' => 'clients/bank-indonesia.webp'],
            ['name' => 'Telkom Indonesia', 'logo' => 'clients/telkom-indonesia.webp'],
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
