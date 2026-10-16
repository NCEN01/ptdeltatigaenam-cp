<?php

namespace Database\Seeders;

use App\Models\ProgramBenefit;
use Illuminate\Database\Seeder;

/**
 * Benefit bawaan yang berlaku untuk semua program.
 *
 * Hanya mengisi bila tabelnya masih kosong. Dijalankan ulang tidak
 * menggandakan apa pun dan tidak menimpa suntingan admin — begitu ada satu
 * baris saja, seeder ini berhenti.
 *
 * Isinya titik awal yang masuk akal, bukan ketetapan: admin menyuntingnya
 * dari panel di Layanan → Benefit Bawaan.
 */
class ProgramBenefitSeeder extends Seeder
{
    public function run(): void
    {
        if (ProgramBenefit::whereNull('service_id')->exists()) {
            $this->command?->info('Benefit bawaan sudah terisi, dilewati.');

            return;
        }

        $items = [
            [
                'icon' => 'certificate',
                'id' => ['Sertifikat kompetensi resmi', 'Diterbitkan lembaga sertifikasi berlisensi BNSP dan berlaku untuk audit, tender, maupun persyaratan regulasi.'],
                'en' => ['Officially recognised certificate', 'Issued by a BNSP-licensed certification body and valid for audits, tenders, and regulatory requirements.'],
            ],
            [
                'icon' => 'people',
                'id' => ['Mentor praktisi, bukan hanya pengajar', 'Dibimbing orang yang mengerjakan bidangnya sehari-hari, sehingga contoh kasusnya datang dari pekerjaan nyata.'],
                'en' => ['Practitioner mentors, not only lecturers', 'Guided by people who work in the field daily, so the cases come from real work.'],
            ],
            [
                'icon' => 'presentation',
                'id' => ['Modul dan materi lengkap', 'Seluruh materi diberikan dalam bentuk digital dan tetap bisa dibuka setelah kelas selesai.'],
                'en' => ['Complete modules and materials', 'All material is provided digitally and stays accessible after the class ends.'],
            ],
            [
                'icon' => 'target',
                'id' => ['Latihan berbasis kasus nyata', 'Setiap materi ditutup latihan yang mengambil persoalan dari lapangan, bukan soal buatan.'],
                'en' => ['Practice on real cases', 'Every topic closes with an exercise drawn from the field, not an invented problem.'],
            ],
            [
                'icon' => 'shield',
                'id' => ['Pendampingan sampai uji kompetensi', 'Tidak berhenti saat kelas usai: Anda didampingi sampai asesmen dan penerbitan sertifikatnya.'],
                'en' => ['Support through to assessment', 'It does not stop when the class ends: we stay with you through assessment and certificate issuance.'],
            ],
            [
                'icon' => 'chart',
                'id' => ['Laporan hasil untuk perusahaan', 'Perusahaan pengirim menerima ringkasan capaian tiap peserta sebagai dasar pengembangan berikutnya.'],
                'en' => ['Outcome report for the company', 'Sending companies receive a summary of each participant’s results as a basis for what comes next.'],
            ],
        ];

        foreach ($items as $i => $item) {
            ProgramBenefit::create([
                'service_id' => null,
                'title' => ['id' => $item['id'][0], 'en' => $item['en'][0]],
                'description' => ['id' => $item['id'][1], 'en' => $item['en'][1]],
                'icon' => $item['icon'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }

        $this->command?->info(count($items).' benefit bawaan dibuat.');
    }
}
