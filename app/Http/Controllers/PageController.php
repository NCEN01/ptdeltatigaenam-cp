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
            ? 'Menjadi lembaga pelatihan dan sertifikasi profesi yang paling dipercaya di Indonesia. Tempat perusahaan menitipkan pengembangan timnya tanpa ragu, dan tempat setiap pekerja membuktikan kemampuannya dengan sertifikat yang diakui di tempat kerja maupun di hadapan auditor. Dari situ lahir tenaga kerja Indonesia yang berani bersaing dengan standar dunia.'
            : 'To become the most trusted training and professional certification institution in Indonesia. The place a company entrusts its people to without hesitation, and the place a worker proves their ability with a certificate that holds up on the job and in front of auditors. From there comes an Indonesian workforce ready to stand against world standards.');

        // Lima misi, satu per lini layanan yang benar-benar dijalankan
        // (pelatihan, sertifikasi, headhunter, konsultasi) ditutup satu janji
        // mutu. Tiap butir menyebut apa yang dikerjakan dan sampai mana
        // selesainya, bukan sifat baik yang tidak bisa ditagih: "inovatif",
        // "berkelanjutan", dan "menjunjung tinggi profesionalisme" tidak
        // memberi pembaca satu pun hal yang bisa diperiksa.
        $missionItems = $id ? [
            'Menyusun program pelatihan dari kebutuhan nyata di tempat kerja klien, bukan dari katalog yang sudah jadi.',
            'Menjalankan uji kompetensi berskema BNSP bersama asesor berlisensi, agar sertifikatnya berlaku untuk audit pelanggan, tender, dan persyaratan regulasi.',
            'Mempertemukan talenta yang tepat dengan organisasi yang tepat lewat seleksi dan rekrutmen eksekutif yang terukur.',
            'Mendampingi organisasi menata manajemen dan pengelolaan human capital-nya sampai perubahannya terasa di pekerjaan harian.',
            'Menjaga satu standar mutu untuk semua klien, apa pun ukuran perusahaannya, dan membuka hasilnya untuk diperiksa siapa pun.',
        ] : [
            "Build training programmes from the real needs of a client's workplace, not from a ready-made catalogue.",
            'Run BNSP-scheme assessments with licensed assessors, so the certificate holds up for customer audits, tenders, and regulatory requirements.',
            'Match the right talent with the right organisation through measured executive selection and recruitment.',
            'Work alongside organisations to shape their management and human capital until the change shows in daily work.',
            'Hold one standard of quality for every client, whatever their size, and leave the results open for anyone to check.',
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
