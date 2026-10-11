<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /** Two rows of three cards per page, matching the blog and agenda indexes. */
    private const PER_PAGE = 6;

    /**
     * Jumlah kartu di rel penutup halaman detail. Sembilan, sama dengan rel
     * "Terus Membaca" di halaman artikel: cukup untuk membuat relnya benar-benar
     * perlu digeser, dan masih jauh dari memuat seluruh katalog di kaki halaman
     * yang baru saja selesai dibaca orang.
     */
    private const KEEP_EXPLORING_LIMIT = 9;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $needle = $this->likeNeedle($q);

        return view('pages.portfolio.index', [
            'q' => $q,
            'portfolios' => Portfolio::where('is_active', true)->with('category')
                // LOWER() di kedua sisi: title dan short_description adalah kolom
                // terjemahan, dan di MariaDB collationnya peka huruf besar-kecil.
                ->when($q !== '', fn ($query) => $query->where(
                    fn ($sub) => $sub->whereRaw('LOWER(title) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(short_description) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(client_name) LIKE ?', [$needle])
                        ->orWhereRaw('LOWER(location) LIKE ?', [$needle]),
                ))
                ->orderBy('sort_order')->latest('project_date')
                ->paginate(self::PER_PAGE)->withQueryString()->fragment(self::RESULTS_ANCHOR),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
            'clients' => Client::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->latest()->get(),
        ]);
    }

    public function show(string $locale, string $slug)
    {
        $portfolio = Portfolio::where('slug', $slug)->where('is_active', true)
            ->with(['category', 'images', 'testimonials'])->firstOrFail();

        return view('pages.portfolio.show', [
            'portfolio' => $portfolio,
            // Beberapa portofolio terbaru, bukan hanya yang sekategori dan
            // bukan pula seluruhnya. Pembatasan sekategori membuat rel ini
            // kosong pada kategori yang isinya cuma satu; memuat seluruhnya
            // menaruh seisi katalog di kaki halaman yang baru selesai dibaca.
            //
            // Diurut tanggal terbaru, bukan sort_order: yang dijanjikan
            // judulnya "terbaru", dan sort_order adalah urutan pilihan admin
            // untuk halaman daftar, bukan urutan waktu.
            'latestPortfolios' => Portfolio::where('is_active', true)
                ->where('id', '!=', $portfolio->id)
                ->with('category')
                ->latest('project_date')
                ->take(self::KEEP_EXPLORING_LIMIT)->get(),
        ]);
    }
}
