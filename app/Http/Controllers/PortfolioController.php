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
            'related' => Portfolio::where('is_active', true)->where('id', '!=', $portfolio->id)
                ->where('service_category_id', $portfolio->service_category_id)->take(3)->get(),
        ]);
    }
}
