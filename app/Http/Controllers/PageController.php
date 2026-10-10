<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Collection;

class PageController extends Controller
{
    public function about()
    {
        $id = app()->getLocale() === 'id';

        // Load from settings database with static fallbacks.
        // Cadangan ini dijaga sama persis dengan isi pengaturan 'company_about'
        // di basis data: dua paragraf. Kalau keduanya menyimpang, halaman akan
        // berganti isi begitu barisnya dikosongkan di CMS.
        $about = Setting::getLocalized('company_about') ?: ($id
            ? "PT. Delta Tiga Enam adalah lembaga pelatihan dan memfasilitasi penyelenggaraan sertifikasi profesi yang berkomitmen untuk meningkatkan kompetensi tenaga kerja di berbagai industri. Selain itu, kami juga menyediakan layanan konsultasi manajemen, pengelolaan human capital, serta proses seleksi dan rekrutmen eksekutif (headhunter). Dengan pendekatan yang komprehensif, kami membantu individu dan perusahaan dalam mengembangkan keterampilan, meningkatkan daya saing, dan memastikan standar profesional yang lebih tinggi.\n\nDidukung oleh tenaga ahli berpengalaman di bidangnya, kami menghadirkan solusi yang terintegrasi dan disesuaikan dengan kebutuhan klien. Baik dalam hal pelatihan, sertifikasi, konsultasi manajemen, pengelolaan human capital maupun proses seleksi dan rekrutmen eksekutif (headhunter). Kami berupaya memberikan layanan terbaik yang tidak hanya relevan dengan perkembangan industri, tetapi juga berdampak nyata bagi pertumbuhan bisnis dan karier profesional."
            : "PT. Delta Tiga Enam is a training institution and a facilitator of professional certification, committed to raising workforce competency across industries. We also provide management consulting, human capital management, and executive selection and recruitment (headhunter) services. With a comprehensive approach, we help individuals and companies develop their skills, strengthen their competitiveness, and meet higher professional standards.\n\nSupported by experts experienced in their respective fields, we deliver integrated solutions tailored to each client's needs, whether in training, certification, management consulting, human capital management, or executive selection and recruitment (headhunter). We strive to provide services that are not only relevant to industry developments, but also create real impact on business growth and professional careers.");

        // Cadangan ini dijaga sama persis dengan isi pengaturan 'company_vision'
        // di basis data. Kalau keduanya menyimpang, halaman berganti isi begitu
        // barisnya dikosongkan di CMS.
        $vision = Setting::getLocalized('company_vision') ?: ($id
            ? 'Menjadi lembaga pelatihan dan sertifikasi profesi yang paling dipercaya di Indonesia. Dipercaya bukan karena mengaku, melainkan karena setiap nama yang diluluskan terbuka untuk diperiksa siapa pun, dan kemampuannya terbukti pada pekerjaan yang ia tangani sehari-hari. Dari situ lahir tenaga kerja Indonesia yang berani berdiri di hadapan standar dunia.'
            : 'To become the most trusted training and professional certification institution in Indonesia. Trusted not because we say so, but because every name we pass is open for anyone to check, and their ability shows in the work they handle every day. From there comes an Indonesian workforce ready to stand against world standards.');

        // Lima misi, satu per lini layanan yang benar-benar dijalankan
        // (pelatihan, sertifikasi, headhunter, konsultasi) ditutup satu janji
        // mutu. Tiap butir menyebut apa yang dikerjakan dan sampai mana
        // selesainya, bukan sifat baik yang tidak bisa ditagih: "inovatif",
        // "berkelanjutan", dan "menjunjung tinggi profesionalisme" tidak
        // memberi pembaca satu pun hal yang bisa diperiksa.
        //
        // Tiap butir juga menunjuk sesuatu yang benar-benar ada di situs ini —
        // tiga cara mengikuti program di halaman Layanan, daftar nomor
        // sertifikat yang terbuka di halaman Sertifikat, dan nilai perusahaan
        // di halaman ini — supaya janjinya bisa ditelusuri pembaca pada
        // kunjungan yang sama, bukan sekadar dipercaya.
        $missionItems = $id ? [
            'Menyusun program dari pekerjaan yang benar-benar dijalankan peserta, lalu menggelarnya di tempat kerja, daring, atau gabungan keduanya, mengikuti jam kerja Anda.',
            'Menjalankan uji kompetensi berskema BNSP bersama asesor berlisensi, lalu membuka nomor sertifikatnya di situs ini agar siapa pun dapat memverifikasinya sendiri.',
            'Mempertemukan talenta yang tepat dengan organisasi yang tepat, dan menilai calonnya dari kompetensi yang teruji, bukan dari berkas lamaran.',
            'Mendampingi organisasi menata manajemen dan human capital-nya sampai perubahannya terbaca di pekerjaan harian, bukan berhenti di laporan akhir.',
            'Memberi satu standar yang sama kepada perusahaan sepuluh peserta maupun seribu, dan menguji ulang kompetensi pengajar serta asesornya secara berkala.',
        ] : [
            'Build each programme around the work participants actually do, then run it on site, online, or as a mix of both, around your working hours.',
            'Run BNSP-scheme assessments with licensed assessors, then publish the certificate numbers on this site so anyone can verify them.',
            'Match the right talent with the right organisation, judging candidates on assessed competency rather than on a CV.',
            'Work alongside organisations on their management and human capital until the change shows in daily work, not only in a closing report.',
            'Give the same standard to a company sending ten participants and one sending a thousand, and reassess our own instructors and assessors on a schedule.',
        ];
        $missions = Collection::make($missionItems)->map(fn ($content) => (object) ['content' => $content]);

        $tagline = Setting::getLocalized('company_tagline') ?: ($id
            ? 'Mitra strategis dalam transformasi human capital yang berkelanjutan.'
            : 'A strategic partner in sustainable human capital transformation.');

        // Sumber yang sama dengan papan angka di bawah Kategori Layanan di
        // beranda. Pembacaannya dipusatkan supaya kedua halaman tidak bisa
        // menampilkan angka yang berbeda tentang perusahaan yang sama.
        $stats = \App\Support\Company::stats();

        return view('pages.about', [
            'about' => $about,
            'vision' => $vision,
            'tagline' => $tagline,
            'missions' => $missions,
            'stats' => $stats,
            // Daftar tersendiri, bukan Company::values(). values() adalah
            // akronim DELTA dan sudah dipakai seksi "Alasan Memilih Kami" di
            // beranda; menampilkannya lagi di sini membuat lima judul yang
            // sama terbaca dua kali dalam satu kunjungan.
            'values' => \App\Support\Company::aboutValues(),
            // 'offices' dan 'partners' dilepas: keduanya tidak pernah dipakai
            // about.blade.php, dan 'partners' menjalankan satu query beserta
            // hidrasi modelnya di tiap kunjungan halaman ini lalu membuangnya.
            // Footer memanggil Company::offices() sendiri, jadi salinan di sini
            // memang tidak punya pemakai.
        ]);
    }
}
