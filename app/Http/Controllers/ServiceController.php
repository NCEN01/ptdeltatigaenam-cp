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
            // schedules ikut dimuat: kartu layanan menyebut jadwal terdekat
            // lewat nextSchedule(), dan tanpa ini tiap kartu menembak satu
            // kueri sendiri — sepuluh layanan berarti sepuluh kueri tambahan
            // di satu halaman.
            'categories' => ServiceCategory::where('is_active', true)
                ->with(['services' => fn ($q) => $q->where('is_active', true)
                    /* Hanya jadwal yang belum lewat. Satu-satunya pemakai
                       relasi ini di halaman tersebut adalah nextSchedule(),
                       yang menyaring koleksinya untuk tanggal mendatang lalu
                       membuang sisanya — jadi jadwal yang sudah berlalu dimuat
                       dari basis data hanya untuk langsung dilempar.

                       Sekarang bedanya tiga baris dari dua puluh empat. Tapi
                       tabel ini bertambah terus dan tidak pernah dibersihkan:
                       setiap angkatan yang selesai meninggalkan satu baris,
                       dan setelah dua tahun jadwal yang sudah lewat jumlahnya
                       berkali-kali lipat yang masih berlaku. Halaman Layanan
                       memuat seluruh kategori beserta seluruh layanannya, jadi
                       pemborosan itu terkali di setiap kartu.

                       endOfDay dipakai nextSchedule(), jadi pembatasnya di sini
                       tanggal hari ini — bukan waktu sekarang — supaya jadwal
                       yang digelar hari ini tidak hilang di tengah hari. */
                    ->with(['schedules' => fn ($s) => $s->where('is_active', true)
                        ->whereDate('start_date', '>=', now()->toDateString())
                        ->orderBy('start_date')])
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')])
                ->orderByDesc('is_featured')
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
            ->with([
                'category',
                'activities',
                'schedules' => fn ($q) => $q->where('is_active', true)->orderBy('start_date'),
                // Mentor yang dinonaktifkan di pustaka tidak ikut tampil, tanpa
                // perlu dilepas satu per satu dari tiap program yang diampunya.
                'mentors' => fn ($q) => $q->where('is_active', true),
            ])
            ->firstOrFail();

        return view('pages.services.show', [
            'service' => $service,

            // Benefit yang berlaku bagi program ini: miliknya sendiri bila ada,
            // kalau tidak daftar bawaannya. Resolusinya ada di model supaya
            // aturannya tidak tersebar ke tampilan.
            'benefits' => $service->resolvedBenefits(),

            // Ajakan konsultasi, satu teks untuk semua program. Nomornya boleh
            // kosong; tampilannya lalu menautkan ke halaman Kontak.
            'consult' => [
                'title' => Setting::getLocalized('program_consult_title'),
                'desc' => Setting::getLocalized('program_consult_desc'),
                'cta' => Setting::getLocalized('program_consult_cta'),
                'whatsapp' => trim((string) Setting::get('program_consult_whatsapp')),
            ],

            'related' => Service::where('is_active', true)
                ->where('service_category_id', $service->service_category_id)
                ->where('id', '!=', $service->id)
                ->with(['schedules' => fn ($q) => $q->where('is_active', true)->orderBy('start_date')])
                ->take(3)->get(),
        ]);
    }
}
