<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Client;
use App\Models\InstagramUpdate;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home', [
            'heroBanners' => Banner::activeNow()->where('placement', 'home_hero')->orderBy('sort_order')->get(),
            'featuredCategories' => ServiceCategory::where('is_active', true)->where('is_featured', true)
                ->withCount('services')->orderBy('sort_order')->get(),
            'categories' => ServiceCategory::where('is_active', true)
                ->withCount('services')->orderBy('sort_order')->get(),
            'featuredServices' => Service::where('is_active', true)->where('is_featured', true)
                ->with('category')->orderBy('sort_order')->take(4)->get(),
            // Dipetakan ke bentuk yang dipakai <x-pelatihan-terbaru>. Datanya
            // layanan sungguhan, bukan array contoh: judul, gambar, dan harganya
            // ikut berubah begitu admin menyuntingnya lewat CMS.
            //
            // harga_awal hanya diisi ketika diskonnya memang aktif; komponennya
            // menghitung persentase dari selisih keduanya, jadi mengisinya tanpa
            // syarat akan memunculkan lencana diskon pada layanan yang tidak
            // sedang berdiskon.
            'programs' => Service::where('is_active', true)
                ->with('category')->latest()->take(8)->get()
                ->map(fn (Service $s): array => [
                    'kategori' => optional($s->category)->name,
                    'judul' => $s->title,
                    'deskripsi' => $s->short_description,
                    'gambar' => $s->image ? Storage::url($s->image) : null,
                    'url' => route('services.show', $s->slug),
                    'harga' => (float) $s->price,
                    'harga_awal' => $s->hasDiscount() ? (float) $s->discount_original_price : null,
                ]),
            // 3 newest portfolios — auto-updates whenever a more recent one is added.
            'portfolios' => Portfolio::where('is_active', true)
                ->with('category')->latest('project_date')->latest('id')->take(3)->get(),
            // Cakupan portofolio, dihitung dari datanya sendiri sehingga angkanya
            // ikut bergerak saat admin menambah proyek. Satu query, bukan tiga:
            // ketiganya membaca tabel dan filter yang sama persis.
            'portfolioScope' => Portfolio::where('is_active', true)
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("COUNT(DISTINCT NULLIF(client_name, '')) as clients")
                ->selectRaw('COUNT(DISTINCT service_category_id) as categories')
                ->first(),
            'testimonials' => Testimonial::where('is_active', true)
                ->latest()->take(12)->get(),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
            'clients' => Client::where('is_active', true)->orderBy('sort_order')->get(),
            'posts' => BlogPost::published()->with('category')->latest('published_at')->take(9)->get(),
            // Show all agendas — upcoming (soonest first) lead, past events trail (most recent first).
            // Tidak diambil sama sekali selagi halaman Agenda disembunyikan lewat
            // config/company.php: seksinya di beranda pun tidak dirender, jadi
            // query ini hanya membebani setiap kunjungan beranda tanpa hasil.
            'upcomingAgendas' => config('company.pages.agenda', true)
                ? \App\Models\Agenda::published()
                    ->orderByRaw('starts_at >= NOW() DESC')
                    ->orderByRaw('CASE WHEN starts_at >= NOW() THEN starts_at END ASC')
                    ->orderByDesc('starts_at')
                    ->take(12)->get()
                : collect(),
            'instagramUpdates' => InstagramUpdate::where('is_active', true)->orderBy('sort_order')->take(2)->get(),
        ]);
    }
}
