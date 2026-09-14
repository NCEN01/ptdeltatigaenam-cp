<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Testimonial;

class PortfolioController extends Controller
{
    /** Two rows of three cards per page, matching the blog and agenda indexes. */
    private const PER_PAGE = 6;

    public function index()
    {
        return view('pages.portfolio.index', [
            'portfolios' => Portfolio::where('is_active', true)->with('category')
                ->orderBy('sort_order')->latest('project_date')->paginate(self::PER_PAGE),
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
