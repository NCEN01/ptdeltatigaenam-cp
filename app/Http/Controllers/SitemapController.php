<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Models\BlogPost;
use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $locales = SetLocale::SUPPORTED;
        $urls = [];

        // Static routes (per locale)
        $static = ['home', 'about', 'services.index', 'portfolio.index', 'blog.index', 'partnership.index', 'contact.index'];
        foreach ($locales as $locale) {
            foreach ($static as $name) {
                $urls[] = ['loc' => route($name, ['locale' => $locale]), 'priority' => $name === 'home' ? '1.0' : '0.8'];
            }
        }

        // Dynamic content
        foreach (Service::where('is_active', true)->pluck('slug') as $slug) {
            foreach ($locales as $locale) {
                $urls[] = ['loc' => route('services.show', ['locale' => $locale, 'slug' => $slug]), 'priority' => '0.7'];
            }
        }
        foreach (Portfolio::where('is_active', true)->pluck('slug') as $slug) {
            foreach ($locales as $locale) {
                $urls[] = ['loc' => route('portfolio.show', ['locale' => $locale, 'slug' => $slug]), 'priority' => '0.6'];
            }
        }
        foreach (BlogPost::published()->pluck('slug') as $slug) {
            foreach ($locales as $locale) {
                $urls[] = ['loc' => route('blog.show', ['locale' => $locale, 'slug' => $slug]), 'priority' => '0.6'];
            }
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
