<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\Testimonial;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;

/**
 * Testimoni untuk beranda, halaman Portofolio, dan halaman detail tiap proyek.
 *
 * Sebagian ditautkan ke portofolio lewat portfolio_id — itulah yang membuat
 * blok "Kata klien tentang proyek ini" muncul di halaman detail portofolio,
 * lengkap dengan rata-rata ratingnya.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $photos = StoredImages::in('testimonials');
        $portfolios = Portfolio::pluck('id', 'slug');

        foreach ($this->testimonials() as $i => $item) {
            Testimonial::updateOrCreate(
                ['author_name' => $item['name'], 'author_company' => $item['company']],
                [
                    'portfolio_id' => $portfolios[$item['portfolio']] ?? null,
                    'author_position' => $item['position'],
                    'author_photo' => StoredImages::pick($photos, $i),
                    'content' => $item['quote'],
                    'rating' => $item['rating'],
                    'is_featured' => $i < 6,
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ],
            );
        }

        $linked = Testimonial::whereNotNull('portfolio_id')->count();

        $this->command?->info('Testimoni siap: '.Testimonial::count().' testimoni ('
            .$linked.' tertaut ke portofolio).');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function testimonials(): array
    {
        return [
            [
                'name' => 'Andika Pratama',
                'company' => 'PT Nusantara Jaya',
                'position' => ['id' => 'Direktur Human Capital', 'en' => 'Human Capital Director'],
                'portfolio' => 'transformasi-human-capital-nasional',
                'rating' => 5,
                'quote' => [
                    'id' => 'Yang membedakan Delta Tiga Enam adalah mereka turun langsung ke cabang, bukan menyusun struktur dari ruang rapat pusat. Hasilnya bisa kami jalankan tanpa banyak revisi di lapangan.',
                    'en' => 'What set Delta Tiga Enam apart was that they went to the branches themselves rather than designing structure from a head-office meeting room. The result worked without much revision on the ground.',
                ],
            ],
            [
                'name' => 'Siti Rahmawati',
                'company' => 'PT Nusantara Jaya',
                'position' => ['id' => 'Manajer Pengembangan Organisasi', 'en' => 'Organisational Development Manager'],
                'portfolio' => 'transformasi-human-capital-nasional',
                'rating' => 5,
                'quote' => [
                    'id' => 'Rotasi antarcabang yang dulu memakan enam pekan kini selesai dalam dua pekan. Perubahan sebesar itu terasa langsung oleh tim saya setiap bulan.',
                    'en' => 'Inter-branch rotation that used to take six weeks now finishes in two. A change that size is felt by my team every single month.',
                ],
            ],
            [
                'name' => 'Bambang Setiawan',
                'company' => 'Bank Sinar Mas',
                'position' => ['id' => 'Kepala Divisi Pembelajaran', 'en' => 'Head of Learning Division'],
                'portfolio' => 'program-pelatihan-kepemimpinan',
                'rating' => 5,
                'quote' => [
                    'id' => 'Materinya memakai kasus nyata dari catatan internal kami sendiri. Peserta tidak bisa menjawab dengan teori, karena kasusnya benar-benar mereka hadapi minggu lalu.',
                    'en' => 'The material used real cases from our own internal records. Participants could not answer with theory, because the cases were ones they had faced the week before.',
                ],
            ],
            [
                'name' => 'Dewi Kartika',
                'company' => 'Bank Sinar Mas',
                'position' => ['id' => 'Supervisor Operasional Cabang', 'en' => 'Branch Operations Supervisor'],
                'portfolio' => 'program-pelatihan-kepemimpinan',
                'rating' => 4,
                'quote' => [
                    'id' => 'Bagian percakapan sulit adalah yang paling saya butuhkan. Saya akhirnya punya cara menyampaikan koreksi tanpa membuat anggota tim menutup diri.',
                    'en' => 'The difficult conversations section was what I needed most. I finally have a way to deliver correction without making team members shut down.',
                ],
            ],
            [
                'name' => 'Hendra Wijaya',
                'company' => 'Global Energi Group',
                'position' => ['id' => 'Komisaris', 'en' => 'Commissioner'],
                'portfolio' => 'rekrutmen-eksekutif-c-level',
                'rating' => 5,
                'quote' => [
                    'id' => 'Pencarian sebelumnya berjalan delapan bulan tanpa hasil. Delta Tiga Enam mengisi dua posisi direksi dalam sebelas pekan, dan keduanya masih menjabat sampai hari ini.',
                    'en' => 'Our previous search ran eight months with nothing. Delta Tiga Enam filled two board roles in eleven weeks, and both are still in post today.',
                ],
            ],
            [
                'name' => 'Rina Marlina',
                'company' => 'PT Baja Perkasa',
                'position' => ['id' => 'Manajer Produksi', 'en' => 'Production Manager'],
                'portfolio' => 'sertifikasi-kompetensi-operator-produksi',
                'rating' => 5,
                'quote' => [
                    'id' => 'Dua ratus empat puluh operator tersertifikasi tanpa satu jam pun produksi berhenti. Itu yang paling saya hargai — mereka menyesuaikan jadwal uji dengan pola sif kami.',
                    'en' => 'Two hundred and forty operators certified without a single production hour lost. That is what I valued most — they fitted the assessment schedule to our shift pattern.',
                ],
            ],
            [
                'name' => 'Agus Salim',
                'company' => 'PT Baja Perkasa',
                'position' => ['id' => 'Koordinator Lini Produksi', 'en' => 'Production Line Coordinator'],
                'portfolio' => 'sertifikasi-kompetensi-operator-produksi',
                'rating' => 4,
                'quote' => [
                    'id' => 'Kami diuji pada mesin yang kami operasikan sehari-hari, bukan di ruang kelas. Tim saya jauh lebih tenang menghadapinya.',
                    'en' => 'We were assessed on the machines we run every day, not in a classroom. My team was far more at ease with it.',
                ],
            ],
            [
                'name' => 'Yusuf Maulana',
                'company' => 'PT Cipta Mandiri Manufaktur',
                'position' => ['id' => 'Direktur Operasional', 'en' => 'Operations Director'],
                'portfolio' => 'restrukturisasi-organisasi-manufaktur',
                'rating' => 5,
                'quote' => [
                    'id' => 'Mereka memangkas titik persetujuan, bukan memangkas orang. Waktu tanggap keluhan pelanggan kami turun dari sembilan hari menjadi tiga, tanpa satu pun PHK.',
                    'en' => 'They cut approval points, not people. Our complaint response time fell from nine days to three with not a single redundancy.',
                ],
            ],
            [
                'name' => 'Lestari Ningsih',
                'company' => 'PT Tirta Anugerah',
                'position' => ['id' => 'Manajer SDM', 'en' => 'HR Manager'],
                'portfolio' => 'asesmen-kompetensi-supervisor',
                'rating' => 5,
                'quote' => [
                    'id' => 'Untuk pertama kalinya keputusan promosi kami punya dasar yang bisa dijelaskan ke karyawan. Perdebatan soal keberpihakan langsung berhenti.',
                    'en' => 'For the first time our promotion decisions had a basis we could explain to employees. The arguments about favouritism stopped immediately.',
                ],
            ],
            [
                'name' => 'Fajar Nugroho',
                'company' => 'PT Lintas Samudra Energi',
                'position' => ['id' => 'Kepala HSE', 'en' => 'Head of HSE'],
                'portfolio' => 'pelatihan-k3-industri-migas',
                'rating' => 5,
                'quote' => [
                    'id' => 'Materinya disusun dari catatan insiden di lokasi kami sendiri. Pekerja mengenali setiap contoh yang dibahas, dan laporan nyaris celaka turun 41% pada semester berikutnya.',
                    'en' => 'The material was built from incident records at our own site. Workers recognised every example discussed, and near-miss reports fell 41% the next half-year.',
                ],
            ],
            [
                'name' => 'Ratna Sari Dewi',
                'company' => 'Bank Pembangunan Daerah Jabar',
                'position' => ['id' => 'Kepala Divisi SDM', 'en' => 'Head of HR Division'],
                'portfolio' => 'talent-mapping-perbankan-daerah',
                'rating' => 5,
                'quote' => [
                    'id' => 'Sepertiga pemegang jabatan kunci kami akan pensiun dalam tiga tahun. Sekarang setiap posisi itu punya minimal dua kandidat pengganti yang sudah disiapkan.',
                    'en' => 'A third of our key post-holders retire within three years. Now every one of those roles has at least two successor candidates already being prepared.',
                ],
            ],
            [
                'name' => 'Teguh Prasetyo',
                'company' => 'PT Sumber Daya Elektrik',
                'position' => ['id' => 'Manajer Teknik', 'en' => 'Engineering Manager'],
                'portfolio' => 'sertifikasi-bnsp-teknisi-listrik',
                'rating' => 5,
                'quote' => [
                    'id' => 'Delapan puluh lima teknisi bersertifikat tanpa perlu keluar kota sama sekali. Biaya sertifikasi angkatan berikutnya turun 38% karena tempat uji kini ada di lokasi kami.',
                    'en' => 'Eighty-five technicians certified with no out-of-town travel at all. The next cohort cost 38% less because the assessment venue is now on our own site.',
                ],
            ],
            [
                'name' => 'Maria Ulfa',
                'company' => 'PT Ritel Maju Bersama',
                'position' => ['id' => 'Manajer Operasional Gerai', 'en' => 'Store Operations Manager'],
                'portfolio' => 'pelatihan-layanan-pelanggan-ritel',
                'rating' => 4,
                'quote' => [
                    'id' => 'Standar layanannya diambil dari praktik gerai terbaik kami sendiri, jadi staf tidak merasa dipaksa mengikuti cara orang luar. Selisih nilai antargerai menyempit dari 34 poin ke 9.',
                    'en' => 'The service standard came from our own best outlets, so staff did not feel forced into an outsider\'s way. The gap between outlets narrowed from 34 points to nine.',
                ],
            ],
            [
                'name' => 'dr. Aditya Ramadhan',
                'company' => 'RS Harapan Sehat',
                'position' => ['id' => 'Direktur Pelayanan Medis', 'en' => 'Medical Services Director'],
                'portfolio' => 'audit-struktur-organisasi-rumah-sakit',
                'rating' => 5,
                'quote' => [
                    'id' => 'Uraian tugas ditulis dari apa yang benar-benar dikerjakan unit kami, bukan dari bagan di atas kertas. Temuan akreditasi soal ini tidak terulang lagi.',
                    'en' => 'Job descriptions were written from what our units actually do, not from a chart on paper. The accreditation finding on this did not recur.',
                ],
            ],
            [
                'name' => 'Iwan Kurniawan',
                'company' => 'PT Konstruksi Bumi Persada',
                'position' => ['id' => 'Manajer Proyek', 'en' => 'Project Manager'],
                'portfolio' => 'sertifikasi-ahli-k3-umum',
                'rating' => 5,
                'quote' => [
                    'id' => 'Dari empat orang Ahli K3 bersertifikat menjadi empat puluh satu. Kami akhirnya memenuhi syarat untuk ikut tender proyek besar yang selama ini tertutup.',
                    'en' => 'From four certified safety experts to forty-one. We finally met the requirements for the large tenders that had been closed to us.',
                ],
            ],
            [
                'name' => 'Nurul Hidayah',
                'company' => 'PT Tekstil Indah Permai',
                'position' => ['id' => 'Manajer Rekrutmen', 'en' => 'Recruitment Manager'],
                'portfolio' => 'rekrutmen-massal-operator-produksi',
                'rating' => 4,
                'quote' => [
                    'id' => 'Dua ribu empat ratus pelamar disaring menjadi tiga ratus operator dalam sembilan pekan. Tingkat bertahan kerja 92% setelah tiga bulan membuktikan seleksinya tepat.',
                    'en' => 'Two thousand four hundred applicants screened down to three hundred operators in nine weeks. A 92% retention rate after three months proved the selection was right.',
                ],
            ],
            [
                'name' => 'Rizky Firmansyah',
                'company' => 'PT Asuransi Wira Sentosa',
                'position' => ['id' => 'Kepala Divisi Klaim', 'en' => 'Head of Claims Division'],
                'portfolio' => 'pelatihan-digital-mindset-asuransi',
                'rating' => 5,
                'quote' => [
                    'id' => 'Pelatihannya tidak dimulai dari fitur sistem, tapi dari kekhawatiran karyawan. Itu yang membuat pemakaian naik dari 23% ke 88% dalam dua bulan.',
                    'en' => 'The training started not with system features but with employees\' worries. That is what took usage from 23% to 88% in two months.',
                ],
            ],
            [
                'name' => 'Surya Hadiningrat',
                'company' => 'PT Pelabuhan Niaga Indonesia',
                'position' => ['id' => 'Manajer Pengembangan SDM', 'en' => 'HR Development Manager'],
                'portfolio' => 'pemetaan-jalur-karier-bumn',
                'rating' => 5,
                'quote' => [
                    'id' => 'Keluhan soal kejelasan karier turun dari peringkat satu ke peringkat tujuh dalam survei keterikatan. Karyawan kini bisa membaca sendiri syarat naik jenjang.',
                    'en' => 'Career clarity complaints dropped from rank one to rank seven in our engagement survey. Employees can now read the advancement requirements themselves.',
                ],
            ],
            [
                'name' => 'Bayu Anggara',
                'company' => 'PT Mineral Jaya Abadi',
                'position' => ['id' => 'Manajer Manajemen Risiko', 'en' => 'Risk Management Manager'],
                'portfolio' => 'konsultasi-manajemen-risiko-tambang',
                'rating' => 4,
                'quote' => [
                    'id' => 'Sebelas catatan risiko departemen akhirnya menjadi satu daftar dengan skala yang sama. Direksi kami baru pertama kali melihat gambaran risiko perusahaan secara utuh.',
                    'en' => 'Eleven departmental risk logs finally became one register on a single scale. Our board saw a whole-company risk picture for the first time.',
                ],
            ],
            [
                'name' => 'Dian Permatasari',
                'company' => 'PT Properti Cendana Group',
                'position' => ['id' => 'Sekretaris Perusahaan', 'en' => 'Corporate Secretary'],
                'portfolio' => 'executive-search-direktur-keuangan',
                'rating' => 5,
                'quote' => [
                    'id' => 'Kandidat dengan pengalaman emiten di sektor properti sangat sedikit. Mereka memperluas pencarian ke sektor bersebelahan dan posisi terisi dalam tiga belas pekan.',
                    'en' => 'Candidates with issuer experience in property are very few. They widened the search to adjacent sectors and the role was filled in thirteen weeks.',
                ],
            ],
        ];
    }
}
