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
            // Seluruh portofolio lain, bukan hanya yang sekategori dan bukan
            // hanya tiga. Pembatasan sekategori membuat rel ini kosong pada
            // kategori yang isinya cuma satu, dan tiga kartu di rel yang bisa
            // digeser tidak pernah sampai perlu digeser.
            //
            // Urutannya sama dengan halaman daftar — sort_order lalu tanggal
            // terbaru — jadi orang yang berpindah ke sini menemukan susunan
            // yang sama dengan yang baru saja dilihatnya.
            'otherPortfolios' => Portfolio::where('is_active', true)
                ->where('id', '!=', $portfolio->id)
                ->with('category')
                ->orderBy('sort_order')->latest('project_date')->get(),
        ]);
    }
}
