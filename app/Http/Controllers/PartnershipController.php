<?php

namespace App\Http\Controllers;

use App\Filament\Resources\PartnershipRegistrationResource;
use App\Models\Partner;
use App\Models\PartnershipBenefit;
use App\Models\PartnershipPackage;
use App\Models\PartnershipRegistration;
use App\Models\Setting;
use App\Models\User;
use App\Rules\PersonName;
use App\Rules\PhoneNumber;
use App\Rules\Recaptcha;
use App\Rules\SafeEmail;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PartnershipController extends Controller
{
    /**
     * Kunci sesi tempat isian formulir tamu dititipkan selama ia pergi masuk.
     * Dititipkan ke sesi, bukan ke _old_input milik withInput(): old input
     * hanya bertahan satu permintaan, dan perjalanan tamu dari sini ke halaman
     * masuk lalu kembali memakan tiga permintaan.
     */
    private const DRAFT = 'partnership.draft';

    public function index(Request $request)
    {
        return view('pages.partnership', [
            // Isian yang dititipkan sebelum pemiliknya diminta masuk. Dibaca,
            // belum dihapus: dihapusnya setelah pendaftarannya benar-benar
            // tersimpan, supaya menyegarkan halaman atau membuka tab lain di
            // tengah jalan tidak ikut membuang isiannya.
            'draft' => $request->session()->get(self::DRAFT, []),
            'intro' => Setting::getLocalized('partnership_intro'),
            'partnersTitle' => Setting::getLocalized('partnership_partners_title'),
            'partnersDesc' => Setting::getLocalized('partnership_partners_desc'),
            'benefits' => PartnershipBenefit::where('is_active', true)->orderBy('sort_order')->get(),
            'packages' => PartnershipPackage::where('is_active', true)->orderBy('sort_order')->get(),
            'partners' => Partner::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'company_name' => ['required', 'string', 'min:2', 'max:200'],
            'company_address' => ['required', 'string', 'min:5', 'max:1000'],
            'pic_name' => ['required', 'string', 'max:150', new PersonName],
            'pic_position' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:50', new PhoneNumber],
            'email' => ['required', 'email', 'max:150', new SafeEmail],
            /* Paket yang sudah dinonaktifkan admin tidak tampil di formulir,
               jadi id-nya hanya bisa datang dari permintaan yang direkayasa. */
            'partnership_package_id' => [
                'nullable', 'integer',
                Rule::exists('partnership_packages', 'id')->where('is_active', true),
            ],
            /* Jadwal presentasi di masa lalu tidak bisa dikonfirmasi tim. */
            'preferred_meeting_at' => ['nullable', 'date', 'after:now'],
            'alternative_meeting_at' => ['nullable', 'date', 'after:now', 'different:preferred_meeting_at'],
            'notes' => ['nullable', 'string', 'max:2000'],

            /* Kotak "Saya bukan robot". rules() mengembalikan array kosong
               selama kuncinya belum diisi di .env, jadi kolom ini tidak
               divalidasi sama sekali sampai fiturnya dinyalakan. */
            'g-recaptcha-response' => Recaptcha::rules(),
        ]);

        /* Dibuang sebelum apa pun memakainya: ia bukti sesaat bahwa
           pengirimnya manusia, bukan bagian dari pendaftarannya — dan $data
           di bawah ini dititipkan ke sesi serta dipakai membuat baris
           PartnershipRegistration. */
        unset($data['g-recaptcha-response']);

        /* Pemeriksaan isian dijalankan lebih dulu, baru keharusan masuk.
           Urutannya dibalik akan memulangkan tamu ke halaman masuk untuk
           formulir yang ternyata masih kosong — ia baru tahu nama
           perusahaannya belum diisi setelah membuat akun. */
        if (! Auth::guard('customer')->check()) {
            $request->session()->put(self::DRAFT, $data);

            /* Tujuan setelah masuk ditetapkan di sini karena permintaan ini
               tidak lewat middleware auth:customer lagi, jadi tidak ada yang
               mengisi url.intended untuknya. Dengan #daftar, yang bersangkutan
               kembali tepat ke formulirnya, bukan ke pucuk halaman Kemitraan
               yang panjang. */
            $request->session()->put(
                'url.intended',
                route('partnership.index', ['locale' => $locale]).'#daftar'
            );

            return redirect()->route('login', ['locale' => $locale]);
        }

        $registration = PartnershipRegistration::create([
            ...$data, 'status' => 'baru', 'locale' => app()->getLocale(), 'is_read' => false,
        ]);

        // Titipan isian dibuang di sini, bukan saat halaman formulir dibuka:
        // selama pendaftarannya belum tersimpan, isian itu masih dibutuhkan.
        $request->session()->forget(self::DRAFT);

        // Notify Admin Transaksi (+ super admins) of the new lead.
        $admins = User::where('is_active', true)
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin_transaksi', 'super_admin']))
            ->get();

        if ($admins->isNotEmpty()) {
            $notification = Notification::make()
                ->title('Pendaftaran Kemitraan Baru')
                ->body("{$registration->company_name} · {$registration->pic_name}")
                ->icon('heroicon-o-clipboard-document-list')
                ->success();

            try {
                $notification->actions([
                    Action::make('view')
                        ->label('Lihat pendaftaran')
                        ->url(PartnershipRegistrationResource::getUrl('edit', ['record' => $registration->getKey()], panel: 'admin'))
                        ->markAsRead(),
                ]);
            } catch (\Throwable $e) {
                // Skip action URL if route resolution is unavailable.
            }

            foreach ($admins as $admin) {
                $admin->notifyNow($notification->toDatabase());
            }
        }

        return back()->with('status', __('site.contact.success'));
    }
}
