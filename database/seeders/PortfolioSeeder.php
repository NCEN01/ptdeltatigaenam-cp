<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\ServiceCategory;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ServiceCategory::pluck('id', 'slug');

        // Hanya ada satu sampul portofolio lama yang tersisa di disk, jadi kolamnya
        // digabung dengan folder lain supaya 20 proyek tidak memakai gambar yang sama.
        $covers = StoredImages::pool('portfolio', 'services', 'banners', 'agenda', 'blog');
        $gallery = StoredImages::pool('portfolio/gallery', 'agenda', 'services', 'blog', 'banners');

        foreach ($this->items() as $index => $item) {
            $portfolio = Portfolio::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'short_description' => $item['summary'],
                    'content' => [
                        'id' => $this->body($item, 'id'),
                        'en' => $this->body($item, 'en'),
                    ],
                    'client_name' => $item['client'],
                    'location' => $item['location'],
                    'service_category_id' => $categories[$item['category']] ?? null,
                    'cover_image' => StoredImages::pick($covers, $index),
                    'project_date' => Carbon::parse($item['date']),
                    'is_featured' => $index < 4,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            $this->syncGallery($portfolio, $item, $gallery, $index);
        }

        $this->command?->info('Portofolio siap: '.Portfolio::count().' proyek, '
            .$portfolio->newQuery()->withCount('images')->get()->sum('images_count').' foto dokumentasi.');
    }

    /**
     * Foto dokumentasi ditulis ulang setiap kali seeder jalan. Tanpa ini, seeder
     * yang dijalankan dua kali akan menumpuk foto ganda pada proyek yang sama.
     */
    private function syncGallery(Portfolio $portfolio, array $item, array $pool, int $index): void
    {
        $portfolio->images()->delete();

        $captions = $item['gallery'];
        $photos = StoredImages::take($pool, $index * 3, count($captions));

        foreach ($photos as $position => $photo) {
            $portfolio->images()->create([
                'image' => $photo,
                'caption' => $captions[$position],
                'sort_order' => $position + 1,
            ]);
        }
    }

    /** Menyusun isi halaman detail dari fakta-fakta khas tiap proyek. */
    private function body(array $item, string $locale): string
    {
        $isId = $locale === 'id';

        $heading = $isId
            ? ['Latar Belakang', 'Pendekatan Kami', 'Lingkup Pengerjaan', 'Hasil yang Dicapai']
            : ['Background', 'Our Approach', 'Scope of Work', 'Results Achieved'];

        $list = fn (array $rows) => '<ul>'.implode('', array_map(
            fn ($row) => '<li>'.$row[$locale].'</li>',
            $rows,
        )).'</ul>';

        return implode('', [
            '<h2>'.$heading[0].'</h2>',
            '<p>'.$item['challenge'][$locale].'</p>',

            '<h2>'.$heading[1].'</h2>',
            '<p>'.$item['approach'][$locale].'</p>',

            '<h3>'.$heading[2].'</h3>',
            $list($item['scope']),

            '<h2>'.$heading[2 + 1].'</h2>',
            $list($item['results']),

            '<p>'.($isId
                ? 'Pengerjaan ditutup dengan serah terima dokumen, rekaman pelatihan, dan sesi pendampingan lanjutan bagi tim internal '.$item['client'].'.'
                : 'The engagement closed with document handover, training records, and a follow-up coaching session for the internal team at '.$item['client'].'.'
            ).'</p>',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function items(): array
    {
        return [
            [
                'slug' => 'transformasi-human-capital-nasional',
                'title' => ['id' => 'Transformasi Human Capital Nasional', 'en' => 'National Human Capital Transformation'],
                'client' => 'PT Nusantara Jaya',
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'category' => 'konsultasi-manajemen-human-capital',
                'date' => '2025-02-15',
                'summary' => [
                    'id' => 'Merancang ulang strategi dan struktur SDM untuk 1.200+ karyawan yang tersebar di 12 cabang.',
                    'en' => 'Redesigning HR strategy and structure for 1,200+ employees across 12 branches.',
                ],
                'challenge' => [
                    'id' => 'Pertumbuhan cepat membuat struktur organisasi di 12 cabang tidak lagi seragam. Jenjang jabatan berbeda-beda antarwilayah, sehingga rotasi karyawan sulit dilakukan dan biaya gaji tidak dapat dibandingkan antarcabang.',
                    'en' => 'Rapid growth left the organisational structure inconsistent across 12 branches. Job levels differed by region, making staff rotation difficult and payroll costs impossible to compare between branches.',
                ],
                'approach' => [
                    'id' => 'Kami memulai dari pemetaan seluruh jabatan yang ada, lalu menyusun satu kerangka jenjang tunggal yang berlaku nasional sebelum memindahkan karyawan ke dalamnya secara bertahap.',
                    'en' => 'We began by mapping every existing role, then built a single national job-level framework before migrating employees into it in stages.',
                ],
                'scope' => [
                    ['id' => 'Analisis jabatan pada 168 posisi unik di seluruh cabang.', 'en' => 'Job analysis across 168 unique positions in all branches.'],
                    ['id' => 'Penyusunan kerangka jenjang jabatan tunggal 9 tingkat.', 'en' => 'Design of a single nine-tier job-level framework.'],
                    ['id' => 'Pendampingan tim HR pusat selama masa transisi enam bulan.', 'en' => 'Coaching the central HR team through a six-month transition.'],
                ],
                'results' => [
                    ['id' => 'Struktur jabatan seragam di 12 cabang dalam waktu enam bulan.', 'en' => 'Uniform job structure across 12 branches within six months.'],
                    ['id' => 'Waktu proses rotasi antarcabang turun dari 6 pekan menjadi 2 pekan.', 'en' => 'Inter-branch rotation time cut from six weeks to two.'],
                    ['id' => 'Biaya gaji per jenjang dapat dibandingkan antarwilayah untuk pertama kalinya.', 'en' => 'Payroll cost per level became comparable across regions for the first time.'],
                ],
                'gallery' => [
                    ['id' => 'Lokakarya penyelarasan struktur bersama kepala cabang', 'en' => 'Structure alignment workshop with branch heads'],
                    ['id' => 'Sesi validasi analisis jabatan di kantor pusat', 'en' => 'Job analysis validation session at head office'],
                    ['id' => 'Serah terima dokumen kerangka jenjang jabatan', 'en' => 'Handover of the job-level framework documents'],
                ],
            ],
            [
                'slug' => 'program-pelatihan-kepemimpinan',
                'title' => ['id' => 'Program Pelatihan Kepemimpinan Lini', 'en' => 'Line Leadership Development Program'],
                'client' => 'Bank Sinar Mas',
                'location' => 'Jakarta Pusat, DKI Jakarta',
                'category' => 'pelatihan-karyawan',
                'date' => '2025-04-08',
                'summary' => [
                    'id' => 'Membangun 80 pemimpin lini melalui pelatihan berbasis studi kasus selama enam bulan.',
                    'en' => 'Building 80 line leaders through six months of case-study based training.',
                ],
                'challenge' => [
                    'id' => 'Banyak supervisor diangkat karena keahlian teknis, bukan karena kesiapan memimpin. Akibatnya keluhan tim meningkat dan tingkat pengunduran diri di lapisan staf muda ikut naik.',
                    'en' => 'Many supervisors were promoted for technical skill rather than leadership readiness. Team complaints rose and attrition among junior staff climbed with them.',
                ],
                'approach' => [
                    'id' => 'Materi disusun dari kasus nyata yang diambil dari catatan internal bank, bukan contoh generik, sehingga setiap peserta melatih keputusan yang benar-benar akan mereka hadapi.',
                    'en' => 'The material was built from real cases drawn from the bank\'s own records rather than generic examples, so every participant practised decisions they would actually face.',
                ],
                'scope' => [
                    ['id' => 'Delapan modul kelas dengan total 96 jam tatap muka.', 'en' => 'Eight classroom modules totalling 96 contact hours.'],
                    ['id' => 'Proyek perbaikan nyata di unit masing-masing peserta.', 'en' => 'A real improvement project inside each participant\'s own unit.'],
                    ['id' => 'Umpan balik 360 derajat sebelum dan sesudah program.', 'en' => '360-degree feedback before and after the programme.'],
                ],
                'results' => [
                    ['id' => '80 supervisor menyelesaikan program dengan tingkat kehadiran 94%.', 'en' => '80 supervisors completed the programme with 94% attendance.'],
                    ['id' => 'Skor umpan balik bawahan naik rata-rata 1,2 poin dari skala 5.', 'en' => 'Subordinate feedback scores rose an average of 1.2 points out of 5.'],
                    ['id' => '31 proyek perbaikan unit dijalankan sampai selesai.', 'en' => '31 unit improvement projects were carried through to completion.'],
                ],
                'gallery' => [
                    ['id' => 'Simulasi pengambilan keputusan kelompok, angkatan pertama', 'en' => 'Group decision-making simulation, first cohort'],
                    ['id' => 'Presentasi proyek perbaikan di hadapan manajemen', 'en' => 'Improvement project presentation before management'],
                    ['id' => 'Penyerahan sertifikat kelulusan program', 'en' => 'Programme completion certificate handover'],
                ],
            ],
            [
                'slug' => 'rekrutmen-eksekutif-c-level',
                'title' => ['id' => 'Rekrutmen Eksekutif C-Level', 'en' => 'C-Level Executive Search'],
                'client' => 'Global Energi Group',
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'category' => 'headhunter',
                'date' => '2024-11-20',
                'summary' => [
                    'id' => 'Penempatan CFO dan COO melalui proses headhunter yang selektif dan terukur.',
                    'en' => 'Placing a CFO and COO through a selective, measured executive-search process.',
                ],
                'challenge' => [
                    'id' => 'Dua posisi kunci kosong bersamaan menjelang penutupan tahun buku. Pencarian sebelumnya berjalan delapan bulan tanpa hasil karena kandidat yang masuk tidak memiliki pengalaman sektor energi.',
                    'en' => 'Two key posts fell vacant at once as the financial year closed. A previous search had run eight months without result because the candidates lacked energy-sector experience.',
                ],
                'approach' => [
                    'id' => 'Kami menyusun daftar panjang dari pemetaan langsung perusahaan sejenis, bukan dari papan lowongan, lalu melakukan pendekatan tertutup agar proses tidak terbaca pasar.',
                    'en' => 'We built the long list by directly mapping comparable companies rather than posting jobs, then approached candidates confidentially so the process stayed off the market\'s radar.',
                ],
                'scope' => [
                    ['id' => 'Pemetaan 47 kandidat dari 19 perusahaan sektor energi.', 'en' => 'Mapping 47 candidates from 19 energy-sector companies.'],
                    ['id' => 'Asesmen kompetensi dan wawancara berbasis perilaku.', 'en' => 'Competency assessment and behaviour-based interviews.'],
                    ['id' => 'Penelusuran rekam jejak dan pendampingan negosiasi.', 'en' => 'Reference checking and offer-negotiation support.'],
                ],
                'results' => [
                    ['id' => 'Kedua posisi terisi dalam 11 pekan sejak penugasan.', 'en' => 'Both roles filled within 11 weeks of engagement.'],
                    ['id' => 'Lima kandidat final diajukan, seluruhnya lolos tahap direksi.', 'en' => 'Five finalists submitted, all cleared the board stage.'],
                    ['id' => 'Kedua eksekutif masih menjabat setelah masa garansi 12 bulan.', 'en' => 'Both executives remained in post beyond the 12-month guarantee period.'],
                ],
                'gallery' => [
                    ['id' => 'Sesi kalibrasi kriteria bersama komite nominasi', 'en' => 'Criteria calibration session with the nomination committee'],
                    ['id' => 'Wawancara panel tahap akhir', 'en' => 'Final-stage panel interview'],
                ],
            ],
            [
                'slug' => 'sertifikasi-kompetensi-operator-produksi',
                'title' => ['id' => 'Sertifikasi Kompetensi Operator Produksi', 'en' => 'Production Operator Competency Certification'],
                'client' => 'PT Baja Perkasa',
                'location' => 'Cilegon, Banten',
                'category' => 'sertifikasi-kompetensi',
                'date' => '2025-01-10',
                'summary' => [
                    'id' => 'Sertifikasi BNSP bagi 240 operator produksi dalam empat gelombang uji kompetensi.',
                    'en' => 'BNSP certification for 240 production operators across four assessment waves.',
                ],
                'challenge' => [
                    'id' => 'Audit pelanggan mensyaratkan seluruh operator lini bersertifikat dalam satu tahun, sementara produksi tidak boleh berhenti. Jadwal uji harus menyesuaikan pola tiga sif.',
                    'en' => 'A customer audit required every line operator certified within a year, yet production could not stop. The assessment schedule had to fit a three-shift pattern.',
                ],
                'approach' => [
                    'id' => 'Uji kompetensi digelar di tempat kerja pada jam sif masing-masing peserta, sehingga tidak ada lini yang berhenti dan peserta diuji pada mesin yang benar-benar mereka operasikan.',
                    'en' => 'Assessments ran on site during each participant\'s own shift, so no line stopped and operators were tested on the machines they actually run.',
                ],
                'scope' => [
                    ['id' => 'Pelatihan penyegaran 24 jam sebelum uji kompetensi.', 'en' => '24 hours of refresher training ahead of assessment.'],
                    ['id' => 'Uji kompetensi BNSP di tempat kerja untuk 240 operator.', 'en' => 'On-site BNSP assessment for 240 operators.'],
                    ['id' => 'Penyusunan arsip bukti kompetensi untuk keperluan audit.', 'en' => 'Building a competency evidence archive for audit purposes.'],
                ],
                'results' => [
                    ['id' => '228 dari 240 operator dinyatakan kompeten pada percobaan pertama.', 'en' => '228 of 240 operators judged competent on the first attempt.'],
                    ['id' => 'Nol jam produksi hilang selama seluruh rangkaian uji.', 'en' => 'Zero production hours lost across the whole assessment series.'],
                    ['id' => 'Temuan audit pelanggan mengenai kompetensi operator ditutup.', 'en' => 'The customer audit finding on operator competency was closed.'],
                ],
                'gallery' => [
                    ['id' => 'Uji praktik di lini produksi, gelombang kedua', 'en' => 'Practical assessment on the production line, second wave'],
                    ['id' => 'Verifikasi berkas bukti kompetensi peserta', 'en' => 'Verification of participant competency evidence'],
                    ['id' => 'Pengarahan asesor sebelum pelaksanaan uji', 'en' => 'Assessor briefing before the assessment'],
                ],
            ],
            [
                'slug' => 'restrukturisasi-organisasi-manufaktur',
                'title' => ['id' => 'Restrukturisasi Organisasi Manufaktur', 'en' => 'Manufacturing Organisation Restructuring'],
                'client' => 'PT Cipta Mandiri Manufaktur',
                'location' => 'Cikarang, Jawa Barat',
                'category' => 'konsultasi-manajemen',
                'date' => '2024-09-05',
                'summary' => [
                    'id' => 'Merampingkan tujuh lapis struktur menjadi lima tanpa pemutusan hubungan kerja.',
                    'en' => 'Flattening seven management layers to five without any redundancies.',
                ],
                'challenge' => [
                    'id' => 'Keputusan sederhana perlu melewati tujuh lapis persetujuan. Waktu tanggap terhadap keluhan pelanggan rata-rata sembilan hari kerja, jauh di atas janji layanan perusahaan.',
                    'en' => 'A simple decision had to pass seven approval layers. Average response time to customer complaints was nine working days, far beyond the company\'s service promise.',
                ],
                'approach' => [
                    'id' => 'Alih-alih memangkas orang, kami memangkas titik persetujuan: setiap lapis yang tidak menambah keputusan dihapus, dan wewenangnya diturunkan ke jenjang di bawahnya.',
                    'en' => 'Rather than cutting people, we cut approval points: every layer that added no decision was removed and its authority pushed down one level.',
                ],
                'scope' => [
                    ['id' => 'Pemetaan alur persetujuan pada 24 proses inti.', 'en' => 'Mapping approval flows across 24 core processes.'],
                    ['id' => 'Perancangan struktur baru lima lapis beserta matriks wewenang.', 'en' => 'Designing a new five-layer structure with an authority matrix.'],
                    ['id' => 'Penempatan ulang 38 karyawan ke peran yang setara.', 'en' => 'Redeploying 38 employees into equivalent roles.'],
                ],
                'results' => [
                    ['id' => 'Waktu tanggap keluhan pelanggan turun dari 9 hari menjadi 3 hari.', 'en' => 'Complaint response time fell from nine days to three.'],
                    ['id' => 'Tidak ada pemutusan hubungan kerja sepanjang proses.', 'en' => 'No redundancies throughout the process.'],
                    ['id' => 'Biaya overhead manajemen turun 14% dalam setahun.', 'en' => 'Management overhead dropped 14% within a year.'],
                ],
                'gallery' => [
                    ['id' => 'Pemetaan alur persetujuan bersama kepala departemen', 'en' => 'Approval-flow mapping with department heads'],
                    ['id' => 'Sosialisasi struktur baru kepada seluruh karyawan', 'en' => 'Rolling out the new structure to all employees'],
                ],
            ],
            [
                'slug' => 'asesmen-kompetensi-supervisor',
                'title' => ['id' => 'Asesmen Kompetensi Supervisor', 'en' => 'Supervisor Competency Assessment'],
                'client' => 'PT Tirta Anugerah',
                'location' => 'Serang, Banten',
                'category' => 'konsultasi-manajemen-human-capital',
                'date' => '2025-03-12',
                'summary' => [
                    'id' => 'Assessment center bagi 95 supervisor sebagai dasar keputusan promosi dan pengembangan.',
                    'en' => 'An assessment centre for 95 supervisors informing promotion and development decisions.',
                ],
                'challenge' => [
                    'id' => 'Keputusan promosi selama ini bergantung pada penilaian atasan langsung, yang berbeda standar antardepartemen. Beberapa promosi terbukti keliru dan sulit dikoreksi.',
                    'en' => 'Promotion decisions had rested on line-manager judgement, which varied in standard between departments. Several promotions proved wrong and were hard to reverse.',
                ],
                'approach' => [
                    'id' => 'Kami menjalankan assessment center dengan simulasi yang sama bagi semua peserta, dinilai oleh asesor bersertifikat dari luar departemen agar hasilnya setara dan dapat dibandingkan.',
                    'en' => 'We ran an assessment centre using identical simulations for every participant, scored by certified assessors from outside the department so results were comparable.',
                ],
                'scope' => [
                    ['id' => 'Penyusunan kamus kompetensi untuk jenjang supervisor.', 'en' => 'Building a competency dictionary for the supervisor level.'],
                    ['id' => 'Assessment center dua hari untuk 95 peserta.', 'en' => 'A two-day assessment centre for 95 participants.'],
                    ['id' => 'Laporan individual berisi rencana pengembangan.', 'en' => 'Individual reports containing development plans.'],
                ],
                'results' => [
                    ['id' => '95 laporan individual diserahkan lengkap dengan rencana pengembangan.', 'en' => '95 individual reports delivered with development plans.'],
                    ['id' => '22 supervisor teridentifikasi siap naik jenjang dalam setahun.', 'en' => '22 supervisors identified as ready for promotion within a year.'],
                    ['id' => 'Standar penilaian promosi menjadi seragam di seluruh departemen.', 'en' => 'Promotion assessment standards became uniform across departments.'],
                ],
                'gallery' => [
                    ['id' => 'Simulasi in-tray exercise, hari pertama', 'en' => 'In-tray exercise simulation, day one'],
                    ['id' => 'Diskusi kelompok terarah yang diamati asesor', 'en' => 'Assessor-observed focus group discussion'],
                    ['id' => 'Sesi umpan balik individual', 'en' => 'Individual feedback session'],
                ],
            ],
            [
                'slug' => 'pelatihan-k3-industri-migas',
                'title' => ['id' => 'Pelatihan K3 Industri Migas', 'en' => 'Oil & Gas Occupational Safety Training'],
                'client' => 'PT Lintas Samudra Energi',
                'location' => 'Balikpapan, Kalimantan Timur',
                'category' => 'pelatihan-karyawan',
                'date' => '2024-12-02',
                'summary' => [
                    'id' => 'Pelatihan keselamatan kerja bagi 160 pekerja lapangan di dua lokasi operasi.',
                    'en' => 'Safety training for 160 field workers across two operating sites.',
                ],
                'challenge' => [
                    'id' => 'Angka nyaris celaka meningkat pada pekerja kontrak yang berganti setiap enam bulan. Pelatihan induksi yang ada terlalu umum dan tidak menyentuh bahaya spesifik di lokasi.',
                    'en' => 'Near-miss counts rose among contract workers who rotated every six months. The existing induction was too generic to address site-specific hazards.',
                ],
                'approach' => [
                    'id' => 'Materi disusun ulang dari catatan insiden dua tahun terakhir di lokasi itu sendiri, sehingga bahaya yang dibahas adalah bahaya yang benar-benar pernah terjadi di sana.',
                    'en' => 'Material was rebuilt from two years of incident records at the sites themselves, so the hazards discussed were ones that had genuinely occurred there.',
                ],
                'scope' => [
                    ['id' => 'Analisis 214 catatan insiden dan nyaris celaka.', 'en' => 'Analysis of 214 incident and near-miss records.'],
                    ['id' => 'Pelatihan 32 jam di dua lokasi operasi.', 'en' => '32 hours of training at two operating sites.'],
                    ['id' => 'Latihan tanggap darurat dengan skenario lokasi nyata.', 'en' => 'Emergency drills using real site scenarios.'],
                ],
                'results' => [
                    ['id' => 'Laporan nyaris celaka turun 41% pada semester berikutnya.', 'en' => 'Near-miss reports fell 41% the following half-year.'],
                    ['id' => '160 pekerja menyelesaikan pelatihan dan lulus uji akhir.', 'en' => '160 workers completed training and passed the final test.'],
                    ['id' => 'Materi induksi baru dipakai permanen untuk pekerja masuk.', 'en' => 'The new induction material became permanent for incoming workers.'],
                ],
                'gallery' => [
                    ['id' => 'Latihan tanggap darurat kebakaran di area tangki', 'en' => 'Fire emergency drill in the tank area'],
                    ['id' => 'Kelas analisis bahaya bersama pengawas lapangan', 'en' => 'Hazard analysis class with field supervisors'],
                ],
            ],
            [
                'slug' => 'talent-mapping-perbankan-daerah',
                'title' => ['id' => 'Talent Mapping Perbankan Daerah', 'en' => 'Regional Banking Talent Mapping'],
                'client' => 'Bank Pembangunan Daerah Jabar',
                'location' => 'Bandung, Jawa Barat',
                'category' => 'konsultasi-manajemen-human-capital',
                'date' => '2025-05-20',
                'summary' => [
                    'id' => 'Memetakan kesiapan 310 karyawan untuk mengisi 45 posisi kunci dalam tiga tahun.',
                    'en' => 'Mapping 310 employees\' readiness to fill 45 key roles over three years.',
                ],
                'challenge' => [
                    'id' => 'Sepertiga pemegang jabatan kunci akan pensiun dalam tiga tahun, sementara bank belum punya gambaran siapa yang siap menggantikan dan siapa yang masih perlu disiapkan.',
                    'en' => 'A third of key post-holders would retire within three years, yet the bank had no picture of who was ready to step up and who still needed preparing.',
                ],
                'approach' => [
                    'id' => 'Setiap karyawan dinilai pada dua sumbu — kinerja saat ini dan potensi jangka panjang — lalu ditempatkan pada kisi sembilan kotak yang menjadi dasar keputusan pengembangan.',
                    'en' => 'Every employee was rated on two axes — current performance and long-term potential — then placed on a nine-box grid that guided development decisions.',
                ],
                'scope' => [
                    ['id' => 'Penilaian potensi dan kinerja atas 310 karyawan.', 'en' => 'Potential and performance review of 310 employees.'],
                    ['id' => 'Penyusunan peta suksesi untuk 45 posisi kunci.', 'en' => 'Building a succession map for 45 key roles.'],
                    ['id' => 'Rancangan program pengembangan bagi 60 kandidat suksesor.', 'en' => 'Designing a development programme for 60 successor candidates.'],
                ],
                'results' => [
                    ['id' => '45 posisi kunci memiliki minimal dua kandidat pengganti.', 'en' => 'All 45 key roles gained at least two successor candidates.'],
                    ['id' => '60 karyawan masuk program pengembangan terarah.', 'en' => '60 employees entered a targeted development programme.'],
                    ['id' => 'Risiko kekosongan jabatan kunci terpetakan sampai 2028.', 'en' => 'Key-role vacancy risk mapped through to 2028.'],
                ],
                'gallery' => [
                    ['id' => 'Kalibrasi kisi sembilan kotak bersama direksi', 'en' => 'Nine-box grid calibration with the board'],
                    ['id' => 'Wawancara potensi kandidat suksesor', 'en' => 'Potential interview with a successor candidate'],
                    ['id' => 'Paparan peta suksesi kepada komite SDM', 'en' => 'Succession map presentation to the HR committee'],
                ],
            ],
            [
                'slug' => 'sertifikasi-bnsp-teknisi-listrik',
                'title' => ['id' => 'Sertifikasi BNSP Teknisi Listrik', 'en' => 'BNSP Electrical Technician Certification'],
                'client' => 'PT Sumber Daya Elektrik',
                'location' => 'Surabaya, Jawa Timur',
                'category' => 'sertifikasi-kompetensi',
                'date' => '2025-02-28',
                'summary' => [
                    'id' => 'Sertifikasi 85 teknisi listrik tegangan menengah beserta penyiapan tempat uji kompetensi.',
                    'en' => 'Certifying 85 medium-voltage electrical technicians and preparing an assessment venue.',
                ],
                'challenge' => [
                    'id' => 'Regulasi mewajibkan teknisi tegangan menengah bersertifikat, namun tempat uji terdekat berada di luar kota dan mengharuskan teknisi meninggalkan pekerjaan berhari-hari.',
                    'en' => 'Regulation required medium-voltage technicians to hold certificates, but the nearest assessment venue was out of town and meant days away from work.',
                ],
                'approach' => [
                    'id' => 'Kami menyiapkan tempat uji kompetensi di dalam lokasi klien sendiri, termasuk verifikasi peralatan dan pelatihan calon asesor internal, agar biaya uji berikutnya jauh lebih murah.',
                    'en' => 'We set up an assessment venue inside the client\'s own site, including equipment verification and internal assessor training, so future assessments would cost far less.',
                ],
                'scope' => [
                    ['id' => 'Verifikasi kelayakan tempat uji kompetensi di lokasi klien.', 'en' => 'Verifying the on-site assessment venue for eligibility.'],
                    ['id' => 'Pelatihan penyegaran dan uji kompetensi 85 teknisi.', 'en' => 'Refresher training and assessment for 85 technicians.'],
                    ['id' => 'Penyiapan tiga calon asesor internal.', 'en' => 'Preparing three internal assessor candidates.'],
                ],
                'results' => [
                    ['id' => '85 teknisi bersertifikat tanpa perjalanan ke luar kota.', 'en' => '85 technicians certified without any out-of-town travel.'],
                    ['id' => 'Tempat uji kompetensi internal resmi terverifikasi.', 'en' => 'The internal assessment venue was formally verified.'],
                    ['id' => 'Biaya sertifikasi per orang turun 38% untuk angkatan berikutnya.', 'en' => 'Per-person certification cost fell 38% for the next cohort.'],
                ],
                'gallery' => [
                    ['id' => 'Uji praktik panel tegangan menengah', 'en' => 'Medium-voltage panel practical assessment'],
                    ['id' => 'Verifikasi peralatan tempat uji kompetensi', 'en' => 'Assessment venue equipment verification'],
                ],
            ],
            [
                'slug' => 'headhunter-manajer-pabrik-agro',
                'title' => ['id' => 'Pencarian Manajer Pabrik Agroindustri', 'en' => 'Agro-Industry Plant Manager Search'],
                'client' => 'PT Agro Lestari Nusantara',
                'location' => 'Medan, Sumatera Utara',
                'category' => 'headhunter',
                'date' => '2024-10-14',
                'summary' => [
                    'id' => 'Mengisi tiga posisi manajer pabrik di lokasi terpencil yang sulit menarik kandidat.',
                    'en' => 'Filling three plant manager roles at remote sites that struggled to attract candidates.',
                ],
                'challenge' => [
                    'id' => 'Tiga pabrik berada jauh dari pusat kota dan kandidat berpengalaman umumnya menolak relokasi. Dua lowongan sudah terbuka lebih dari setahun.',
                    'en' => 'Three plants sat far from any city and experienced candidates generally refused relocation. Two vacancies had been open for over a year.',
                ],
                'approach' => [
                    'id' => 'Kami memusatkan pencarian pada kandidat yang berasal dari wilayah itu dan ingin kembali, lalu membantu klien menyusun paket relokasi keluarga yang menjawab keberatan utama mereka.',
                    'en' => 'We focused the search on candidates originally from the region who wanted to return, then helped the client build a family relocation package answering their main objection.',
                ],
                'scope' => [
                    ['id' => 'Pemetaan kandidat berlatar daerah asal di tiga provinsi.', 'en' => 'Mapping regionally-rooted candidates across three provinces.'],
                    ['id' => 'Kajian pembanding paket remunerasi dan relokasi.', 'en' => 'Benchmarking remuneration and relocation packages.'],
                    ['id' => 'Wawancara berbasis perilaku dan penelusuran rekam jejak.', 'en' => 'Behaviour-based interviews and reference checks.'],
                ],
                'results' => [
                    ['id' => 'Tiga posisi terisi dalam 14 pekan setelah setahun kosong.', 'en' => 'Three roles filled in 14 weeks after a year of vacancy.'],
                    ['id' => 'Seluruh kandidat terpilih menerima penempatan tanpa negosiasi ulang.', 'en' => 'Every selected candidate accepted placement without renegotiation.'],
                    ['id' => 'Paket relokasi baru dipakai ulang untuk rekrutmen berikutnya.', 'en' => 'The new relocation package was reused for later hiring.'],
                ],
                'gallery' => [
                    ['id' => 'Kunjungan lokasi pabrik bersama kandidat final', 'en' => 'Plant site visit with final candidates'],
                    ['id' => 'Wawancara panel bersama direksi operasional', 'en' => 'Panel interview with the operations board'],
                ],
            ],
            [
                'slug' => 'penyusunan-kpi-dan-sistem-remunerasi',
                'title' => ['id' => 'Penyusunan KPI dan Sistem Remunerasi', 'en' => 'KPI and Remuneration System Design'],
                'client' => 'PT Karya Bangun Sejahtera',
                'location' => 'Semarang, Jawa Tengah',
                'category' => 'konsultasi-manajemen',
                'date' => '2025-06-18',
                'summary' => [
                    'id' => 'Menghubungkan sasaran perusahaan ke KPI individu dan struktur gaji yang transparan.',
                    'en' => 'Linking company targets to individual KPIs and a transparent pay structure.',
                ],
                'challenge' => [
                    'id' => 'Kenaikan gaji diberikan merata setiap tahun tanpa kaitan dengan pencapaian. Karyawan berkinerja tinggi merasa tidak dihargai dan beberapa di antaranya mengundurkan diri.',
                    'en' => 'Pay rises were granted evenly each year with no link to achievement. High performers felt unrecognised and several resigned.',
                ],
                'approach' => [
                    'id' => 'Sasaran perusahaan diturunkan berjenjang sampai ke KPI individu, lalu struktur gaji disusun ulang dengan rentang yang jelas sehingga setiap orang tahu posisinya dan apa yang menaikkannya.',
                    'en' => 'Company targets were cascaded down to individual KPIs, then the pay structure was rebuilt with clear ranges so everyone knew where they stood and what would move them.',
                ],
                'scope' => [
                    ['id' => 'Penurunan sasaran perusahaan ke 14 departemen.', 'en' => 'Cascading company targets to 14 departments.'],
                    ['id' => 'Penyusunan KPI individu untuk 9 jenjang jabatan.', 'en' => 'Designing individual KPIs for nine job levels.'],
                    ['id' => 'Kajian pembanding gaji terhadap pasar industri sejenis.', 'en' => 'Salary benchmarking against comparable industry data.'],
                ],
                'results' => [
                    ['id' => 'Rentang gaji tiap jenjang terdokumentasi dan dibuka ke karyawan.', 'en' => 'Pay ranges per level documented and disclosed to employees.'],
                    ['id' => 'Pengunduran diri sukarela turun dari 18% menjadi 11% setahun.', 'en' => 'Voluntary attrition fell from 18% to 11% a year.'],
                    ['id' => 'Anggaran kenaikan gaji dialihkan ke pencapaian, bukan masa kerja.', 'en' => 'The pay-rise budget shifted to achievement rather than tenure.'],
                ],
                'gallery' => [
                    ['id' => 'Lokakarya penurunan sasaran bersama kepala departemen', 'en' => 'Target cascading workshop with department heads'],
                    ['id' => 'Paparan struktur remunerasi baru kepada manajemen', 'en' => 'New remuneration structure presented to management'],
                ],
            ],
            [
                'slug' => 'pelatihan-layanan-pelanggan-ritel',
                'title' => ['id' => 'Pelatihan Layanan Pelanggan Ritel', 'en' => 'Retail Customer Service Training'],
                'client' => 'PT Ritel Maju Bersama',
                'location' => 'Tangerang, Banten',
                'category' => 'pelatihan-karyawan',
                'date' => '2025-07-09',
                'summary' => [
                    'id' => 'Menyeragamkan mutu layanan 420 staf toko di 35 gerai melalui satu standar layanan.',
                    'en' => 'Standardising service quality for 420 store staff across 35 outlets.',
                ],
                'challenge' => [
                    'id' => 'Nilai kepuasan pelanggan berbeda jauh antargerai. Gerai terbaik dan terburuk terpaut 34 poin, padahal produk dan harga yang dijual sama persis.',
                    'en' => 'Customer satisfaction varied sharply between outlets. The best and worst were 34 points apart despite identical products and pricing.',
                ],
                'approach' => [
                    'id' => 'Kami mengamati langsung praktik di gerai berkinerja terbaik, mengangkatnya menjadi standar tertulis, lalu melatihkannya ke seluruh gerai melalui pelatih internal.',
                    'en' => 'We observed practice directly in the best-performing outlets, turned it into a written standard, then trained it across all outlets through internal trainers.',
                ],
                'scope' => [
                    ['id' => 'Observasi lapangan di 8 gerai berkinerja tertinggi dan terendah.', 'en' => 'Field observation in the eight highest and lowest performing outlets.'],
                    ['id' => 'Penyusunan standar layanan dan naskah penanganan keluhan.', 'en' => 'Writing service standards and complaint-handling scripts.'],
                    ['id' => 'Pelatihan 28 pelatih internal untuk penyebaran mandiri.', 'en' => 'Training 28 internal trainers for self-sustained rollout.'],
                ],
                'results' => [
                    ['id' => 'Selisih nilai kepuasan antargerai menyempit dari 34 ke 9 poin.', 'en' => 'The outlet satisfaction gap narrowed from 34 points to nine.'],
                    ['id' => '420 staf toko menyelesaikan pelatihan dalam 11 pekan.', 'en' => '420 store staff completed training within 11 weeks.'],
                    ['id' => 'Penyegaran tahunan dijalankan mandiri oleh pelatih internal.', 'en' => 'Annual refreshers now run independently by internal trainers.'],
                ],
                'gallery' => [
                    ['id' => 'Praktik penanganan keluhan pelanggan di kelas', 'en' => 'Complaint-handling practice in class'],
                    ['id' => 'Observasi layanan langsung di gerai', 'en' => 'Live service observation in an outlet'],
                    ['id' => 'Pembekalan pelatih internal angkatan pertama', 'en' => 'First-cohort internal trainer briefing'],
                ],
            ],
            [
                'slug' => 'audit-struktur-organisasi-rumah-sakit',
                'title' => ['id' => 'Audit Struktur Organisasi Rumah Sakit', 'en' => 'Hospital Organisational Structure Audit'],
                'client' => 'RS Harapan Sehat',
                'location' => 'Yogyakarta, DI Yogyakarta',
                'category' => 'konsultasi-manajemen',
                'date' => '2024-08-22',
                'summary' => [
                    'id' => 'Menata ulang pembagian tugas 18 unit layanan menjelang akreditasi rumah sakit.',
                    'en' => 'Reorganising duty allocation across 18 service units ahead of hospital accreditation.',
                ],
                'challenge' => [
                    'id' => 'Uraian tugas banyak jabatan sudah tidak sesuai praktik sehari-hari, dan sebagian tanggung jawab tumpang tindih antarunit. Ini menjadi temuan berulang pada penilaian akreditasi sebelumnya.',
                    'en' => 'Many job descriptions no longer matched daily practice and some responsibilities overlapped between units — a repeat finding in the previous accreditation review.',
                ],
                'approach' => [
                    'id' => 'Kami menelusuri apa yang benar-benar dikerjakan tiap unit lebih dulu, baru menulis ulang uraian tugas dari kenyataan itu, bukan dari struktur di atas kertas.',
                    'en' => 'We traced what each unit actually did first, then rewrote job descriptions from that reality rather than from the structure on paper.',
                ],
                'scope' => [
                    ['id' => 'Wawancara dan observasi kerja pada 18 unit layanan.', 'en' => 'Interviews and work observation across 18 service units.'],
                    ['id' => 'Penulisan ulang 112 uraian tugas jabatan.', 'en' => 'Rewriting 112 job descriptions.'],
                    ['id' => 'Penyusunan matriks tanggung jawab antarunit.', 'en' => 'Building an inter-unit responsibility matrix.'],
                ],
                'results' => [
                    ['id' => '112 uraian tugas selesai dan disahkan direksi.', 'en' => '112 job descriptions completed and ratified by the board.'],
                    ['id' => 'Tumpang tindih tanggung jawab antarunit terurai seluruhnya.', 'en' => 'All inter-unit responsibility overlaps were resolved.'],
                    ['id' => 'Temuan akreditasi mengenai uraian tugas tidak terulang.', 'en' => 'The accreditation finding on job descriptions did not recur.'],
                ],
                'gallery' => [
                    ['id' => 'Observasi alur kerja di unit rawat jalan', 'en' => 'Workflow observation in the outpatient unit'],
                    ['id' => 'Validasi uraian tugas bersama kepala unit', 'en' => 'Job description validation with unit heads'],
                ],
            ],
            [
                'slug' => 'sertifikasi-ahli-k3-umum',
                'title' => ['id' => 'Sertifikasi Ahli K3 Umum', 'en' => 'General Occupational Safety Expert Certification'],
                'client' => 'PT Konstruksi Bumi Persada',
                'location' => 'Makassar, Sulawesi Selatan',
                'category' => 'sertifikasi-kompetensi',
                'date' => '2025-04-25',
                'summary' => [
                    'id' => 'Menyiapkan 40 pengawas proyek memperoleh sertifikat Ahli K3 Umum.',
                    'en' => 'Preparing 40 project supervisors to obtain General Safety Expert certification.',
                ],
                'challenge' => [
                    'id' => 'Setiap proyek baru mensyaratkan sejumlah Ahli K3 bersertifikat, sementara perusahaan hanya punya empat orang. Keterbatasan ini mulai menutup peluang ikut tender besar.',
                    'en' => 'Every new project required a quota of certified safety experts while the company had only four. The shortfall was starting to close off large tenders.',
                ],
                'approach' => [
                    'id' => 'Pelatihan dijalankan dalam pola dua pekan penuh agar peserta dari berbagai proyek bisa dikumpulkan sekaligus, dengan pendalaman soal berbasis kasus proyek konstruksi mereka sendiri.',
                    'en' => 'Training ran as two full weeks so participants from different projects could be gathered at once, with exam practice built on their own construction cases.',
                ],
                'scope' => [
                    ['id' => 'Pelatihan 120 jam sesuai kurikulum Ahli K3 Umum.', 'en' => '120 hours of training following the safety expert curriculum.'],
                    ['id' => 'Pendalaman soal dan simulasi ujian sertifikasi.', 'en' => 'Exam drilling and certification mock tests.'],
                    ['id' => 'Pendampingan penyusunan laporan praktik kerja lapangan.', 'en' => 'Support in preparing field practice reports.'],
                ],
                'results' => [
                    ['id' => '37 dari 40 peserta lulus sertifikasi pada percobaan pertama.', 'en' => '37 of 40 participants passed certification at the first attempt.'],
                    ['id' => 'Jumlah Ahli K3 bersertifikat naik dari 4 menjadi 41 orang.', 'en' => 'Certified safety experts rose from four to 41.'],
                    ['id' => 'Perusahaan memenuhi syarat K3 untuk tender proyek besar.', 'en' => 'The company met safety requirements for large project tenders.'],
                ],
                'gallery' => [
                    ['id' => 'Kelas pendalaman materi Ahli K3 Umum', 'en' => 'General safety expert intensive class'],
                    ['id' => 'Praktik inspeksi K3 di lokasi proyek', 'en' => 'Safety inspection practice on the project site'],
                    ['id' => 'Simulasi ujian sertifikasi', 'en' => 'Certification mock examination'],
                ],
            ],
            [
                'slug' => 'rekrutmen-massal-operator-produksi',
                'title' => ['id' => 'Rekrutmen Massal Operator Produksi', 'en' => 'Mass Production Operator Recruitment'],
                'client' => 'PT Tekstil Indah Permai',
                'location' => 'Karawang, Jawa Barat',
                'category' => 'headhunter',
                'date' => '2025-03-30',
                'summary' => [
                    'id' => 'Menyaring 2.400 pelamar menjadi 300 operator terpilih untuk pembukaan lini baru.',
                    'en' => 'Screening 2,400 applicants down to 300 selected operators for a new production line.',
                ],
                'challenge' => [
                    'id' => 'Lini produksi baru harus berjalan dalam sepuluh pekan dan membutuhkan 300 operator sekaligus. Proses seleksi manual perusahaan tidak sanggup menangani volume sebesar itu.',
                    'en' => 'A new line had to start within ten weeks and needed 300 operators at once. The company\'s manual selection process could not handle that volume.',
                ],
                'approach' => [
                    'id' => 'Seleksi dibagi menjadi tahap penyaringan cepat berbasis syarat mutlak lalu uji ketelitian praktis, sehingga waktu wawancara hanya dipakai untuk kandidat yang sudah lolos dua saringan.',
                    'en' => 'Selection was split into a fast knock-out screen on hard requirements then a practical dexterity test, so interview time was spent only on candidates past two filters.',
                ],
                'scope' => [
                    ['id' => 'Penyaringan administratif 2.400 pelamar.', 'en' => 'Administrative screening of 2,400 applicants.'],
                    ['id' => 'Uji ketelitian dan ketahanan kerja untuk 780 kandidat.', 'en' => 'Dexterity and work-endurance testing for 780 candidates.'],
                    ['id' => 'Wawancara akhir dan pemeriksaan kesehatan 340 kandidat.', 'en' => 'Final interviews and medical checks for 340 candidates.'],
                ],
                'results' => [
                    ['id' => '300 operator terpilih dan mulai bekerja tepat waktu.', 'en' => '300 operators selected and started on schedule.'],
                    ['id' => 'Seluruh proses selesai dalam 9 pekan dari 10 pekan tersedia.', 'en' => 'The whole process finished in nine of the ten weeks available.'],
                    ['id' => 'Tingkat bertahan kerja 92% setelah tiga bulan pertama.', 'en' => '92% retention after the first three months.'],
                ],
                'gallery' => [
                    ['id' => 'Uji ketelitian kerja tahap kedua', 'en' => 'Second-stage dexterity testing'],
                    ['id' => 'Pengarahan awal operator terpilih', 'en' => 'Initial briefing for selected operators'],
                ],
            ],
            [
                'slug' => 'pelatihan-digital-mindset-asuransi',
                'title' => ['id' => 'Pelatihan Pola Pikir Digital Asuransi', 'en' => 'Insurance Digital Mindset Training'],
                'client' => 'PT Asuransi Wira Sentosa',
                'location' => 'Jakarta Barat, DKI Jakarta',
                'category' => 'pelatihan-karyawan',
                'date' => '2025-08-11',
                'summary' => [
                    'id' => 'Menyiapkan 210 karyawan menghadapi peralihan proses klaim ke sistem digital.',
                    'en' => 'Preparing 210 employees for the shift of claims processing to a digital system.',
                ],
                'challenge' => [
                    'id' => 'Sistem klaim digital sudah dibeli tetapi pemakaiannya rendah. Sebagian besar karyawan tetap memproses klaim dengan berkas kertas karena merasa lebih aman.',
                    'en' => 'A digital claims system had been purchased but adoption was low. Most staff kept processing claims on paper because it felt safer.',
                ],
                'approach' => [
                    'id' => 'Pelatihan tidak dimulai dari fitur sistem, melainkan dari kekhawatiran karyawan terhadap perubahan, baru kemudian menunjukkan bahwa proses digital menyelesaikan kekhawatiran itu.',
                    'en' => 'Training began not with system features but with employees\' worries about the change, then showed how the digital process addressed those worries.',
                ],
                'scope' => [
                    ['id' => 'Survei hambatan adopsi pada 210 karyawan.', 'en' => 'Adoption-barrier survey across 210 employees.'],
                    ['id' => 'Pelatihan 24 jam dengan praktik langsung pada sistem.', 'en' => '24 hours of training with hands-on system practice.'],
                    ['id' => 'Pendampingan di meja kerja selama empat pekan pertama.', 'en' => 'Desk-side support during the first four weeks.'],
                ],
                'results' => [
                    ['id' => 'Pemakaian sistem digital naik dari 23% ke 88% dalam dua bulan.', 'en' => 'Digital system usage rose from 23% to 88% within two months.'],
                    ['id' => 'Waktu proses klaim rata-rata turun dari 5 hari ke 2 hari.', 'en' => 'Average claim processing time fell from five days to two.'],
                    ['id' => 'Pemakaian berkas kertas untuk klaim dihentikan sepenuhnya.', 'en' => 'Paper-based claim files were discontinued entirely.'],
                ],
                'gallery' => [
                    ['id' => 'Praktik langsung sistem klaim digital', 'en' => 'Hands-on practice with the digital claims system'],
                    ['id' => 'Pendampingan di meja kerja pekan pertama', 'en' => 'Desk-side support in the first week'],
                ],
            ],
            [
                'slug' => 'pemetaan-jalur-karier-bumn',
                'title' => ['id' => 'Pemetaan Jalur Karier BUMN', 'en' => 'State-Owned Enterprise Career Path Mapping'],
                'client' => 'PT Pelabuhan Niaga Indonesia',
                'location' => 'Surabaya, Jawa Timur',
                'category' => 'konsultasi-manajemen-human-capital',
                'date' => '2024-07-16',
                'summary' => [
                    'id' => 'Menyusun jalur karier yang jelas bagi 640 karyawan pada enam rumpun jabatan.',
                    'en' => 'Designing clear career paths for 640 employees across six job families.',
                ],
                'challenge' => [
                    'id' => 'Karyawan tidak mengetahui syarat untuk naik jenjang, sehingga perpindahan jabatan dianggap ditentukan kedekatan. Survei keterikatan menempatkan kejelasan karier sebagai keluhan tertinggi.',
                    'en' => 'Employees did not know what was required to advance, so role changes were seen as decided by proximity. The engagement survey ranked career clarity as the top complaint.',
                ],
                'approach' => [
                    'id' => 'Setiap rumpun jabatan dipetakan menjadi jalur bertingkat dengan syarat kompetensi dan pengalaman yang tertulis, lalu dipublikasikan agar setiap karyawan bisa membacanya sendiri.',
                    'en' => 'Each job family was mapped into a tiered path with written competency and experience requirements, then published so every employee could read it directly.',
                ],
                'scope' => [
                    ['id' => 'Pengelompokan 640 jabatan ke dalam enam rumpun.', 'en' => 'Grouping 640 positions into six job families.'],
                    ['id' => 'Penetapan syarat kompetensi tiap tingkat jalur karier.', 'en' => 'Defining competency requirements for each career tier.'],
                    ['id' => 'Penyusunan panduan karier yang dibagikan ke seluruh karyawan.', 'en' => 'Producing a career guide distributed to all employees.'],
                ],
                'results' => [
                    ['id' => 'Enam jalur karier resmi terbit dan dapat diakses karyawan.', 'en' => 'Six official career paths published and accessible to employees.'],
                    ['id' => 'Keluhan kejelasan karier turun dari peringkat 1 ke peringkat 7.', 'en' => 'Career clarity complaints dropped from rank one to rank seven.'],
                    ['id' => 'Permohonan mutasi yang sesuai syarat naik dua kali lipat.', 'en' => 'Qualifying transfer applications doubled.'],
                ],
                'gallery' => [
                    ['id' => 'Lokakarya pengelompokan rumpun jabatan', 'en' => 'Job family grouping workshop'],
                    ['id' => 'Sosialisasi panduan karier kepada karyawan', 'en' => 'Career guide briefing for employees'],
                ],
            ],
            [
                'slug' => 'konsultasi-manajemen-risiko-tambang',
                'title' => ['id' => 'Konsultasi Manajemen Risiko Tambang', 'en' => 'Mining Risk Management Consulting'],
                'client' => 'PT Mineral Jaya Abadi',
                'location' => 'Samarinda, Kalimantan Timur',
                'category' => 'konsultasi-manajemen',
                'date' => '2025-05-06',
                'summary' => [
                    'id' => 'Membangun kerangka manajemen risiko operasional untuk tiga wilayah tambang.',
                    'en' => 'Building an operational risk management framework for three mining areas.',
                ],
                'challenge' => [
                    'id' => 'Risiko dicatat terpisah oleh masing-masing departemen dengan format berbeda. Manajemen tidak pernah melihat gambaran risiko perusahaan secara utuh dalam satu dokumen.',
                    'en' => 'Risks were logged separately by each department in different formats. Management never saw a whole-company risk picture in a single document.',
                ],
                'approach' => [
                    'id' => 'Kami menyatukan seluruh catatan risiko ke dalam satu daftar dengan ukuran dampak dan kemungkinan yang sama, sehingga risiko dari departemen berbeda akhirnya dapat diperingkat bersama.',
                    'en' => 'We merged every risk log into a single register using one impact and likelihood scale, so risks from different departments could finally be ranked together.',
                ],
                'scope' => [
                    ['id' => 'Penggabungan 11 catatan risiko departemen menjadi satu daftar.', 'en' => 'Merging 11 departmental risk logs into one register.'],
                    ['id' => 'Penetapan skala dampak dan kemungkinan yang seragam.', 'en' => 'Setting a uniform impact and likelihood scale.'],
                    ['id' => 'Pelatihan pemilik risiko di tiga wilayah tambang.', 'en' => 'Training risk owners across three mining areas.'],
                ],
                'results' => [
                    ['id' => 'Satu daftar risiko perusahaan berisi 148 risiko terperingkat.', 'en' => 'A single corporate register holding 148 ranked risks.'],
                    ['id' => '12 risiko tingkat tinggi memperoleh rencana mitigasi bernama penanggung jawab.', 'en' => '12 high-level risks gained mitigation plans with named owners.'],
                    ['id' => 'Tinjauan risiko menjadi agenda tetap rapat direksi triwulanan.', 'en' => 'Risk review became a standing item in quarterly board meetings.'],
                ],
                'gallery' => [
                    ['id' => 'Lokakarya identifikasi risiko wilayah tambang', 'en' => 'Mining area risk identification workshop'],
                    ['id' => 'Peninjauan pengendalian risiko di lapangan', 'en' => 'Field review of risk controls'],
                ],
            ],
            [
                'slug' => 'sertifikasi-juru-las-industri',
                'title' => ['id' => 'Sertifikasi Juru Las Industri', 'en' => 'Industrial Welder Certification'],
                'client' => 'PT Fabrikasi Logam Nusantara',
                'location' => 'Batam, Kepulauan Riau',
                'category' => 'sertifikasi-kompetensi',
                'date' => '2024-11-08',
                'summary' => [
                    'id' => 'Sertifikasi 120 juru las pada tiga posisi pengelasan sesuai permintaan pelanggan ekspor.',
                    'en' => 'Certifying 120 welders in three welding positions as required by export customers.',
                ],
                'challenge' => [
                    'id' => 'Pelanggan ekspor mensyaratkan sertifikat juru las yang diakui untuk setiap posisi pengelasan. Tanpa itu, pesanan bernilai besar tidak dapat diambil.',
                    'en' => 'Export customers required recognised welder certificates for each welding position. Without them, high-value orders could not be taken.',
                ],
                'approach' => [
                    'id' => 'Kemampuan setiap juru las diuji lebih dulu untuk menentukan siapa yang siap langsung diuji dan siapa yang perlu latihan tambahan, agar biaya pelatihan tidak dikeluarkan merata tanpa perlu.',
                    'en' => 'Every welder\'s skill was pre-tested to decide who could go straight to assessment and who needed extra practice, so training spend was not spread evenly without need.',
                ],
                'scope' => [
                    ['id' => 'Uji kemampuan awal 120 juru las pada tiga posisi.', 'en' => 'Initial skill testing of 120 welders in three positions.'],
                    ['id' => 'Pelatihan tambahan bagi 44 juru las yang belum siap.', 'en' => 'Extra training for 44 welders who were not yet ready.'],
                    ['id' => 'Uji kompetensi dan uji hasil las oleh pihak ketiga.', 'en' => 'Competency and weld-quality testing by a third party.'],
                ],
                'results' => [
                    ['id' => '114 juru las memperoleh sertifikat untuk minimal dua posisi.', 'en' => '114 welders certified in at least two positions.'],
                    ['id' => 'Biaya pelatihan 37% lebih rendah dibanding rencana awal.', 'en' => 'Training cost came in 37% below the original plan.'],
                    ['id' => 'Pesanan ekspor yang tertunda dapat mulai dikerjakan.', 'en' => 'The held export orders could finally begin production.'],
                ],
                'gallery' => [
                    ['id' => 'Uji praktik pengelasan posisi vertikal', 'en' => 'Vertical position welding practical test'],
                    ['id' => 'Pemeriksaan mutu hasil las oleh penguji', 'en' => 'Weld quality inspection by the examiner'],
                    ['id' => 'Pelatihan tambahan bagi peserta gelombang kedua', 'en' => 'Additional training for the second-wave participants'],
                ],
            ],
            [
                'slug' => 'executive-search-direktur-keuangan',
                'title' => ['id' => 'Pencarian Direktur Keuangan', 'en' => 'Finance Director Search'],
                'client' => 'PT Properti Cendana Group',
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'category' => 'headhunter',
                'date' => '2025-09-02',
                'summary' => [
                    'id' => 'Mencari direktur keuangan berpengalaman properti menjelang rencana penawaran saham perdana.',
                    'en' => 'Finding a finance director with property experience ahead of a planned public offering.',
                ],
                'challenge' => [
                    'id' => 'Perusahaan bersiap melantai di bursa dan membutuhkan direktur keuangan yang pernah melalui proses serupa. Jumlah kandidat dengan pengalaman itu di sektor properti sangat terbatas.',
                    'en' => 'The company was preparing to list and needed a finance director who had been through the process. Candidates with that experience in property were very few.',
                ],
                'approach' => [
                    'id' => 'Pencarian diperluas ke sektor bersebelahan yang memiliki pola pelaporan serupa, lalu setiap kandidat diuji pemahamannya atas persyaratan keterbukaan informasi emiten.',
                    'en' => 'The search widened to adjacent sectors with similar reporting patterns, then every candidate was tested on issuer disclosure requirements.',
                ],
                'scope' => [
                    ['id' => 'Pemetaan 31 kandidat dari sektor properti dan infrastruktur.', 'en' => 'Mapping 31 candidates from property and infrastructure.'],
                    ['id' => 'Asesmen kesiapan menghadapi proses penawaran saham perdana.', 'en' => 'Assessing readiness for the public offering process.'],
                    ['id' => 'Pendampingan negosiasi paket dan masa transisi.', 'en' => 'Package negotiation and transition support.'],
                ],
                'results' => [
                    ['id' => 'Posisi terisi dalam 13 pekan dengan kandidat berpengalaman emiten.', 'en' => 'The role was filled in 13 weeks by a candidate with issuer experience.'],
                    ['id' => 'Empat kandidat final memenuhi seluruh syarat mutlak pemegang saham.', 'en' => 'All four finalists met every shareholder hard requirement.'],
                    ['id' => 'Masa transisi berjalan tanpa kekosongan fungsi keuangan.', 'en' => 'The handover ran with no gap in the finance function.'],
                ],
                'gallery' => [
                    ['id' => 'Wawancara kandidat bersama pemegang saham', 'en' => 'Candidate interview with shareholders'],
                    ['id' => 'Sesi penyelarasan kriteria jabatan', 'en' => 'Role criteria alignment session'],
                ],
            ],
        ];
    }
}
