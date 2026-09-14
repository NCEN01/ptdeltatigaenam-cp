<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Database\Seeders\Support\StoredImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $tags = $this->seedTags();
        $images = StoredImages::pool('blog', 'agenda', 'services', 'banners');

        foreach ($this->posts() as $index => $item) {
            $post = BlogPost::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'blog_category_id' => $categories[$item['category']] ?? null,
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'content' => [
                        'id' => $this->body($item, 'id'),
                        'en' => $this->body($item, 'en'),
                    ],
                    'featured_image' => StoredImages::pick($images, $index),
                    'location' => $item['location'],
                    'status' => 'published',
                    'is_featured' => $index < 3,
                    'views' => 120 + ($index * 37) % 900,
                    // Terbit mundur seminggu sekali dari hari ini, jadi urutan "terbaru"
                    // selalu masuk akal berapa pun seeder ini dijalankan.
                    'published_at' => Carbon::today()->subDays(3 + ($index * 7))->setTime(9, 0),
                ],
            );

            $post->tags()->sync(array_map(fn ($t) => $tags[$t], $item['tags']));
        }

        $this->command?->info('Blog siap: '.BlogPost::count().' artikel, '
            .BlogCategory::count().' kategori, '.BlogTag::count().' tag.');
    }

    /** @return array<string, int> */
    private function seedCategories(): array
    {
        $categories = [
            'human-capital' => ['id' => 'Human Capital', 'en' => 'Human Capital'],
            'pelatihan' => ['id' => 'Pelatihan', 'en' => 'Training'],
            'sertifikasi' => ['id' => 'Sertifikasi', 'en' => 'Certification'],
            'rekrutmen' => ['id' => 'Rekrutmen', 'en' => 'Recruitment'],
            'manajemen' => ['id' => 'Manajemen', 'en' => 'Management'],
            'keselamatan-kerja' => ['id' => 'Keselamatan Kerja', 'en' => 'Workplace Safety'],
        ];

        $map = [];

        foreach ($categories as $slug => $name) {
            $map[$slug] = BlogCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'sort_order' => count($map) + 1, 'is_active' => true],
            )->id;
        }

        return $map;
    }

    /** @return array<string, int> */
    private function seedTags(): array
    {
        $tags = [
            'kompetensi' => ['id' => 'Kompetensi', 'en' => 'Competency'],
            'bnsp' => ['id' => 'BNSP', 'en' => 'BNSP'],
            'kepemimpinan' => ['id' => 'Kepemimpinan', 'en' => 'Leadership'],
            'karier' => ['id' => 'Karier', 'en' => 'Career'],
            'produktivitas' => ['id' => 'Produktivitas', 'en' => 'Productivity'],
            'k3' => ['id' => 'K3', 'en' => 'Safety'],
            'talenta' => ['id' => 'Talenta', 'en' => 'Talent'],
            'organisasi' => ['id' => 'Organisasi', 'en' => 'Organisation'],
            'wawancara' => ['id' => 'Wawancara', 'en' => 'Interview'],
            'remunerasi' => ['id' => 'Remunerasi', 'en' => 'Remuneration'],
        ];

        $map = [];

        foreach ($tags as $slug => $name) {
            $map[$slug] = BlogTag::updateOrCreate(['slug' => $slug], ['name' => $name])->id;
        }

        return $map;
    }

    /**
     * Isi artikel sengaja dibangun dengan <h2>/<h3> karena daftar isi di halaman
     * artikel memakai heading itu untuk menyusun navigasinya.
     */
    private function body(array $item, string $locale): string
    {
        $isId = $locale === 'id';

        $closing = $isId
            ? '<h2>Kesimpulan</h2><p>'.$item['closing']['id'].'</p>'
            : '<h2>Conclusion</h2><p>'.$item['closing']['en'].'</p>';

        $sections = '';

        foreach ($item['sections'] as $section) {
            $sections .= '<h2>'.$section['heading'][$locale].'</h2>';
            $sections .= '<p>'.$section['body'][$locale].'</p>';

            if (isset($section['points'])) {
                $sections .= '<h3>'.($isId ? 'Yang perlu diperhatikan' : 'What to watch for').'</h3>';
                $sections .= '<ul>'.implode('', array_map(
                    fn ($p) => '<li>'.$p[$locale].'</li>',
                    $section['points'],
                )).'</ul>';
            }
        }

        return '<p>'.$item['lead'][$locale].'</p>'.$sections.$closing;
    }

    /**
     * Artikel dibangun dari kerangka ringkas: satu paragraf pembuka, beberapa
     * bagian bertajuk, lalu penutup. Cukup panjang untuk menguji daftar isi,
     * waktu baca, dan potongan kartu tanpa menjadi teks tempelan.
     *
     * @return list<array<string, mixed>>
     */
    private function posts(): array
    {
        $raw = [
            [
                'Mengapa Analisis Kebutuhan Pelatihan Sering Dilewati',
                'Why Training Needs Analysis Gets Skipped',
                'pelatihan', 'Jakarta Selatan, DKI Jakarta', ['kompetensi', 'produktivitas'],
                'Banyak perusahaan memilih program pelatihan dari katalog sebelum tahu masalah apa yang hendak diselesaikan.',
                'Many companies pick training from a catalogue before knowing which problem they intend to solve.',
            ],
            [
                'Membaca Kisi Sembilan Kotak Tanpa Salah Tafsir',
                'Reading the Nine-Box Grid Without Misreading It',
                'human-capital', 'Bandung, Jawa Barat', ['talenta', 'karier'],
                'Kisi sembilan kotak bukan alat untuk memberi label pada orang, melainkan untuk menentukan perlakuan pengembangan.',
                'The nine-box grid is not a tool for labelling people but for deciding development treatment.',
            ],
            [
                'Sertifikasi BNSP: Apa yang Diuji dan Apa yang Tidak',
                'BNSP Certification: What Is Assessed and What Is Not',
                'sertifikasi', 'Jakarta Pusat, DKI Jakarta', ['bnsp', 'kompetensi'],
                'Sertifikasi kompetensi menguji bukti kerja, bukan hafalan teori. Perbedaan ini mengubah cara peserta perlu bersiap.',
                'Competency certification assesses work evidence, not memorised theory. That difference changes how participants should prepare.',
            ],
            [
                'Supervisor Baru dan Jebakan Keahlian Teknis',
                'New Supervisors and the Technical Skill Trap',
                'pelatihan', 'Surabaya, Jawa Timur', ['kepemimpinan', 'produktivitas'],
                'Orang terbaik di satu pekerjaan belum tentu menjadi pemimpin terbaik bagi orang yang mengerjakannya.',
                'The best person at a job is not automatically the best leader of the people doing it.',
            ],
            [
                'Menyusun KPI yang Benar-Benar Dikendalikan Pemegang Jabatan',
                'Writing KPIs the Role-Holder Actually Controls',
                'manajemen', 'Semarang, Jawa Tengah', ['remunerasi', 'produktivitas'],
                'KPI yang hasilnya ditentukan pihak lain hanya menghasilkan frustrasi, bukan perbaikan kinerja.',
                'A KPI whose result is decided by someone else produces frustration, not performance.',
            ],
            [
                'Wawancara Berbasis Perilaku dan Mengapa Ia Lebih Jujur',
                'Behaviour-Based Interviews and Why They Are More Honest',
                'rekrutmen', 'Jakarta Selatan, DKI Jakarta', ['wawancara', 'talenta'],
                'Menanyakan apa yang pernah dilakukan seseorang memberi gambaran jauh lebih akurat daripada menanyakan apa yang akan ia lakukan.',
                'Asking what someone has done gives a far more accurate picture than asking what they would do.',
            ],
            [
                'Catatan Nyaris Celaka Adalah Data Paling Berharga',
                'Near-Miss Records Are Your Most Valuable Data',
                'keselamatan-kerja', 'Balikpapan, Kalimantan Timur', ['k3', 'produktivitas'],
                'Insiden yang tidak jadi terjadi menyimpan seluruh informasi kecelakaan, tanpa biayanya.',
                'An incident that almost happened holds all the information of an accident without the cost.',
            ],
            [
                'Struktur Organisasi yang Memperlambat Keputusan',
                'Organisational Structures That Slow Decisions Down',
                'manajemen', 'Cikarang, Jawa Barat', ['organisasi', 'produktivitas'],
                'Menambah lapisan persetujuan terasa seperti menambah kehati-hatian, padahal sering hanya menambah waktu tunggu.',
                'Adding an approval layer feels like adding care, yet often adds only waiting time.',
            ],
            [
                'Menyiapkan Suksesi Sebelum Jabatannya Kosong',
                'Preparing Succession Before the Seat Empties',
                'human-capital', 'Jakarta Selatan, DKI Jakarta', ['talenta', 'karier'],
                'Suksesi yang disiapkan setelah pengunduran diri diumumkan hampir selalu terlambat.',
                'Succession prepared after a resignation is announced is almost always too late.',
            ],
            [
                'Uji Kompetensi di Tempat Kerja: Untung dan Syaratnya',
                'On-Site Competency Assessment: Benefits and Requirements',
                'sertifikasi', 'Cilegon, Banten', ['bnsp', 'kompetensi'],
                'Menguji orang pada mesin yang mereka operasikan sehari-hari menghemat waktu sekaligus menaikkan keandalan hasilnya.',
                'Assessing people on the machines they run daily saves time and raises the reliability of the result.',
            ],
            [
                'Memberi Umpan Balik yang Tidak Membuat Orang Bertahan Diri',
                'Giving Feedback That Does Not Trigger Defensiveness',
                'pelatihan', 'Jakarta Barat, DKI Jakarta', ['kepemimpinan', 'produktivitas'],
                'Umpan balik gagal bukan karena isinya salah, melainkan karena cara penyampaiannya memicu pembelaan diri.',
                'Feedback fails not because its content is wrong but because the delivery triggers self-defence.',
            ],
            [
                'Rekrutmen Massal Tanpa Menurunkan Standar',
                'Mass Recruitment Without Lowering the Bar',
                'rekrutmen', 'Karawang, Jawa Barat', ['talenta', 'produktivitas'],
                'Volume besar memaksa proses seleksi berubah bentuk, tetapi tidak harus memaksa standarnya turun.',
                'High volume forces the selection process to change shape, but it need not force the standard down.',
            ],
            [
                'Transparansi Struktur Gaji dan Dampaknya pada Perputaran Karyawan',
                'Pay Transparency and Its Effect on Turnover',
                'manajemen', 'Jakarta Selatan, DKI Jakarta', ['remunerasi', 'karier'],
                'Karyawan jarang keluar karena angkanya kecil; lebih sering karena tidak tahu bagaimana angkanya ditentukan.',
                'Employees rarely leave because the number is small; more often because they cannot see how it was set.',
            ],
            [
                'Membangun Jalur Karier di Organisasi yang Datar',
                'Building Career Paths in a Flat Organisation',
                'human-capital', 'Surabaya, Jawa Timur', ['karier', 'organisasi'],
                'Ketika jabatan di atas sedikit, jalur karier harus dibangun dari kedalaman keahlian, bukan dari tangga jabatan.',
                'When there are few seats above, career paths must be built from depth of expertise rather than a ladder of titles.',
            ],
            [
                'Pelatihan Daring, Tatap Muka, atau Gabungan?',
                'Online, In-Person, or Blended Training?',
                'pelatihan', 'Tangerang, Banten', ['produktivitas', 'kompetensi'],
                'Pilihan bentuk pelatihan sebaiknya ditentukan oleh jenis keterampilannya, bukan oleh anggaran perjalanan.',
                'The training format should be decided by the type of skill involved, not by the travel budget.',
            ],
            [
                'Mengukur Dampak Pelatihan Setelah Kelas Berakhir',
                'Measuring Training Impact After Class Ends',
                'pelatihan', 'Jakarta Pusat, DKI Jakarta', ['produktivitas', 'kompetensi'],
                'Daftar hadir dan nilai ujian menunjukkan kehadiran, bukan perubahan perilaku di tempat kerja.',
                'Attendance sheets and test scores show presence, not changed behaviour in the workplace.',
            ],
            [
                'Peran Ahli K3 yang Sering Disalahpahami',
                'The Frequently Misunderstood Role of the Safety Expert',
                'keselamatan-kerja', 'Makassar, Sulawesi Selatan', ['k3', 'organisasi'],
                'Ahli K3 bukan petugas yang mencari kesalahan, melainkan penasihat yang membantu pekerjaan berjalan aman.',
                'A safety expert is not an officer hunting for faults but an adviser helping work proceed safely.',
            ],
            [
                'Menilai Kecocokan Budaya Tanpa Menutup Keberagaman',
                'Assessing Culture Fit Without Closing Off Diversity',
                'rekrutmen', 'Jakarta Selatan, DKI Jakarta', ['wawancara', 'talenta'],
                'Kecocokan budaya sering dipakai sebagai alasan menolak orang yang berbeda, bukan orang yang tidak sejalan nilainya.',
                'Culture fit is often used to reject people who are different rather than people whose values diverge.',
            ],
            [
                'Asesmen Kompetensi Sebagai Dasar Promosi',
                'Competency Assessment as a Basis for Promotion',
                'human-capital', 'Serang, Banten', ['kompetensi', 'karier'],
                'Keputusan promosi yang hanya berdasarkan penilaian atasan langsung sulit dipertahankan ketika dipertanyakan.',
                'Promotion decisions resting only on line-manager judgement are hard to defend when questioned.',
            ],
            [
                'Menyiapkan Karyawan Menghadapi Perubahan Sistem',
                'Preparing Employees for a System Change',
                'manajemen', 'Jakarta Barat, DKI Jakarta', ['produktivitas', 'organisasi'],
                'Sistem baru gagal dipakai bukan karena rumit, melainkan karena kekhawatiran penggunanya tidak pernah dijawab.',
                'New systems go unused not because they are complex but because users\' worries were never addressed.',
            ],
        ];

        return array_map(fn ($row) => $this->expand($row), $raw);
    }

    /**
     * Mengubah baris ringkas menjadi struktur artikel penuh. Kerangkanya sama,
     * tetapi setiap bagian mengambil kalimat khas artikel itu sendiri.
     *
     * @param  array{0:string,1:string,2:string,3:string,4:list<string>,5:string,6:string}  $row
     */
    private function expand(array $row): array
    {
        [$titleId, $titleEn, $category, $location, $tags, $leadId, $leadEn] = $row;

        return [
            'slug' => Str::slug($titleId),
            'title' => ['id' => $titleId, 'en' => $titleEn],
            'category' => $category,
            'location' => $location,
            'tags' => $tags,
            'excerpt' => ['id' => $leadId, 'en' => $leadEn],
            'lead' => ['id' => $leadId, 'en' => $leadEn],
            'sections' => [
                [
                    'heading' => ['id' => 'Masalah yang sering terjadi', 'en' => 'The problem we keep seeing'],
                    'body' => [
                        'id' => 'Dalam pendampingan kami di berbagai perusahaan, persoalan ini hampir selalu muncul dalam bentuk yang sama: keputusan diambil lebih dulu, alasannya disusun belakangan. Akibatnya sumber daya terpakai untuk memperbaiki gejala, sementara penyebabnya tetap di tempat.',
                        'en' => 'Across the companies we work with, this issue shows up in almost the same shape every time: the decision comes first and the reasoning is assembled afterwards. Resources then go into treating symptoms while the cause stays put.',
                    ],
                    'points' => [
                        ['id' => 'Ukuran keberhasilan tidak disepakati sebelum program dimulai.', 'en' => 'Success measures are not agreed before the programme starts.'],
                        ['id' => 'Data yang sudah dimiliki perusahaan jarang dilihat kembali.', 'en' => 'Data the company already holds is rarely revisited.'],
                        ['id' => 'Pihak yang paling terdampak tidak dilibatkan sejak awal.', 'en' => 'Those most affected are not involved from the outset.'],
                    ],
                ],
                [
                    'heading' => ['id' => 'Cara memperbaikinya', 'en' => 'How to put it right'],
                    'body' => [
                        'id' => 'Langkah pertama selalu memperjelas apa yang hendak berubah dan bagaimana perubahan itu akan terlihat. Tanpa itu, program apa pun sulit dinilai berhasil atau tidak, dan pembahasannya berhenti pada selera.',
                        'en' => 'The first step is always to make clear what should change and how that change will be visible. Without it, no programme can be judged a success, and the discussion stops at taste.',
                    ],
                ],
                [
                    'heading' => ['id' => 'Apa yang berubah setelah diterapkan', 'en' => 'What changes once it is applied'],
                    'body' => [
                        'id' => 'Perubahan yang paling cepat terasa biasanya bukan pada angka besar, melainkan pada berkurangnya pekerjaan ulang dan perdebatan berulang. Angka menyusul kemudian, setelah kebiasaan barunya bertahan beberapa bulan.',
                        'en' => 'The fastest-felt change is usually not in the headline numbers but in less rework and fewer repeated arguments. The numbers follow later, once the new habit has held for a few months.',
                    ],
                ],
            ],
            'closing' => [
                'id' => 'Tidak ada pendekatan tunggal yang cocok untuk semua organisasi. Namun memulai dari pertanyaan yang tepat hampir selalu lebih murah daripada memperbaiki program yang sudah terlanjur berjalan ke arah yang salah.',
                'en' => 'No single approach suits every organisation. But starting from the right question is almost always cheaper than fixing a programme already running in the wrong direction.',
            ],
        ];
    }
}
