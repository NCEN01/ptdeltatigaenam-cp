<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;

class ServiceController extends Controller
{
    public function index()
    {
        return view('pages.services.index', [
            'categories' => ServiceCategory::where('is_active', true)
                ->with(['services' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')->get(),
        ]);
    }

    public function show(string $locale, string $slug)
    {
        $service = Service::where('slug', $slug)->where('is_active', true)
            ->with(['category', 'activities', 'schedules' => fn ($q) => $q->where('is_active', true)->orderBy('start_date')])
            ->firstOrFail();

        return view('pages.services.show', [
            'service' => $service,
            'related' => Service::where('is_active', true)
                ->where('service_category_id', $service->service_category_id)
                ->where('id', '!=', $service->id)->take(3)->get(),
        ]);
    }
}
