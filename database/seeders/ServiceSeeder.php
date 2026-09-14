<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ServiceCategory::pluck('id', 'slug');
        $images = StoredImages::pool('services', 'service-categories', 'banners', 'agenda');

        foreach ($this->services() as $index => $item) {
            $service = Service::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'service_category_id' => $categories[$item['category']] ?? $categories->first(),
                    'title' => $item['title'],
                    'short_description' => $item['summary'],
                    'description' => [
                        'id' => $this->body($item, 'id'),
                        'en' => $this->body($item, 'en'),
                    ],
                    'image' => StoredImages::pick($images, $index),
                    // Satuan harga. Dikosongkan untuk pelatihan dan sertifikasi karena
                    // halaman layanan sudah memakai "peserta" sebagai cadangan.
                    'price_label' => $item['price_unit'] ?? null,
                    'price' => $item['price'],
                    // Diskon di CMS bersifat tampilan saja: `price` tetap harga yang
                    // ditagih, `discount_original_price` adalah angka coret di atasnya.
                    // Keduanya ditulis eksplisit (termasuk false/null) supaya seeder
                    // yang dijalankan ulang ikut membersihkan diskon yang dicabut.
                    'discount_active' => isset($item['price_before']),
                    'discount_original_price' => $item['price_before'] ?? null,
                    'duration' => $item['duration'],
                    'location' => $item['location'],
                    'mode' => $item['mode'],
                    'quota' => $item['quota'],
                    'seats_taken' => $item['taken'],
                    // Layanan konsultasi berharga 0 tidak dijual per kursi, jadi
                    // tombol "Daftar Sekarang" tidak ditawarkan untuk itu.
                    'is_purchasable' => $item['price'] > 0,
                    'is_featured' => $index < 4,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    // Tanpa ini seluruh layanan punya created_at yang sama persis,
                    // dan blok "Pelatihan Terbaru" di beranda memakai latest() —
                    // artinya 6 dari 10 layanan terpilih secara acak dan berubah-ubah.
                    // Diberi jarak sehari agar urutannya tetap dan sesuai sort_order.
                    'created_at' => Carbon::today()->subDays($index)->setTime(9, 0),
                ],
            );

            $this->syncActivities($service, $item);
            $this->syncSchedules($service, $item, $index);
        }

        $this->command?->info('Layanan siap: '.Service::count().' layanan, '
            .\App\Models\ServiceSchedule::count().' jadwal, '
            .\App\Models\ServiceActivity::count().' rincian kegiatan.');
    }

    /** Ditulis ulang setiap kali seeder jalan supaya tidak menumpuk baris ganda. */
    private function syncActivities(Service $service, array $item): void
    {
        $service->activities()->delete();

        foreach ($item['activities'] as $position => $activity) {
            $service->activities()->create([
                'title' => $activity['title'],
                'description' => $activity['description'],
                'sort_order' => $position + 1,
            ]);
        }
    }

    /**
     * Jadwal selalu di masa depan dan dihitung dari hari ini, supaya halaman
     * pendaftaran tidak pernah tampil basi berapa pun lama seeder ini tidak
     * dijalankan ulang.
     *
     * Pelatihan dan sertifikasi dibuka dalam beberapa angkatan, jadi tiga batch
     * masuk akal. Headhunter dan konsultasi dikerjakan per penugasan — menawarkan
     * tiga batch dengan harga dan kuota yang persis sama hanya mengulang kartu
     * yang serupa, jadi layanan itu diberi satu batch saja lewat kunci 'batches'.
     */
    private function syncSchedules(Service $service, array $item, int $index): void
    {
        $service->schedules()->delete();

        if ($item['quota'] === null) {
            return;
        }

        // Jarak antarbatch mengikuti lama programnya. Penugasan headhunter dan
        // konsultasi berjalan 70–120 hari; jarak tetap 35 hari akan membuat
        // batch berikutnya mulai sebelum yang sebelumnya selesai.
        $gap = max(35, $item['days'] + 21);

        foreach (range(0, ($item['batches'] ?? 3) - 1) as $n) {
            $start = Carbon::today()->addDays(21 + ($index * 4) + ($n * $gap));

            $service->schedules()->create([
                'start_date' => $start,
                'end_date' => $start->copy()->addDays($item['days'] - 1),
                'start_time' => '08:30:00',
                'end_time' => '16:30:00',
                'location' => $item['location'],
                'mode' => $item['mode'],
                'quota' => $item['quota'],
                'seats_taken' => max(0, $item['taken'] - ($n * 4)),
                'is_active' => true,
            ]);
        }
    }

    private function body(array $item, string $locale): string
    {
        $isId = $locale === 'id';

        $heading = $isId
            ? ['Tentang Program Ini', 'Untuk Siapa Program Ini', 'Yang Anda Dapatkan']
            : ['About This Programme', 'Who This Is For', 'What You Receive'];

        $list = fn (array $rows) => '<ul>'.implode('', array_map(
            fn ($row) => '<li>'.$row[$locale].'</li>',
            $rows,
        )).'</ul>';

        return implode('', [
            '<h2>'.$heading[0].'</h2>',
            '<p>'.$item['about'][$locale].'</p>',

            '<h2>'.$heading[1].'</h2>',
            '<p>'.$item['audience'][$locale].'</p>',

            '<h2>'.$heading[2].'</h2>',
            $list($item['deliverables']),
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function services(): array
    {
        return [
            [
                'slug' => 'sertifikasi-bnsp-ahli-k3-umum',
                'category' => 'sertifikasi-kompetensi',
                'title' => ['id' => 'Sertifikasi BNSP Ahli K3 Umum', 'en' => 'BNSP General Safety Expert Certification'],
                'summary' => [
                    'id' => 'Pelatihan 120 jam dan uji kompetensi untuk memperoleh sertifikat Ahli K3 Umum.',
                    'en' => '120 hours of training and assessment leading to General Safety Expert certification.',
                ],
                'about' => [
                    'id' => 'Program ini menyiapkan peserta menghadapi uji kompetensi Ahli K3 Umum sesuai skema BNSP. Materi disusun mengikuti unit kompetensi resmi, dengan porsi latihan soal dan studi kasus lapangan yang lebih besar dibanding kelas teori biasa.',
                    'en' => 'This programme prepares participants for the General Safety Expert assessment under the BNSP scheme. Material follows the official competency units, with more exam practice and field case work than a standard theory class.',
                ],
                'audience' => [
                    'id' => 'Pengawas lapangan, staf K3, dan calon petugas K3 di industri konstruksi, manufaktur, serta minyak dan gas yang perusahaannya mensyaratkan Ahli K3 bersertifikat.',
                    'en' => 'Field supervisors, safety staff, and prospective safety officers in construction, manufacturing, and oil and gas whose companies require certified safety experts.',
                ],
                'deliverables' => [
                    ['id' => 'Modul cetak dan digital seluruh unit kompetensi.', 'en' => 'Printed and digital modules for every competency unit.'],
                    ['id' => 'Dua kali simulasi ujian lengkap dengan pembahasan.', 'en' => 'Two full mock examinations with discussion.'],
                    ['id' => 'Uji kompetensi BNSP dan sertifikat bagi yang dinyatakan kompeten.', 'en' => 'BNSP assessment and a certificate for those judged competent.'],
                    ['id' => 'Pendampingan penyusunan laporan praktik kerja lapangan.', 'en' => 'Support in preparing the field practice report.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Dasar hukum dan kebijakan K3', 'en' => 'Safety law and policy foundations'], 'description' => ['id' => 'Peraturan perundangan K3, kewajiban perusahaan, dan peran Ahli K3 di tempat kerja.', 'en' => 'Safety legislation, company obligations, and the safety expert\'s role in the workplace.']],
                    ['title' => ['id' => 'Identifikasi bahaya dan penilaian risiko', 'en' => 'Hazard identification and risk assessment'], 'description' => ['id' => 'Teknik HIRADC dan penyusunan pengendalian risiko yang dapat dijalankan di lapangan.', 'en' => 'HIRADC technique and designing risk controls that work on the ground.']],
                    ['title' => ['id' => 'Praktik inspeksi dan investigasi insiden', 'en' => 'Inspection and incident investigation practice'], 'description' => ['id' => 'Kunjungan lapangan, pencatatan temuan, dan penelusuran akar penyebab insiden.', 'en' => 'Site visits, recording findings, and tracing incident root causes.']],
                    ['title' => ['id' => 'Simulasi dan uji kompetensi', 'en' => 'Simulation and assessment'], 'description' => ['id' => 'Dua kali simulasi ujian, pembahasan, lalu uji kompetensi bersama asesor BNSP.', 'en' => 'Two mock exams, review, then assessment with a BNSP assessor.']],
                ],
                'price' => 8500000,
                'price_before' => 12500000, // hemat 32%
                'duration' => ['id' => '12 hari (120 jam)', 'en' => '12 days (120 hours)'],
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'mode' => 'offline',
                'quota' => 25,
                'taken' => 14,
                'days' => 12,
            ],
            [
                'slug' => 'pelatihan-kepemimpinan-supervisor',
                'category' => 'pelatihan-karyawan',
                'title' => ['id' => 'Pelatihan Kepemimpinan untuk Supervisor', 'en' => 'Leadership Training for Supervisors'],
                'summary' => [
                    'id' => 'Membekali supervisor baru dengan keterampilan memimpin tim, bukan sekadar keahlian teknis.',
                    'en' => 'Equipping new supervisors with team leadership skills, not just technical expertise.',
                ],
                'about' => [
                    'id' => 'Banyak supervisor diangkat karena mahir secara teknis, lalu kesulitan saat harus memimpin orang. Program ini menutup jarak itu melalui latihan percakapan sulit, pemberian umpan balik, dan pembagian tugas yang dilatih berulang dalam kelas.',
                    'en' => 'Many supervisors are promoted for technical skill, then struggle when they must lead people. This programme closes that gap through repeated practice in difficult conversations, feedback, and delegation.',
                ],
                'audience' => [
                    'id' => 'Supervisor yang baru diangkat dalam dua tahun terakhir, serta calon supervisor yang sedang disiapkan naik jenjang.',
                    'en' => 'Supervisors promoted within the last two years, and candidates being prepared for the step up.',
                ],
                'deliverables' => [
                    ['id' => 'Buku kerja berisi lembar latihan yang dapat dipakai kembali di unit masing-masing.', 'en' => 'A workbook of practice sheets reusable back in each participant\'s unit.'],
                    ['id' => 'Umpan balik 360 derajat sebelum dan sesudah program.', 'en' => '360-degree feedback before and after the programme.'],
                    ['id' => 'Rencana pengembangan pribadi untuk tiga bulan berikutnya.', 'en' => 'A personal development plan for the next three months.'],
                    ['id' => 'Sertifikat kepesertaan.', 'en' => 'Certificate of attendance.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Peran supervisor sebagai penghubung', 'en' => 'The supervisor as a bridge'], 'description' => ['id' => 'Memahami posisi supervisor di antara manajemen dan tim, serta tanggung jawab yang melekat padanya.', 'en' => 'Understanding the supervisor\'s position between management and team, and the responsibilities that come with it.']],
                    ['title' => ['id' => 'Percakapan sulit dan umpan balik', 'en' => 'Difficult conversations and feedback'], 'description' => ['id' => 'Latihan menyampaikan koreksi tanpa merusak hubungan kerja, dengan permainan peran berpasangan.', 'en' => 'Practice delivering correction without damaging the working relationship, through paired role-play.']],
                    ['title' => ['id' => 'Pembagian tugas dan pengawasan', 'en' => 'Delegation and oversight'], 'description' => ['id' => 'Menentukan apa yang boleh diserahkan, kepada siapa, dan bagaimana memantaunya tanpa mengambil alih.', 'en' => 'Deciding what to hand over, to whom, and how to monitor without taking back over.']],
                    ['title' => ['id' => 'Proyek perbaikan unit', 'en' => 'Unit improvement project'], 'description' => ['id' => 'Setiap peserta menjalankan satu perbaikan nyata di unitnya dan memaparkan hasilnya.', 'en' => 'Each participant runs one real improvement in their unit and presents the result.']],
                ],
                'price' => 4500000,
                'price_before' => 6000000, // hemat 25%
                'duration' => ['id' => '4 hari (32 jam)', 'en' => '4 days (32 hours)'],
                'location' => 'Jakarta Pusat, DKI Jakarta',
                'mode' => 'offline',
                'quota' => 30,
                'taken' => 21,
                'days' => 4,
            ],
            [
                'slug' => 'assessment-center-dan-talent-mapping',
                'category' => 'konsultasi-manajemen-human-capital',
                'title' => ['id' => 'Assessment Center dan Talent Mapping', 'en' => 'Assessment Centre and Talent Mapping'],
                'summary' => [
                    'id' => 'Menilai kesiapan karyawan naik jenjang dengan metode yang sama dan dapat dibandingkan.',
                    'en' => 'Assessing employees\' readiness to advance using one comparable method.',
                ],
                'about' => [
                    'id' => 'Layanan ini menilai kompetensi karyawan melalui simulasi kerja, bukan hanya wawancara. Setiap peserta melalui rangkaian latihan yang sama dan dinilai asesor bersertifikat, sehingga hasil antarpeserta benar-benar setara.',
                    'en' => 'This service assesses competency through work simulations, not interviews alone. Every participant goes through the same exercise set scored by certified assessors, so results are genuinely comparable.',
                ],
                'audience' => [
                    'id' => 'Perusahaan yang sedang menyiapkan suksesi jabatan kunci, atau yang ingin keputusan promosinya berdiri di atas dasar yang dapat dipertanggungjawabkan.',
                    'en' => 'Companies preparing succession for key roles, or those wanting promotion decisions to rest on defensible ground.',
                ],
                'deliverables' => [
                    ['id' => 'Kamus kompetensi khusus untuk jenjang yang dinilai.', 'en' => 'A competency dictionary tailored to the level being assessed.'],
                    ['id' => 'Laporan individual berisi kekuatan, kesenjangan, dan saran pengembangan.', 'en' => 'Individual reports covering strengths, gaps, and development advice.'],
                    ['id' => 'Peta talenta kisi sembilan kotak untuk seluruh peserta.', 'en' => 'A nine-box talent grid covering all participants.'],
                    ['id' => 'Sesi umpan balik tatap muka bagi setiap peserta.', 'en' => 'A face-to-face feedback session for every participant.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Penyusunan kamus kompetensi', 'en' => 'Competency dictionary design'], 'description' => ['id' => 'Menetapkan perilaku apa yang dinilai dan bagaimana tingkatannya dibedakan.', 'en' => 'Defining which behaviours are assessed and how levels are distinguished.']],
                    ['title' => ['id' => 'Simulasi in-tray dan studi kasus', 'en' => 'In-tray and case study simulation'], 'description' => ['id' => 'Peserta menangani tumpukan pekerjaan tiruan dalam batas waktu, diamati asesor.', 'en' => 'Participants handle a simulated workload against the clock, observed by assessors.']],
                    ['title' => ['id' => 'Diskusi kelompok terarah', 'en' => 'Focus group discussion'], 'description' => ['id' => 'Menilai cara peserta memengaruhi, mendengarkan, dan mengambil keputusan bersama.', 'en' => 'Assessing how participants influence, listen, and decide together.']],
                    ['title' => ['id' => 'Penyusunan laporan dan umpan balik', 'en' => 'Reporting and feedback'], 'description' => ['id' => 'Kalibrasi antarasesor, penulisan laporan, lalu penyampaian hasil kepada peserta.', 'en' => 'Assessor calibration, report writing, then delivering results to participants.']],
                ],
                'price' => 3200000,
                'duration' => ['id' => '2 hari per angkatan', 'en' => '2 days per cohort'],
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'mode' => 'offline',
                'quota' => 20,
                'taken' => 12,
                'days' => 2,
            ],
            [
                'slug' => 'layanan-headhunter-eksekutif',
                'category' => 'headhunter',
                'title' => ['id' => 'Layanan Headhunter Eksekutif', 'en' => 'Executive Headhunter Service'],
                'summary' => [
                    'id' => 'Pencarian tertutup untuk posisi manajerial dan direksi yang sulit diisi lewat lowongan terbuka.',
                    'en' => 'Confidential search for managerial and board roles that open advertising cannot fill.',
                ],
                'about' => [
                    'id' => 'Kami memetakan kandidat langsung dari perusahaan sejenis, bukan menunggu lamaran masuk. Pendekatan dilakukan tertutup sehingga proses tidak terbaca pasar dan kandidat yang sedang menjabat tetap nyaman berbicara.',
                    'en' => 'We map candidates directly from comparable companies rather than waiting for applications. Approaches stay confidential so the process stays off the market and sitting executives remain comfortable talking.',
                ],
                'audience' => [
                    'id' => 'Perusahaan yang membutuhkan posisi manajer senior ke atas, terutama untuk peran yang langka atau berada di lokasi yang sulit menarik kandidat.',
                    'en' => 'Companies needing senior manager roles and above, especially scarce positions or sites that struggle to attract candidates.',
                ],
                'deliverables' => [
                    ['id' => 'Laporan pemetaan pasar kandidat beserta pembanding remunerasi.', 'en' => 'A candidate market map with remuneration benchmarks.'],
                    ['id' => 'Daftar pendek berisi 3–5 kandidat lengkap dengan hasil asesmen.', 'en' => 'A shortlist of three to five candidates with assessment results.'],
                    ['id' => 'Penelusuran rekam jejak dari minimal dua sumber.', 'en' => 'Reference checks from at least two sources.'],
                    ['id' => 'Masa garansi penggantian 12 bulan.', 'en' => 'A 12-month replacement guarantee.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Penyelarasan kriteria jabatan', 'en' => 'Role criteria alignment'], 'description' => ['id' => 'Menyepakati syarat mutlak dan syarat yang masih bisa dinegosiasikan bersama pemberi kerja.', 'en' => 'Agreeing hard requirements and negotiable ones with the employer.']],
                    ['title' => ['id' => 'Pemetaan dan pendekatan kandidat', 'en' => 'Candidate mapping and approach'], 'description' => ['id' => 'Menyusun daftar panjang dari perusahaan sejenis lalu melakukan pendekatan tertutup.', 'en' => 'Building a long list from comparable companies then approaching confidentially.']],
                    ['title' => ['id' => 'Asesmen dan wawancara berbasis perilaku', 'en' => 'Assessment and behaviour-based interviews'], 'description' => ['id' => 'Menguji kompetensi kepemimpinan dan kecocokan dengan budaya organisasi.', 'en' => 'Testing leadership competency and fit with the organisation\'s culture.']],
                    ['title' => ['id' => 'Pendampingan penawaran dan masa transisi', 'en' => 'Offer and transition support'], 'description' => ['id' => 'Membantu negosiasi paket sampai kandidat terpilih benar-benar mulai bekerja.', 'en' => 'Supporting package negotiation until the selected candidate actually starts.']],
                ],
                'price' => 38500000,
                'price_before' => 52000000, // hemat 26%
                'price_unit' => ['id' => 'penugasan', 'en' => 'assignment'],
                'batches' => 1, // per penugasan, bukan per angkatan
                'duration' => ['id' => '8–14 pekan', 'en' => '8–14 weeks'],
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'mode' => 'hybrid',
                'quota' => 6,
                'taken' => 4,
                'days' => 70,
            ],
            [
                'slug' => 'konsultasi-struktur-organisasi',
                'category' => 'konsultasi-manajemen',
                'title' => ['id' => 'Konsultasi Struktur Organisasi', 'en' => 'Organisational Structure Consulting'],
                'summary' => [
                    'id' => 'Menata ulang struktur, uraian tugas, dan alur wewenang agar keputusan tidak tersendat.',
                    'en' => 'Rebuilding structure, job descriptions, and authority flow so decisions stop stalling.',
                ],
                'about' => [
                    'id' => 'Kami menelusuri apa yang benar-benar dikerjakan tiap unit sebelum menggambar struktur baru. Dengan begitu, struktur yang dihasilkan menjawab hambatan nyata di lapangan, bukan sekadar merapikan bagan di atas kertas.',
                    'en' => 'We trace what each unit actually does before drawing a new structure, so the result answers real bottlenecks rather than merely tidying a chart on paper.',
                ],
                'audience' => [
                    'id' => 'Perusahaan yang tumbuh cepat sehingga strukturnya tidak lagi seimbang, atau yang sedang menyiapkan akreditasi dan audit yang menyoroti uraian tugas.',
                    'en' => 'Fast-growing companies whose structure has fallen out of balance, or those preparing for accreditation and audits that scrutinise job descriptions.',
                ],
                'deliverables' => [
                    ['id' => 'Peta alur persetujuan pada seluruh proses inti.', 'en' => 'An approval-flow map across all core processes.'],
                    ['id' => 'Rancangan struktur baru beserta matriks wewenang.', 'en' => 'A new structure design with an authority matrix.'],
                    ['id' => 'Uraian tugas tertulis untuk setiap jabatan terdampak.', 'en' => 'Written job descriptions for every affected position.'],
                    ['id' => 'Pendampingan masa transisi selama tiga bulan.', 'en' => 'Three months of transition support.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Penelusuran proses kerja', 'en' => 'Work process tracing'], 'description' => ['id' => 'Wawancara dan observasi langsung untuk memetakan pekerjaan nyata tiap unit.', 'en' => 'Interviews and direct observation to map each unit\'s real work.']],
                    ['title' => ['id' => 'Analisis hambatan keputusan', 'en' => 'Decision bottleneck analysis'], 'description' => ['id' => 'Menemukan titik persetujuan yang tidak menambah nilai dan memperlambat proses.', 'en' => 'Finding approval points that add no value and slow things down.']],
                    ['title' => ['id' => 'Perancangan struktur dan wewenang', 'en' => 'Structure and authority design'], 'description' => ['id' => 'Menyusun struktur baru dan menurunkan wewenang ke jenjang yang tepat.', 'en' => 'Designing the new structure and pushing authority to the right level.']],
                    ['title' => ['id' => 'Penerapan dan pendampingan', 'en' => 'Rollout and support'], 'description' => ['id' => 'Sosialisasi ke seluruh karyawan dan pendampingan sampai struktur berjalan.', 'en' => 'Briefing all employees and supporting the structure until it runs.']],
                ],
                'price' => 72000000,
                'price_before' => 90000000, // hemat 20%
                'price_unit' => ['id' => 'paket proyek', 'en' => 'project package'],
                'batches' => 1, // per penugasan, bukan per angkatan
                'duration' => ['id' => '3–6 bulan', 'en' => '3–6 months'],
                'location' => 'Di lokasi klien',
                'mode' => 'offline',
                'quota' => 4,
                'taken' => 2,
                'days' => 120,
            ],
            [
                'slug' => 'sertifikasi-bnsp-human-resource',
                'category' => 'sertifikasi-kompetensi',
                'title' => ['id' => 'Sertifikasi BNSP Bidang Human Resource', 'en' => 'BNSP Human Resource Certification'],
                'summary' => [
                    'id' => 'Uji kompetensi bagi praktisi SDM pada skema staf, supervisor, dan manajer.',
                    'en' => 'Assessment for HR practitioners across staff, supervisor, and manager schemes.',
                ],
                'about' => [
                    'id' => 'Program ini menyiapkan praktisi SDM menghadapi uji kompetensi sesuai jenjangnya, mulai dari administrasi kepegawaian sampai perancangan sistem pengelolaan kinerja. Setiap unit kompetensi dilatih dengan berkas kerja nyata.',
                    'en' => 'This programme prepares HR practitioners for assessment at their level, from personnel administration through to performance management system design. Every competency unit is practised with real working documents.',
                ],
                'audience' => [
                    'id' => 'Staf hingga manajer SDM yang membutuhkan pengakuan kompetensi resmi, termasuk untuk keperluan kenaikan jenjang internal.',
                    'en' => 'HR staff through to managers needing formal competency recognition, including for internal advancement.',
                ],
                'deliverables' => [
                    ['id' => 'Modul per unit kompetensi sesuai jenjang yang diambil.', 'en' => 'Modules per competency unit for the chosen level.'],
                    ['id' => 'Pendampingan penyusunan portofolio bukti kompetensi.', 'en' => 'Support in compiling the competency evidence portfolio.'],
                    ['id' => 'Uji kompetensi BNSP dan sertifikat bagi yang kompeten.', 'en' => 'BNSP assessment and certificate for those judged competent.'],
                    ['id' => 'Akses rekaman kelas selama enam bulan.', 'en' => 'Six months of access to class recordings.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Perencanaan dan pengadaan SDM', 'en' => 'HR planning and resourcing'], 'description' => ['id' => 'Menyusun kebutuhan tenaga kerja dan menjalankan proses seleksi yang tertelusur.', 'en' => 'Planning workforce needs and running a traceable selection process.']],
                    ['title' => ['id' => 'Pengelolaan kinerja dan pengembangan', 'en' => 'Performance and development management'], 'description' => ['id' => 'Merancang sasaran kerja, penilaian, dan rencana pengembangan karyawan.', 'en' => 'Designing work targets, appraisals, and employee development plans.']],
                    ['title' => ['id' => 'Hubungan industrial dan kepatuhan', 'en' => 'Industrial relations and compliance'], 'description' => ['id' => 'Penerapan ketentuan ketenagakerjaan dan penanganan perselisihan hubungan kerja.', 'en' => 'Applying labour provisions and handling employment disputes.']],
                    ['title' => ['id' => 'Penyusunan portofolio dan uji kompetensi', 'en' => 'Portfolio preparation and assessment'], 'description' => ['id' => 'Mengumpulkan bukti kerja nyata lalu menjalani uji bersama asesor BNSP.', 'en' => 'Gathering real work evidence then sitting the assessment with a BNSP assessor.']],
                ],
                'price' => 6500000,
                'duration' => ['id' => '6 hari (48 jam)', 'en' => '6 days (48 hours)'],
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'mode' => 'hybrid',
                'quota' => 25,
                'taken' => 9,
                'days' => 6,
            ],
            [
                'slug' => 'pelatihan-layanan-pelanggan',
                'category' => 'pelatihan-karyawan',
                'title' => ['id' => 'Pelatihan Layanan Pelanggan', 'en' => 'Customer Service Training'],
                'summary' => [
                    'id' => 'Menyeragamkan mutu layanan garis depan melalui standar dan latihan penanganan keluhan.',
                    'en' => 'Standardising front-line service quality through standards and complaint-handling practice.',
                ],
                'about' => [
                    'id' => 'Program ini berangkat dari praktik terbaik yang sudah ada di dalam perusahaan Anda sendiri, diangkat menjadi standar tertulis, lalu dilatihkan ke seluruh staf garis depan agar mutunya tidak lagi bergantung pada siapa yang bertugas.',
                    'en' => 'The programme starts from best practice already present inside your own company, turns it into a written standard, then trains it across all front-line staff so quality no longer depends on who is on duty.',
                ],
                'audience' => [
                    'id' => 'Staf toko, resepsionis, petugas layanan pelanggan, dan siapa pun yang berhadapan langsung dengan pelanggan setiap hari.',
                    'en' => 'Store staff, receptionists, customer service officers, and anyone facing customers daily.',
                ],
                'deliverables' => [
                    ['id' => 'Standar layanan tertulis khusus untuk perusahaan Anda.', 'en' => 'A written service standard specific to your company.'],
                    ['id' => 'Naskah penanganan keluhan untuk situasi yang paling sering terjadi.', 'en' => 'Complaint-handling scripts for the most frequent situations.'],
                    ['id' => 'Pembekalan pelatih internal untuk penyegaran mandiri.', 'en' => 'Internal trainer briefing for self-run refreshers.'],
                    ['id' => 'Lembar penilaian layanan untuk pemantauan berkala.', 'en' => 'A service scoring sheet for periodic monitoring.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Observasi layanan di tempat kerja', 'en' => 'On-site service observation'], 'description' => ['id' => 'Mengamati praktik nyata di titik layanan untuk menemukan kesenjangan mutu.', 'en' => 'Observing real practice at service points to find quality gaps.']],
                    ['title' => ['id' => 'Penyusunan standar layanan', 'en' => 'Service standard development'], 'description' => ['id' => 'Mengangkat praktik terbaik internal menjadi standar yang bisa dilatihkan.', 'en' => 'Turning internal best practice into a teachable standard.']],
                    ['title' => ['id' => 'Latihan penanganan keluhan', 'en' => 'Complaint handling practice'], 'description' => ['id' => 'Permainan peran menghadapi keluhan sulit, dengan umpan balik langsung.', 'en' => 'Role-play against difficult complaints with immediate feedback.']],
                    ['title' => ['id' => 'Pembekalan pelatih internal', 'en' => 'Internal trainer briefing'], 'description' => ['id' => 'Menyiapkan staf terpilih agar mampu menyegarkan materi ini sendiri.', 'en' => 'Preparing selected staff to refresh this material themselves.']],
                ],
                'price' => 3500000,
                'duration' => ['id' => '3 hari (24 jam)', 'en' => '3 days (24 hours)'],
                'location' => 'Tangerang, Banten',
                'mode' => 'offline',
                'quota' => 30,
                'taken' => 18,
                'days' => 3,
            ],
            [
                'slug' => 'penyusunan-kpi-dan-remunerasi',
                'category' => 'konsultasi-manajemen-human-capital',
                'title' => ['id' => 'Penyusunan KPI dan Sistem Remunerasi', 'en' => 'KPI and Remuneration System Design'],
                'summary' => [
                    'id' => 'Menghubungkan sasaran perusahaan ke penilaian individu dan struktur gaji yang jelas.',
                    'en' => 'Connecting company targets to individual appraisal and a clear pay structure.',
                ],
                'about' => [
                    'id' => 'Layanan ini menurunkan sasaran perusahaan sampai ke KPI tiap jabatan, lalu menyusun ulang struktur gaji dengan rentang yang jelas per jenjang. Hasilnya, kenaikan gaji punya dasar yang bisa dijelaskan kepada karyawan.',
                    'en' => 'This service cascades company targets down to per-role KPIs, then rebuilds the pay structure with clear ranges per level. Pay rises then have a basis that can be explained to employees.',
                ],
                'audience' => [
                    'id' => 'Perusahaan yang kenaikan gajinya masih merata tanpa kaitan pencapaian, atau yang kehilangan karyawan berkinerja tinggi karena merasa tidak dihargai.',
                    'en' => 'Companies still granting flat pay rises unconnected to achievement, or losing high performers who feel unrecognised.',
                ],
                'deliverables' => [
                    ['id' => 'Peta penurunan sasaran dari perusahaan sampai individu.', 'en' => 'A target cascade map from company down to individual.'],
                    ['id' => 'Kamus KPI untuk seluruh jenjang jabatan.', 'en' => 'A KPI dictionary covering all job levels.'],
                    ['id' => 'Struktur gaji dengan rentang per jenjang beserta kajian pembandingnya.', 'en' => 'A pay structure with ranges per level plus benchmarking analysis.'],
                    ['id' => 'Panduan penerapan untuk tim SDM internal.', 'en' => 'An implementation guide for the internal HR team.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Penurunan sasaran perusahaan', 'en' => 'Company target cascading'], 'description' => ['id' => 'Menerjemahkan sasaran direksi menjadi sasaran departemen yang terukur.', 'en' => 'Translating board targets into measurable departmental goals.']],
                    ['title' => ['id' => 'Penyusunan KPI per jabatan', 'en' => 'Per-role KPI design'], 'description' => ['id' => 'Menetapkan indikator yang benar-benar dikendalikan pemegang jabatan.', 'en' => 'Setting indicators the role-holder genuinely controls.']],
                    ['title' => ['id' => 'Kajian pembanding gaji', 'en' => 'Salary benchmarking'], 'description' => ['id' => 'Membandingkan struktur gaji terhadap data pasar industri sejenis.', 'en' => 'Comparing the pay structure against comparable industry market data.']],
                    ['title' => ['id' => 'Penerapan dan komunikasi', 'en' => 'Rollout and communication'], 'description' => ['id' => 'Menyiapkan cara menjelaskan struktur baru kepada seluruh karyawan.', 'en' => 'Preparing how to explain the new structure to all employees.']],
                ],
                'price' => 48000000,
                'price_before' => 60000000, // hemat 20%
                'price_unit' => ['id' => 'paket proyek', 'en' => 'project package'],
                'batches' => 1, // per penugasan, bukan per angkatan
                'duration' => ['id' => '2–4 bulan', 'en' => '2–4 months'],
                'location' => 'Di lokasi klien',
                'mode' => 'hybrid',
                'quota' => 5,
                'taken' => 3,
                'days' => 90,
            ],
            [
                'slug' => 'sertifikasi-juru-las-dan-teknisi',
                'category' => 'sertifikasi-kompetensi',
                'title' => ['id' => 'Sertifikasi Juru Las dan Teknisi', 'en' => 'Welder and Technician Certification'],
                'summary' => [
                    'id' => 'Uji kompetensi bidang teknik yang dapat digelar langsung di lokasi kerja Anda.',
                    'en' => 'Technical competency assessment that can run directly at your workplace.',
                ],
                'about' => [
                    'id' => 'Uji kompetensi digelar di tempat kerja pada mesin dan peralatan yang benar-benar dioperasikan peserta. Pola ini menghemat waktu produksi sekaligus memastikan yang diuji adalah keterampilan yang dipakai sehari-hari.',
                    'en' => 'Assessment runs at the workplace on the machines participants actually operate. This saves production time while ensuring the skills tested are the ones used daily.',
                ],
                'audience' => [
                    'id' => 'Perusahaan manufaktur, fabrikasi, dan konstruksi yang pelanggannya mensyaratkan juru las atau teknisi bersertifikat.',
                    'en' => 'Manufacturing, fabrication, and construction companies whose customers require certified welders or technicians.',
                ],
                'deliverables' => [
                    ['id' => 'Uji kemampuan awal untuk menentukan kesiapan tiap peserta.', 'en' => 'Initial skill testing to gauge each participant\'s readiness.'],
                    ['id' => 'Pelatihan tambahan hanya bagi peserta yang membutuhkan.', 'en' => 'Extra training only for participants who need it.'],
                    ['id' => 'Uji kompetensi di tempat kerja dan sertifikat BNSP.', 'en' => 'On-site assessment and BNSP certification.'],
                    ['id' => 'Arsip bukti kompetensi siap untuk keperluan audit.', 'en' => 'A competency evidence archive ready for audit.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Verifikasi kelayakan tempat uji', 'en' => 'Assessment venue verification'], 'description' => ['id' => 'Memastikan peralatan dan lokasi memenuhi syarat sebagai tempat uji kompetensi.', 'en' => 'Ensuring equipment and location meet assessment venue requirements.']],
                    ['title' => ['id' => 'Uji kemampuan awal', 'en' => 'Initial skill testing'], 'description' => ['id' => 'Memisahkan peserta yang siap langsung diuji dari yang perlu latihan tambahan.', 'en' => 'Separating those ready for assessment from those needing extra practice.']],
                    ['title' => ['id' => 'Pelatihan penyegaran', 'en' => 'Refresher training'], 'description' => ['id' => 'Latihan terarah pada kelemahan yang ditemukan di uji kemampuan awal.', 'en' => 'Targeted practice on the weaknesses found in initial testing.']],
                    ['title' => ['id' => 'Uji kompetensi dan penerbitan sertifikat', 'en' => 'Assessment and certification'], 'description' => ['id' => 'Uji praktik bersama asesor, pemeriksaan mutu hasil, lalu penerbitan sertifikat.', 'en' => 'Practical assessment with an assessor, quality inspection, then certificate issuance.']],
                ],
                'price' => 3800000,
                'price_before' => 4750000, // hemat 20%
                'duration' => ['id' => '5 hari (40 jam)', 'en' => '5 days (40 hours)'],
                'location' => 'Di lokasi klien',
                'mode' => 'offline',
                'quota' => 40,
                'taken' => 27,
                'days' => 5,
            ],
            [
                'slug' => 'pelatihan-k3-dan-tanggap-darurat',
                'category' => 'pelatihan-karyawan',
                'title' => ['id' => 'Pelatihan K3 dan Tanggap Darurat', 'en' => 'Safety and Emergency Response Training'],
                'summary' => [
                    'id' => 'Pelatihan keselamatan yang materinya disusun dari catatan insiden di lokasi Anda sendiri.',
                    'en' => 'Safety training built from incident records at your own site.',
                ],
                'about' => [
                    'id' => 'Alih-alih memakai materi induksi umum, kami menyusun ulang bahan pelatihan dari catatan insiden dan nyaris celaka yang pernah terjadi di lokasi Anda. Bahaya yang dibahas menjadi bahaya yang benar-benar dikenali peserta.',
                    'en' => 'Instead of generic induction material, we rebuild the training from incident and near-miss records at your own site. The hazards discussed become ones participants genuinely recognise.',
                ],
                'audience' => [
                    'id' => 'Pekerja lapangan, pengawas, dan tim tanggap darurat di industri dengan risiko kerja tinggi.',
                    'en' => 'Field workers, supervisors, and emergency response teams in high-risk industries.',
                ],
                'deliverables' => [
                    ['id' => 'Analisis catatan insiden dan nyaris celaka dua tahun terakhir.', 'en' => 'Analysis of two years of incident and near-miss records.'],
                    ['id' => 'Materi induksi baru yang dapat dipakai permanen untuk pekerja masuk.', 'en' => 'New induction material usable permanently for incoming workers.'],
                    ['id' => 'Latihan tanggap darurat dengan skenario lokasi nyata.', 'en' => 'Emergency drills using real site scenarios.'],
                    ['id' => 'Laporan kesiapan tanggap darurat beserta rekomendasi.', 'en' => 'An emergency readiness report with recommendations.'],
                ],
                'activities' => [
                    ['title' => ['id' => 'Analisis catatan insiden', 'en' => 'Incident record analysis'], 'description' => ['id' => 'Menelusuri pola insiden di lokasi untuk menentukan prioritas materi.', 'en' => 'Tracing site incident patterns to set material priorities.']],
                    ['title' => ['id' => 'Kelas identifikasi bahaya', 'en' => 'Hazard identification class'], 'description' => ['id' => 'Melatih pekerja mengenali dan melaporkan bahaya sebelum menjadi insiden.', 'en' => 'Training workers to spot and report hazards before they become incidents.']],
                    ['title' => ['id' => 'Latihan tanggap darurat', 'en' => 'Emergency drill'], 'description' => ['id' => 'Simulasi kebakaran, tumpahan, dan evakuasi sesuai kondisi lokasi.', 'en' => 'Fire, spill, and evacuation simulation matched to site conditions.']],
                    ['title' => ['id' => 'Evaluasi dan penyusunan tindak lanjut', 'en' => 'Evaluation and follow-up planning'], 'description' => ['id' => 'Menilai hasil latihan dan menyusun perbaikan yang perlu dijalankan.', 'en' => 'Assessing drill results and planning the improvements to be made.']],
                ],
                'price' => 4200000,
                'duration' => ['id' => '4 hari (32 jam)', 'en' => '4 days (32 hours)'],
                'location' => 'Di lokasi klien',
                'mode' => 'offline',
                'quota' => 35,
                'taken' => 23,
                'days' => 4,
            ],
        ];
    }
}
