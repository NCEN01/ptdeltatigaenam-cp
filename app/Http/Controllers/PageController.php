<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Setting;
use Illuminate\Support\Collection;

class PageController extends Controller
{
    public function about()
    {
        $id = app()->getLocale() === 'id';

        // Load from settings database with static fallbacks
        $about = Setting::getLocalized('company_about') ?: ($id
            ? "Delta Tiga Enam adalah lembaga pelatihan dan penyelenggara sertifikasi profesi yang berkomitmen untuk meningkatkan kompetensi tenaga kerja di berbagai industri. Selain itu, kami juga menyediakan layanan konsultasi manajemen, pengelolaan human capital, serta proses seleksi dan rekrutmen eksekutif (headhunter).\n\nDengan pendekatan yang komprehensif, kami membantu individu dan perusahaan dalam mengembangkan keterampilan, meningkatkan daya saing, dan memastikan standar profesional yang lebih tinggi.\n\nDidukung oleh tenaga ahli berpengalaman di bidangnya, kami menghadirkan solusi yang terintegrasi dan disesuaikan dengan kebutuhan klien, baik dalam hal pelatihan, sertifikasi, konsultasi manajemen, pengelolaan human capital, maupun proses seleksi dan rekrutmen eksekutif (headhunter).\n\nKami berupaya memberikan layanan terbaik yang tidak hanya relevan dengan perkembangan industri, tetapi juga berdampak nyata bagi pertumbuhan bisnis dan karier profesional."
            : "Delta Tiga Enam is a training institution and professional certification provider committed to improving workforce competencies across various industries. We also provide management consulting, human capital management, and executive selection & recruitment (headhunter) services.\n\nWith a comprehensive approach, we help individuals and companies develop skills, strengthen competitiveness, and ensure higher professional standards.\n\nSupported by experienced experts in their respective fields, we deliver integrated solutions tailored to each client's needs — whether in training, certification, management consulting, human capital management, or executive selection & recruitment (headhunter).\n\nWe strive to deliver the best services that are not only relevant to industry developments, but also create real impact on business growth and professional careers.");

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

        $stats = [];
        for ($i = 1; $i <= 4; $i++) {
            $val = Setting::get("about_stat_{$i}_value", '');
            $label = Setting::getLocalized("about_stat_{$i}_label");
            if ($val !== '' && $label !== null) {
                $stats[] = ['value' => $val, 'label' => $label];
            }
        }
        if (empty($stats)) {
            $stats = $id ? [
                ['value' => '10+', 'label' => 'Tahun pengalaman'],
                ['value' => '500+', 'label' => 'Profesional terlatih'],
                ['value' => '50+', 'label' => 'Klien korporat'],
                ['value' => '20+', 'label' => 'Program sertifikasi'],
            ] : [
                ['value' => '10+', 'label' => 'Years of experience'],
                ['value' => '500+', 'label' => 'Professionals trained'],
                ['value' => '50+', 'label' => 'Corporate clients'],
                ['value' => '20+', 'label' => 'Certification programs'],
            ];
        }

        // Core values (DELTA) — name stays in English; description is localized. Image = Unsplash id.
        $values = $id ? [
            ['title' => 'Dinamis', 'desc' => 'Selalu adaptif terhadap perubahan dan inovasi dalam dunia kerja.', 'img' => 'dynamic'],
            ['title' => 'Keunggulan', 'desc' => 'Unggul dalam setiap layanan yang diberikan.', 'img' => 'excellence'],
            ['title' => 'Terdepan', 'desc' => 'Pionir dalam pengembangan solusi human capital yang inovatif.', 'img' => 'leading'],
            ['title' => 'Transenden', 'desc' => 'Melampaui ekspektasi klien dan memberikan nilai tambah yang berkelanjutan.', 'img' => 'transcendent'],
            ['title' => 'Aksi', 'desc' => 'Berorientasi pada hasil dan tindakan nyata untuk mencapai tujuan.', 'img' => 'action'],
        ] : [
            ['title' => 'Dynamic', 'desc' => 'Always adaptive to change and innovation in the world of work.', 'img' => 'dynamic'],
            ['title' => 'Excellence', 'desc' => 'Excelling in every service we deliver.', 'img' => 'excellence'],
            ['title' => 'Leading', 'desc' => 'A pioneer in developing innovative human capital solutions.', 'img' => 'leading'],
            ['title' => 'Transcendent', 'desc' => 'Exceeding client expectations and delivering sustainable added value.', 'img' => 'transcendent'],
            ['title' => 'Action', 'desc' => 'Results-oriented, with real action to achieve goals.', 'img' => 'action'],
        ];

        // Hardcoded office locations
        $offices = $id ? [
            ['name' => 'Kantor Pusat (SCBD)', 'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan, Jakarta Selatan', 'phone' => '(021) 5890 5002'],
            ['name' => 'Kantor Pemasaran', 'address' => 'Cikarang Technopark, Jalan Inti I Blok C1 No. 7, Cibatu, Cikarang Selatan, Kabupaten Bekasi, Jawa Barat 17530', 'phone' => '(021) 8988 1110'],
            ['name' => 'Kantor Operasional', 'address' => 'Taman Widya Asri Blok GG No. 18, Serang, Kota Serang, Banten 46111', 'phone' => '(0254) 401 900'],
        ] : [
            ['name' => 'Head Office (SCBD)', 'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan, Jakarta Selatan', 'phone' => '(021) 5890 5002'],
            ['name' => 'Marketing Office', 'address' => 'Cikarang Technopark, Jalan Inti I Blok C1 No. 7, Cibatu, Cikarang Selatan, Kabupaten Bekasi, Jawa Barat 17530', 'phone' => '(021) 8988 1110'],
            ['name' => 'Operational Office', 'address' => 'Taman Widya Asri Blok GG No. 18, Serang, Kota Serang, Banten 46111', 'phone' => '(0254) 401 900'],
        ];

        return view('pages.about', [
            'about' => $about,
            'vision' => $vision,
            'tagline' => $tagline,
            'missions' => $missions,
            'stats' => $stats,
            'values' => $values,
            'offices' => $offices,
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
