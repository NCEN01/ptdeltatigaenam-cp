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
            'latestServices' => Service::where('is_active', true)
                ->with('category')->latest()->take(6)->get(),
            // 3 newest portfolios — auto-updates whenever a more recent one is added.
            'portfolios' => Portfolio::where('is_active', true)
                ->with('category')->latest('project_date')->latest('id')->take(3)->get(),
            'testimonials' => Testimonial::where('is_active', true)
                ->latest()->take(12)->get(),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
            'clients' => Client::where('is_active', true)->orderBy('sort_order')->get(),
            'posts' => BlogPost::published()->with('category')->latest('published_at')->take(9)->get(),
            'upcomingAgendas' => \App\Models\Agenda::published()->where('starts_at', '>=', now())->orderBy('starts_at')->take(9)->get(),
            'instagramUpdates' => InstagramUpdate::where('is_active', true)->orderBy('sort_order')->take(2)->get(),
        ]);
    }
}
