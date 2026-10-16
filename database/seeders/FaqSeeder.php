<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

/**
 * Memindahkan keenam tanya-jawab yang semula ditulis di
 * resources/views/pages/home.blade.php ke basis data, apa adanya.
 *
 * Kalimatnya tidak diubah sedikit pun. Keenamnya sudah dipakai di situs yang
 * berjalan dan sudah ikut terindeks mesin pencari; penyemai ini memindahkan
 * tempat penyimpanannya, bukan memperbaiki naskahnya.
 *
 * Idempoten: dikenali dari pertanyaan versi Indonesianya, jadi dijalankan dua
 * kali tidak menghasilkan dua belas baris. Pencocokannya pada kolom JSON
 * memakai whereJsonContains, bukan where biasa — kolom question berisi
 * {"id":"…","en":"…"} sehingga perbandingan string utuh tidak akan pernah cocok.
 */
class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'id' => 'Apa saja layanan utama yang ditawarkan oleh PT Delta Tiga Enam?',
                'en' => 'What are the main services offered by PT Delta Tiga Enam?',
                'jawab_id' => 'Kami menawarkan solusi human capital terintegrasi yang mencakup Pelatihan SDM, Sertifikasi Kompetensi Profesi (BNSP), Asesmen & Seleksi Karyawan, serta Headhunter & Penempatan Tenaga Kerja.',
                'jawab_en' => 'We offer integrated human capital solutions covering HR Training, Professional Competence Certification (BNSP), Employee Assessment & Selection, and Headhunting & Talent Placement.',
            ],
            [
                'id' => 'Apa manfaat bekerja sama dengan konsultan human capital seperti Delta Tiga Enam?',
                'en' => 'What are the benefits of working with a human capital consultant like Delta Tiga Enam?',
                'jawab_id' => 'Sebagai konsultan human capital, kami membantu perusahaan mengelola SDM secara strategis, mulai dari pemetaan kompetensi, peningkatan produktivitas, hingga efisiensi rekrutmen. Anda mendapatkan pendampingan ahli sehingga setiap keputusan terkait talenta menjadi lebih terukur, hemat biaya, dan berdampak langsung pada kinerja organisasi.',
                'jawab_en' => 'As a human capital consultant, we help companies manage their workforce strategically, from competency mapping and productivity improvement to more efficient recruitment. You gain expert guidance so every talent-related decision becomes measurable, cost-effective, and directly impacts organizational performance.',
            ],
            [
                'id' => 'Apakah sertifikasi kompetensi dari Delta Tiga Enam resmi?',
                'en' => 'Are the competence certifications official?',
                'jawab_id' => 'Ya, seluruh program sertifikasi kompetensi kami diselenggarakan secara resmi bekerja sama dengan LSP berlisensi BNSP (Badan Nasional Sertifikasi Profesi) sehingga diakui di tingkat nasional oleh berbagai sektor industri.',
                'jawab_en' => 'Yes, all our competency certification programs are officially conducted in partnership with BNSP-licensed LSPs, ensuring national recognition across industries.',
            ],
            [
                'id' => 'Berapa lama durasi pelatihan dan apakah peserta memperoleh sertifikat?',
                'en' => 'How long is the training and do participants receive a certificate?',
                'jawab_id' => 'Durasi pelatihan bervariasi mulai dari 1-5 hari tergantung program dan tingkat kompetensinya. Setiap peserta yang menyelesaikan pelatihan akan memperoleh sertifikat; khusus program sertifikasi kompetensi, sertifikat diterbitkan resmi oleh LSP berlisensi BNSP setelah peserta dinyatakan kompeten dalam uji sertifikasi.',
                'jawab_en' => 'Training duration varies from 1-5 days depending on the program and competency level. Every participant who completes the training receives a certificate; for competency certification programs, certificates are officially issued by BNSP-licensed LSPs once the participant is declared competent in the assessment.',
            ],
            [
                'id' => 'Bagaimana cara mendaftar program pelatihan atau sertifikasi?',
                'en' => 'How can I register for a training or certification program?',
                'jawab_id' => 'Anda dapat mendaftar langsung dengan memilih layanan di website kami, menghubungi admin kami melalui WhatsApp, atau mengirimkan detail kebutuhan Anda melalui formulir kontak.',
                'jawab_en' => 'You can register directly by selecting the service on our website, contacting our admin via WhatsApp, or sending your details through the contact form.',
            ],
            [
                'id' => 'Apakah program pelatihan bisa diadakan khusus untuk internal perusahaan (In-House)?',
                'en' => 'Can training programs be customized for internal company needs (In-House)?',
                'jawab_id' => 'Tentu saja. Kami menyediakan layanan In-House Training dengan materi, jadwal, dan metode penyampaian yang dirancang khusus menyesuaikan kebutuhan peningkatan kompetensi staf di perusahaan Anda.',
                'jawab_en' => 'Absolutely. We provide customized In-House Training with curriculum, schedule, and methods tailored to address specific competence needs in your organization.',
            ],
        ];

        foreach ($faqs as $i => $faq) {
            $sudahAda = Faq::whereJsonContains('question->id', $faq['id'])->exists();

            if ($sudahAda) {
                continue;
            }

            Faq::create([
                'question' => ['id' => $faq['id'], 'en' => $faq['en']],
                'answer' => ['id' => $faq['jawab_id'], 'en' => $faq['jawab_en']],
                'sort_order' => $i + 1,
                'is_active' => true,
            ]);
        }
    }
}
