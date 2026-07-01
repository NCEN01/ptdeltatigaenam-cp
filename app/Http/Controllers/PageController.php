<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use App\Models\Partner;
use Illuminate\Support\Collection;

class PageController extends Controller
{
    public function about()
    {
        $id = app()->getLocale() === 'id';

        // Profil perusahaan, visi & misi di-hardcode di sini (dwibahasa ID/EN)
        // sehingga konten langsung tampil tanpa perlu diisi dari panel admin.
        $about = $id
            ? 'PT Delta Tiga Enam adalah lembaga pelatihan dan penyelenggara sertifikasi profesi yang berfokus pada pengembangan human capital. Sejak berdiri, kami mendampingi perusahaan dan individu untuk meningkatkan kompetensi melalui pelatihan berbasis kebutuhan industri, konsultasi manajemen, pengelolaan sumber daya manusia, serta layanan headhunter yang terintegrasi. Didukung tim profesional berpengalaman dan jaringan mitra yang luas, kami berkomitmen menghadirkan solusi human capital yang terukur, berkelanjutan, dan berdampak nyata bagi pertumbuhan organisasi.'
            : 'PT Delta Tiga Enam is a training institution and professional certification provider focused on human capital development. We help companies and individuals build competencies through industry-driven training, management consulting, human resource management, and integrated headhunter services. Backed by an experienced professional team and a wide partner network, we are committed to delivering measurable, sustainable human capital solutions that create real impact on organizational growth.';

        $vision = $id
            ? 'Menjadi mitra strategis terdepan dalam transformasi human capital yang berkelanjutan, serta turut membangun sumber daya manusia Indonesia yang unggul, kompeten, dan berdaya saing global.'
            : 'To become the leading strategic partner in sustainable human capital transformation, helping build Indonesian talent that is excellent, competent, and globally competitive.';

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

        $tagline = $id
            ? 'Mitra strategis dalam transformasi human capital yang berkelanjutan.'
            : 'A strategic partner in sustainable human capital transformation.';

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

        $values = $id ? [
            ['title' => 'Profesionalisme', 'desc' => 'Standar kerja tinggi dan layanan yang terukur di setiap penugasan.'],
            ['title' => 'Integritas', 'desc' => 'Kejujuran dan komitmen yang kami pegang dalam setiap kerja sama.'],
            ['title' => 'Inovasi', 'desc' => 'Pendekatan dan program yang terus berkembang mengikuti kebutuhan industri.'],
            ['title' => 'Kolaborasi', 'desc' => 'Kemitraan jangka panjang yang tumbuh bersama klien dan talenta.'],
        ] : [
            ['title' => 'Professionalism', 'desc' => 'High working standards and measurable service in every engagement.'],
            ['title' => 'Integrity', 'desc' => 'Honesty and commitment we uphold in every partnership.'],
            ['title' => 'Innovation', 'desc' => 'Approaches and programs that evolve with industry needs.'],
            ['title' => 'Collaboration', 'desc' => 'Long-term partnerships that grow alongside clients and talent.'],
        ];

        return view('pages.about', [
            'about' => $about,
            'vision' => $vision,
            'tagline' => $tagline,
            'missions' => $missions,
            'stats' => $stats,
            'values' => $values,
            'offices' => OfficeLocation::where('is_active', true)->orderBy('sort_order')->get(),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
