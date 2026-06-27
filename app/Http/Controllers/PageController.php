<?php

namespace App\Http\Controllers;

use App\Models\CompanyMission;
use App\Models\OfficeLocation;
use App\Models\Partner;
use App\Models\Setting;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'about' => Setting::getLocalized('company_about'),
            'vision' => Setting::getLocalized('company_vision'),
            'missions' => CompanyMission::where('is_active', true)->orderBy('sort_order')->get(),
            'offices' => OfficeLocation::where('is_active', true)->orderBy('sort_order')->get(),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
