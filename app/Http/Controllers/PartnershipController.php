<?php

namespace App\Http\Controllers;

use App\Models\PartnershipBenefit;
use App\Models\PartnershipPackage;
use App\Models\PartnershipRegistration;
use App\Models\Setting;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    public function index()
    {
        return view('pages.partnership', [
            'intro' => Setting::getLocalized('partnership_intro'),
            'benefits' => PartnershipBenefit::where('is_active', true)->orderBy('sort_order')->get(),
            'packages' => PartnershipPackage::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:200'],
            'company_address' => ['required', 'string', 'max:1000'],
            'pic_name' => ['required', 'string', 'max:150'],
            'pic_position' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:150'],
            'partnership_package_id' => ['nullable', 'exists:partnership_packages,id'],
            'preferred_meeting_at' => ['nullable', 'date'],
            'alternative_meeting_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        PartnershipRegistration::create([...$data, 'status' => 'baru', 'locale' => app()->getLocale(), 'is_read' => false]);

        return back()->with('status', __('site.contact.success'));
    }
}
