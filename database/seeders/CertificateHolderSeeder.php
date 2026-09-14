<?php

namespace Database\Seeders;

use App\Models\CertificateHolder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Data pemegang sertifikat untuk halaman penelusuran sertifikat.
 *
 * Tanggal terbit dihitung mundur dari hari ini dan masa berlakunya tiga tahun,
 * sehingga sebagian besar sertifikat selalu berstatus masih berlaku, dengan
 * beberapa yang sengaja dibuat sudah kedaluwarsa agar tampilan kedua kondisi
 * itu bisa diperiksa.
 */
class CertificateHolderSeeder extends Seeder
{
    /** Masa berlaku sertifikat kompetensi BNSP. */
    private const VALID_YEARS = 3;

    public function run(): void
    {
        foreach ($this->holders() as $index => $holder) {
            [$name, $company, $qualification, $monthsAgo] = $holder;

            $issued = Carbon::today()->subMonths($monthsAgo);
            $number = 1000 + $index;

            CertificateHolder::updateOrCreate(
                ['certificate_number' => sprintf('DTE/BNSP/%s/%04d', $issued->format('Y'), $number)],
                [
                    'ujk_number' => sprintf('UJK-%s-%03d', $issued->format('ym'), $index + 1),
                    'participant_name' => $name,
                    'company_name' => $company,
                    'qualification' => $qualification,
                    'issued_at' => $issued,
                    'expires_at' => $issued->copy()->addYears(self::VALID_YEARS),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }

        $active = CertificateHolder::where('expires_at', '>=', today())->count();
        $expired = CertificateHolder::where('expires_at', '<', today())->count();

        $this->command?->info('Sertifikat siap: '.CertificateHolder::count()
            ." data ({$active} masih berlaku, {$expired} kedaluwarsa).");
    }

    /**
     * [nama, perusahaan, kualifikasi, berapa bulan lalu diterbitkan]
     *
     * Sebagian sengaja diberi umur lebih dari 36 bulan supaya statusnya
     * kedaluwarsa dan tampilan kondisi itu ikut terwakili.
     *
     * @return list<array{0:string,1:string,2:string,3:int}>
     */
    private function holders(): array
    {
        return [
            ['Ahmad Fauzi Rahman', 'PT Baja Perkasa', 'Ahli K3 Umum', 2],
            ['Siti Nurhaliza Putri', 'PT Nusantara Jaya', 'Manajer Human Resource', 3],
            ['Budi Santoso', 'PT Konstruksi Bumi Persada', 'Ahli K3 Umum', 4],
            ['Dewi Anggraini', 'Bank Sinar Mas', 'Supervisor Human Resource', 5],
            ['Rudi Hermawan', 'PT Fabrikasi Logam Nusantara', 'Juru Las SMAW Posisi 3G', 6],
            ['Maya Sari Dewi', 'PT Tirta Anugerah', 'Staf Human Resource', 7],
            ['Eko Prasetyo', 'PT Sumber Daya Elektrik', 'Teknisi Listrik Tegangan Menengah', 8],
            ['Indah Permatasari', 'PT Ritel Maju Bersama', 'Supervisor Layanan Pelanggan', 9],
            ['Hendra Gunawan', 'PT Lintas Samudra Energi', 'Ahli K3 Migas', 10],
            ['Ratna Wulandari', 'Bank Pembangunan Daerah Jabar', 'Manajer Human Resource', 11],
            ['Dimas Aditya Nugraha', 'PT Cipta Mandiri Manufaktur', 'Operator Produksi Madya', 12],
            ['Fitri Handayani', 'RS Harapan Sehat', 'Supervisor Human Resource', 14],
            ['Arif Budiman', 'PT Mineral Jaya Abadi', 'Ahli K3 Pertambangan', 15],
            ['Novi Rahmawati', 'PT Asuransi Wira Sentosa', 'Staf Human Resource', 16],
            ['Gunawan Wibisono', 'PT Agro Lestari Nusantara', 'Manajer Produksi', 18],
            ['Sri Wahyuni', 'PT Tekstil Indah Permai', 'Supervisor Produksi', 19],
            ['Taufik Hidayat', 'PT Pelabuhan Niaga Indonesia', 'Ahli K3 Umum', 20],
            ['Lina Marlina', 'PT Karya Bangun Sejahtera', 'Staf Pengembangan SDM', 22],
            ['Bayu Setiawan', 'PT Fabrikasi Logam Nusantara', 'Juru Las GTAW Posisi 6G', 24],
            ['Anisa Rahmadani', 'Global Energi Group', 'Manajer Human Resource', 26],
            ['Wahyu Kurniawan', 'PT Baja Perkasa', 'Operator Produksi Utama', 28],
            ['Putri Ayu Lestari', 'PT Properti Cendana Group', 'Staf Human Resource', 30],
            ['Ferry Setiadi', 'PT Konstruksi Bumi Persada', 'Ahli K3 Konstruksi', 33],
            // Dua di bawah ini sudah melewati masa berlaku tiga tahun.
            ['Hasan Basri', 'PT Sumber Daya Elektrik', 'Teknisi Listrik Tegangan Rendah', 40],
            ['Yuliana Safitri', 'PT Tirta Anugerah', 'Supervisor Human Resource', 44],
        ];
    }
}
