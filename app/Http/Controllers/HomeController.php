<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Partner;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home', [
            'heroBanners' => Banner::activeNow()->where('placement', 'home_hero')->orderBy('sort_order')->get(),
            'featuredCategories' => ServiceCategory::where('is_active', true)->where('is_featured', true)
                ->withCount('services')->orderBy('sort_order')->get(),
            'featuredServices' => Service::where('is_active', true)->where('is_featured', true)
                ->with('category')->orderBy('sort_order')->take(4)->get(),
            'portfolios' => Portfolio::where('is_active', true)->where('is_featured', true)
                ->with('category')->orderBy('sort_order')->take(3)->get(),
            'testimonials' => Testimonial::where('is_active', true)->where('is_featured', true)
                ->orderBy('sort_order')->take(6)->get(),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
            'posts' => BlogPost::published()->with('category')->latest('published_at')->take(3)->get(),
        ]);
    }
}
