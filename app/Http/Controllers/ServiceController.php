<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        // Seksi Agenda di halaman ini dinyalakan dan diatur lewat panel admin
        // (Konten > Agenda di Halaman Layanan). Saat dimatikan, query agendanya
        // pun tidak dijalankan: seksinya tidak dirender, jadi menjalankannya
        // hanya membebani tiap kunjungan tanpa hasil.
        $agendaActive = (bool) Setting::get('service_agenda_active', true);

        return view('pages.services.index', [
            'categories' => ServiceCategory::where('is_active', true)
                ->with(['services' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
                ->orderBy('sort_order')->get(),

            'agendaActive' => $agendaActive,
            'agendaTitle' => Setting::getLocalized('service_agenda_title'),
            'agendaIntro' => Setting::getLocalized('service_agenda_intro'),

            // Yang akan datang berdiri di depan, paling dekat tanggalnya lebih
            // dulu; yang sudah berakhir menyusul di belakang, paling baru lebih
            // dulu. Keduanya ditandai berbeda di tampilannya, jadi yang lewat
            // tidak menyamar sebagai jadwal yang masih bisa diikuti.
            //
            // Agenda yang sudah berakhir bisa dimatikan dari panel admin. Ia
            // berguna sebagai bukti bahwa kelasnya memang berjalan, tapi di
            // periode yang sepi ia bisa mendominasi dan membuat daftarnya
            // terbaca seolah tidak ada yang akan datang.
            'agendas' => $agendaActive
                ? Agenda::published()
                    ->when(
                        ! (bool) Setting::get('service_agenda_show_past', true),
                        fn ($q) => $q->where(fn ($w) => $w->whereNull('starts_at')->orWhere('starts_at', '>=', now()->startOfDay())),
                    )
                    ->orderByRaw('(starts_at IS NULL OR starts_at >= ?) DESC', [now()->startOfDay()])
                    ->orderByRaw('CASE WHEN starts_at >= ? THEN starts_at END ASC', [now()->startOfDay()])
                    ->orderByDesc('starts_at')
                    ->take(max(1, (int) Setting::get('service_agenda_limit', 6)))
                    ->get()
                : collect(),

            'agendaFile' => $agendaActive ? $this->agendaFile() : null,
        ]);
    }

    /**
     * Berkas jadwal yang diunggah admin, beserta keterangan yang dibutuhkan
     * tampilannya: jenis dan ukurannya.
     *
     * Keduanya dibaca dari berkasnya sendiri, bukan diketik admin. Angka
     * ukuran yang diketik tangan akan salah begitu berkasnya diganti, dan
     * tidak ada yang mengingatkan. Null bila belum ada berkas atau berkasnya
     * sudah tidak ada di penyimpanan, sehingga tampilannya cukup memeriksa
     * satu nilai.
     */
    private function agendaFile(): ?array
    {
        $path = Setting::get('service_agenda_file');

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $bytes = Storage::disk('public')->size($path);

        return [
            'url' => Storage::url($path),
            'ext' => strtoupper(pathinfo($path, PATHINFO_EXTENSION)),
            'size' => $bytes >= 1048576
                ? round($bytes / 1048576, 1).' MB'
                : max(1, (int) round($bytes / 1024)).' KB',
            'label' => Setting::getLocalized('service_agenda_file_label'),
            'note' => Setting::getLocalized('service_agenda_file_note'),
        ];
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
