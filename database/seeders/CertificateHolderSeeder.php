<?php

namespace Database\Seeders;

use App\Models\CertificateHolder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CertificateHolderSeeder extends Seeder
{
    public function run(): void
    {
        $exp = Carbon::create(2027, 8, 1);

        $rows = [
            ['124', 'Fahmy Akbar Idries, S.E., M.M.', 'PT. BPR Bank Daerah Gunung Kidul (Perseroda)', '64127 1346 5 1481 2022', 'P.E. Audit Internal'],
            ['709', 'Nano Priatno, S.P.', 'PT. BPR Majalengka Jabar', '64127 1120 6 1453 2022', 'Direktur Tingkat 2'],
            ['22884', 'Rizyana Mirda', 'Umum (Perorangan BPR)', '64127 1120 6 1471 2022', 'Komisaris'],
            ['22846', 'Akhim Akhmad K.M.', 'Umum (Perorangan BPR)', '64127 1120 6 1458 2022', 'Komisaris'],
            ['22790', 'Drs. Doni Hermawan', 'Umum (Perorangan BPR)', '64127 1120 6 1459 2022', 'Komisaris'],
            ['19376', 'Giandara Muhsy', 'PT. BPR Cirebon Jabar', '64127 1120 6 1451 2022', 'Direktur Tingkat 2'],
            ['22800', 'H. Syahrial Aziz, S.H., M.M.', 'Umum (Perorangan BPR)', '64127 1120 6 1461 2022', 'Komisaris'],
            ['1221', 'Yacob Chandra', 'Umum (Perorangan BPR)', '64127 1120 6 1444 2022', 'Direktur Tingkat 1'],
            ['19382', 'Yanti Rosmalawati', 'PT. BPR Cirebon Jabar', '64127 1120 6 1456 2022', 'Direktur Tingkat 2'],
            ['20455', 'Bambang Setiawan, S.E.', 'PT. Nusantara Jaya', '64127 1120 6 1490 2022', 'Manajer SDM'],
            ['20456', 'Sri Wahyuni, S.Psi.', 'PT. Manufaktur Prima', '64127 1120 6 1491 2022', 'Asesor Kompetensi'],
            ['20457', 'Dedi Kurniawan', 'PT. Retail Sukses', '64127 1120 6 1492 2022', 'Supervisor Operasional'],
        ];

        foreach ($rows as $i => $r) {
            CertificateHolder::updateOrCreate(
                ['certificate_number' => $r[3]],
                [
                    'ujk_number' => $r[0],
                    'participant_name' => $r[1],
                    'company_name' => $r[2],
                    'qualification' => $r[4],
                    'issued_at' => Carbon::create(2022, 8, 1),
                    'expires_at' => $exp,
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ],
            );
        }
    }
}
