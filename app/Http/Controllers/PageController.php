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

        $vision = Setting::getLocalized('company_vision') ?: ($id
            ? 'Menjadi mitra strategis terdepan dalam transformasi human capital yang berkelanjutan, serta turut membangun sumber daya manusia Indonesia yang unggul, kompeten, dan berdaya saing global.'
            : 'To become the leading strategic partner in sustainable human capital transformation, helping build Indonesian talent that is excellent, competent, and globally competitive.');

        // Hardcoded mission items
        $missionItems = $id ? [
            'Menyediakan solusi human capital komprehensif yang dirancang sesuai kebutuhan dan tujuan strategis setiap klien.',
            'Menyelenggarakan program pelatihan dan sertifikasi profesi yang inovatif serta selaras dengan tren dan standar industri.',
            'Menghadirkan layanan headhunter dan rekrutmen yang efektif untuk mempertemukan talenta terbaik dengan organisasi yang tepat.',
            'Memberikan konsultasi manajemen yang membantu organisasi tumbuh secara produktif dan berkelanjutan.',
            'Menjunjung tinggi profesionalisme, integritas, dan kualitas dalam setiap layanan yang kami berikan.',
        ] : [
            "Provide comprehensive human capital solutions designed around each client's needs and strategic goals.",
            'Deliver innovative professional training and certification programs aligned with industry trends and standards.',
            'Offer effective headhunter and recruitment services that match top talent with the right organizations.',
            'Provide management consulting that helps organizations grow productively and sustainably.',
            'Uphold professionalism, integrity, and quality in every service we deliver.',
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
