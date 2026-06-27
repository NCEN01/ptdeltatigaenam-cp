<?php

namespace App\Http\Controllers;

use App\Models\PartnershipBenefit;
use App\Models\PartnershipPackage;
use App\Models\PartnershipRegistration;
use App\Models\Setting;
use App\Models\User;
use Filament\Notifications\Notification;
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

        $registration = PartnershipRegistration::create([
            ...$data, 'status' => 'baru', 'locale' => app()->getLocale(), 'is_read' => false,
        ]);

        // Notify Admin Transaksi (+ super admins) of the new lead.
        $admins = User::where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin_transaksi', 'super_admin']))
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::make()
                ->title('Pendaftaran Kemitraan Baru')
                ->body("{$registration->company_name} — {$registration->pic_name}")
                ->icon('heroicon-o-clipboard-document-list')
                ->success()
                ->sendToDatabase($admins);
        }

        return back()->with('status', __('site.contact.success'));
    }
}
