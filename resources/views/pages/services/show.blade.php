@php
    $id = app()->getLocale() === 'id';
    $scheduleCount = $service->schedules->count();

    // Satuan harga. Sebelumnya "peserta" ditulis mati di tiga tempat, sehingga
    // layanan yang dijual per penugasan (headhunter, konsultasi) ikut tertulis
    // "/ peserta". Kolom price_label memang disediakan untuk ini.
    $priceUnit = trim((string) $service->price_label) ?: ($id ? 'peserta' : 'person');

    // Easy registration — 3 steps [title, description, icon].
    $steps = $id ? [
        ['Pilih Jadwal', 'Tentukan batch & tanggal yang paling sesuai untuk Anda.', 'calendar'],
        ['Isi Data Peserta', 'Lengkapi data pemesan dan peserta yang akan mengikuti.', 'users'],
        ['Bayar Aman', 'Selesaikan pembayaran via Midtrans (QRIS, e-wallet, VA, kartu).', 'shield'],
    ] : [
        ['Choose a Schedule', 'Pick the batch & date that best fits you.', 'calendar'],
        ['Participant Details', 'Fill in the buyer and participant information.', 'users'],
        ['Pay Securely', 'Complete payment via Midtrans (QRIS, e-wallet, VA, card).', 'shield'],
    ];
@endphp

<x-layout :title="$service->title" :description="$service->short_description">
    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden bg-navy-950 pt-36 pb-16 text-white md:pt-44 md:pb-24">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-60"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>

        <div class="container relative grid gap-12 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <a href="{{ route('services.index') }}" class="group mb-6 inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-navy-200 transition-colors hover:text-gold-soft">
                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $service->category?->name }}
                </a>
                <h1 class="text-display-xl text-balance">{{ $service->title }}</h1>
                @if ($service->short_description)<p class="mt-6 max-w-2xl text-lg text-navy-100 text-pretty">{{ $service->short_description }}</p>@endif
                <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-navy-200">
                    @if ($service->duration)<span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $service->duration }}</span>@endif
                    @if ($service->location)<span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $service->location }}</span>@endif
                    @if ($service->is_purchasable && $scheduleCount)<span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $scheduleCount }} {{ $id ? 'jadwal tersedia' : 'schedules available' }}</span>@endif
                </div>
            </div>

            {{-- Price / CTA card --}}
            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-white/10 bg-white/[0.05] p-7 backdrop-blur-xl">
                    <p class="font-mono text-[10px] uppercase tracking-label text-gold-soft">{{ $id ? 'Investasi' : 'Investment' }}</p>
                    @if ($service->price > 0)
                        @if ($service->hasDiscount())
                            <div class="mt-2 flex flex-wrap items-center gap-2.5">
                                <span class="font-display text-xl text-white/45 line-through">Rp {{ number_format((float) $service->discount_original_price, 0, ',', '.') }}</span>
                                <span class="rounded-md bg-gold px-2 py-0.5 font-mono text-[11px] font-bold uppercase tracking-wide text-navy-950">{{ $id ? 'Hemat' : 'Save' }} {{ $service->discountPercent() }}%</span>
                            </div>
                            <p class="mt-1 font-display text-4xl text-white">Rp {{ number_format((float) $service->price, 0, ',', '.') }}<span class="text-base font-normal text-navy-200"> / {{ $priceUnit }}</span></p>
                            <p class="mt-2.5 text-xs leading-relaxed text-gold-soft">{{ $id ? 'Harga promo berlaku terbatas, harga akan naik setelah periode promo berakhir.' : 'Promo price for a limited time, the price will rise after the promo ends.' }}</p>
                        @else
                            <p class="mt-2 font-display text-4xl">Rp {{ number_format((float) $service->price, 0, ',', '.') }}<span class="text-base font-normal text-navy-200"> / {{ $priceUnit }}</span></p>
                        @endif
                    @else
                        <p class="mt-2 font-display text-3xl">{{ $id ? 'Hubungi kami' : 'Contact us' }}</p>
                    @endif
                    @if ($service->is_purchasable && $scheduleCount)
                        <a href="#daftar" class="btn-blue mt-6 w-full justify-center">{{ $id ? 'Daftar Sekarang' : 'Register Now' }}</a>
                        <p class="mt-4 text-center text-xs text-gold-soft">{{ $id ? '🔥 Kursi terbatas, jangan lewatkan kesempatan ini!' : '🔥 Limited seats, don’t miss this opportunity!' }}</p>
                    @else
                        <a href="{{ route('contact.index') }}" class="btn-blue mt-6 w-full justify-center">{{ __('site.cta.consult') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== EASY REGISTRATION · 3 STEPS (timeline) ===================== --}}
    {{-- Tirai aurora dilepas. Seksi ini isinya hanya tiga langkah pendek, dan
         gradasi bergerak di belakangnya menuntut perhatian yang tidak ada
         gunanya diberikan di sini — pembaca sedang membaca urutan, bukan
         mengagumi latar. --}}
    <section class="section-sm bg-white">
        <div class="container">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                {{-- .kicker, bukan .eyebrow: .eyebrow bernilai display:none di
                     seluruh situs, jadi barisnya selama ini tidak pernah tampil
                     sekali pun. --}}
                <p class="kicker">{{ $id ? 'Cara Mendaftar' : 'How to Register' }}</p>
                <h2 class="mt-4 text-display-lg font-bold text-navy">{{ $id ? 'Daftar mudah dalam 3 langkah' : 'Register easily in 3 steps' }}</h2>
                <p class="mx-auto mt-4 max-w-xl leading-relaxed text-slate-600">{{ $id ? 'Prosesnya cepat dan aman. Amankan kursi Anda dalam hitungan menit.' : 'Fast and secure. Reserve your seat in minutes.' }}</p>
            </div>

            {{-- Disederhanakan. Sebelumnya tiap langkah memakai lingkaran 80px
                 berbayang tebal, ikon 32px di dalamnya, DAN lencana nomor emas
                 yang menempel di pojoknya — tiga benda bertumpuk untuk satu
                 langkah, dan nomornya dinyatakan dua kali: sekali oleh urutan
                 kolomnya, sekali oleh lencananya.

                 Kini satu ubin 44px berisi ikon 20px, dengan nomor sebagai
                 angka kecil di atas judulnya. Garis penghubungnya tetap: ia
                 yang menyatakan ketiganya satu urutan, bukan tiga pilihan. --}}
            <div class="relative mx-auto mt-10 max-w-4xl md:mt-12">
                <div class="pointer-events-none absolute inset-x-[16%] top-[22px] h-px bg-gradient-to-r from-navy-100 via-navy-200 to-navy-100"></div>

                <div class="grid grid-cols-3 gap-3 sm:gap-6 md:gap-10">
                    @foreach ($steps as $i => $step)
                        <div class="group relative text-center" data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                            <span class="relative mx-auto grid h-11 w-11 place-items-center rounded-xl border border-navy-100 bg-white text-navy-600 shadow-card transition-colors duration-300 group-hover:border-navy-600 group-hover:bg-navy-600 group-hover:text-white">
                                @if ($step[2] === 'calendar')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="16" x="3" y="4" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                                @elseif ($step[2] === 'users')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13A4 4 0 0 1 16 11"/></svg>
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.4-3 7.6-7 9-4-1.4-7-4.6-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>
                                @endif
                            </span>

                            <p class="mt-4 font-mono text-[11px] text-slate-500">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</p>
                            <h3 class="mt-1 font-display text-sm font-semibold leading-snug text-navy md:text-base">{{ $step[0] }}</h3>
                            <p class="mx-auto mt-1.5 max-w-xs text-xs leading-relaxed text-slate-600 md:text-[13px]">{{ $step[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($service->is_purchasable && $scheduleCount)
                <div class="mt-14 text-center" data-aos="fade-up">
                    <a href="#daftar" class="btn-blue">{{ $id ? 'Pilih Jadwal Sekarang' : 'Choose a Schedule Now' }}</a>
                </div>
            @endif
        </div>
    </section>

    @php
        // Spanduk khusus bila admin mengunggahnya; kalau tidak, gambar yang
        // sudah dipakai kartu program di halaman daftar. Program yang belum
        // sempat disiapkan spanduknya tetap punya bidang gambar, dan gambar
        // itu pula yang baru saja dilihat pembaca saat mengklik kartunya —
        // jadi ia menegaskan "ini program yang tadi", bukan mengejutkan.
        $bannerSrc = $service->banner_image ?: $service->image;
    @endphp
    {{-- ===================== BODY + PURCHASE PANEL ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-white">
        <div class="container grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7">
                {{-- Spanduk duduk di kolom ini, bukan membentang selebar halaman
                     di atasnya. Selebar penuh ia setinggi seperempat layar dan
                     mendorong seluruh isi halaman turun sebelum satu kalimat pun
                     terbaca; di kolom 7/12 ia sejajar dengan kartu jadwal di
                     kanannya, dan keduanya terbaca sebagai satu baris pembuka.

                     16:9, bukan 21:9: pada lebar kolom ini 21:9 tinggal setinggi
                     sekitar 160px dan fotonya terpotong jadi pita sempit.

                     Keterangan cara pelaksanaan, lokasi, dan durasi ditempelkan
                     di tepi bawahnya — "di mana dan bagaimana" adalah pertanyaan
                     yang sudah terbentuk saat pembaca sampai di sini. --}}
                @if ($bannerSrc)
                    @php
                        $bModeLabel = $service->modeLabel();
                        $bPlace = $service->location;
                    @endphp
                    <div class="relative mb-8 overflow-hidden rounded-2xl bg-navy-100 shadow-card" data-aos="fade-up">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($bannerSrc) }}"
                             alt="{{ $service->title }}"
                             loading="lazy" decoding="async"
                             class="aspect-[16/9] w-full object-cover">

                        @if ($bModeLabel || $bPlace || $service->duration)
                            <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/85 via-navy-950/35 to-transparent p-4 md:p-5">
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 font-mono text-[11px] uppercase tracking-normal text-white">
                                    @if ($bModeLabel)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg>
                                            {{ $bModeLabel }}
                                        </span>
                                    @endif
                                    @if ($bPlace)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $bPlace }}
                                        </span>
                                    @endif
                                    @if ($service->duration)
                                        <span class="inline-flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-sky-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                                            {{ $service->duration }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($service->description)
                    <div class="prose prose-lg max-w-none
                                prose-headings:font-display prose-headings:font-normal prose-headings:text-navy
                                prose-p:text-slate-700 prose-p:leading-relaxed
                                prose-li:text-slate-700 prose-a:text-sky-700 prose-a:font-medium prose-strong:text-navy
                                prose-img:rounded-2xl">
                        {!! \App\Helpers\HtmlSanitizer::clean($service->description) !!}
                    </div>
                @endif

                {{-- Materi dipindah keluar dari kolom ini ke seksi lebar penuh di
                     bawah. Di lebar 7/12 tiap kartu materi tinggal sekitar 540px
                     dan isinya berdesakan dalam satu lajur sempit, sementara
                     separuh kanan halaman menganggur di bawah panel pembelian
                     yang jauh lebih pendek. --}}
            </div>

            {{-- Purchase panel --}}
            {{-- Pembungkus sticky memayungi panel jadwal DAN kartu bagikan di
                 bawahnya. Kalau hanya panelnya yang sticky, kartu bagikan
                 tertinggal menggulir sendiri dan di layar panjang ia berpisah
                 jauh dari panel yang menempel di atasnya. --}}
            <div class="lg:col-span-5" id="daftar">
                <div class="sticky top-28 space-y-5">
                <div class="overflow-hidden rounded-3xl border border-navy-100 bg-white shadow-lift">
                    {{-- Header — same aurora/blue-gradient treatment as the hero above --}}
                    <div class="relative overflow-hidden border-b border-navy-100 bg-navy-950 p-6 text-white">
                        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-60"></div>
                        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>
                        {{-- Layanan per penugasan hanya membuka satu batch, jadi tidak ada
                             yang bisa "dipilih". Teksnya menyesuaikan agar tidak menjanjikan
                             pilihan yang tidak ada. --}}
                        <div class="relative">
                            <p class="font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $id ? 'Amankan Kursi Anda' : 'Secure Your Seat' }}</p>
                            <h3 class="mt-2 font-display text-xl">
                                @if ($scheduleCount > 1)
                                    {{ $id ? 'Pilih jadwal di bawah' : 'Pick a schedule below' }}
                                @else
                                    {{ $id ? 'Jadwal terdekat' : 'Next available schedule' }}
                                @endif
                            </h3>
                            <p class="mt-2 text-sm text-navy-200">{{ $id ? 'Kursi terbatas, daftar sekarang sebelum kehabisan.' : 'Limited seats, register now before they’re gone.' }}</p>
                        </div>
                    </div>

                    {{-- Schedules --}}
                    <div class="space-y-3 p-5">
                        @php
                            $left = $service->quota ? max(0, $service->quota - $service->seats_taken) : null;
                            $full = $left !== null && $left <= 0;
                            $low = $left !== null && $left > 0 && $left <= 5;
                        @endphp
                        @forelse ($service->schedules as $schedule)
                            <div class="rounded-2xl border {{ $low ? 'border-amber-200' : 'border-navy-100' }} bg-white p-5 transition-shadow duration-300 hover:shadow-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-display text-lg text-navy">{{ $schedule->start_date?->translatedFormat('d M Y') }}</p>
                                        @if ($schedule->start_time)
                                            <p class="mt-1 font-mono text-xs text-slate-600">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                @if ($schedule->end_time) - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB @endif
                                            </p>
                                        @endif
                                        @if ($schedule->location)<p class="mt-0.5 text-sm text-slate-500">{{ $schedule->location }}</p>@endif
                                    </div>
                                    <span class="shrink-0 rounded-full bg-navy-50 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-slate-600">{{ $service->mode }}</span>
                                </div>

                                @if ($left !== null)
                                    <div class="mt-3 flex items-center gap-3">
                                        {{-- Quota bar --}}
                                        <div class="flex-1 h-2 rounded-full bg-navy-100 overflow-hidden">
                                            <div class="h-full rounded-full transition-all {{ $full ? 'bg-rose-500' : ($low ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                                 style="width: {{ $service->quota > 0 ? round(($service->seats_taken / $service->quota) * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="shrink-0 font-mono text-[10px] uppercase tracking-wider {{ $full ? 'text-rose-600' : ($low ? 'text-amber-700' : 'text-emerald-700') }}">
                                            {{ $full ? ($id ? 'Penuh' : 'Full') : $left.'/'.$service->quota }}
                                        </span>
                                    </div>
                                @endif

                                @if ($schedule->effectivePrice() > 0)
                                    <p class="mt-3 font-display text-xl text-navy">Rp {{ number_format($schedule->effectivePrice(), 0, ',', '.') }}<span class="text-sm font-normal text-slate-500"> / {{ $priceUnit }}</span></p>
                                @endif

                                @if ($service->is_purchasable)
                                    @if ($full)
                                        <button disabled class="mt-4 w-full justify-center !py-2.5 text-sm rounded-full border border-navy-200 bg-navy-50 text-slate-500 cursor-not-allowed font-medium">
                                            {{ $id ? 'Kuota Penuh' : 'Fully Booked' }}
                                        </button>
                                    @elseif (Route::has('checkout.create'))
                                        <a href="{{ route('checkout.create', $schedule->id) }}" class="btn-blue mt-4 w-full justify-center !py-2.5 text-sm">{{ $id ? 'Daftar / Beli' : 'Register / Buy' }}</a>
                                    @else
                                        <a href="{{ route('contact.index') }}" class="btn-ghost mt-4 w-full justify-center !py-2.5 text-sm">{{ __('site.cta.consult') }}</a>
                                    @endif
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-navy-200 bg-white p-6 text-center">
                                <p class="text-sm text-slate-600">{{ $id ? 'Belum ada jadwal terbuka. Hubungi kami untuk jadwal khusus atau in-house training.' : 'No open schedules yet. Contact us for a custom or in-house batch.' }}</p>
                                <a href="{{ route('contact.index') }}" class="btn-blue mt-5 w-full justify-center">{{ __('site.cta.consult') }}</a>
                            </div>
                        @endforelse
                    </div>

                    @if ($service->is_purchasable && $service->schedules->isNotEmpty())
                        <div class="space-y-2 border-t border-navy-100 bg-neutral-50 px-6 py-5 text-xs text-slate-600">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ $id ? 'Pembayaran aman: QRIS, e-wallet, VA, & kartu (Midtrans).' : 'Secure payment: QRIS, e-wallet, VA & card (Midtrans).' }}
                            </div>
                            <p class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.5 5 5.5.8-4 3.9 1 5.5L12 15.5 7 18.2l1-5.5-4-3.9 5.5-.8z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                                {{ $id ? 'Sertifikat resmi untuk setiap peserta yang lulus.' : 'Official certificate for every participant who passes.' }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Bagikan program ini. Bentuknya sama dengan kartu bagikan di
                     halaman artikel — kotak abu-abu bergaris rambut, satu baris
                     ajakan, lalu deretan lambang jaringan — dan memakai komponen
                     yang sama persis, jadi daftar jaringannya tidak bisa
                     menyimpang antara kedua halaman.

                     Di bawah panel jadwal, bukan di dasar halaman: orang yang
                     membagikan program biasanya sedang meneruskannya ke atasan
                     atau rekan yang akan ikut, dan keputusan itu muncul saat ia
                     sedang menimbang jadwal dan harganya. --}}
                <div class="rounded-2xl border border-navy-100 bg-neutral-50 p-6">
                    <p class="mb-4 text-sm font-semibold text-navy">
                        {{ $id ? 'Bagikan program ini' : 'Share this programme' }}
                    </p>
                    <x-share-buttons :url="route('services.show', $service->slug)" :title="$service->title" />
                </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== MENTOR PENGAMPU =====================
         Di atas materi, bukan di bawahnya: siapa yang mengajar ikut menentukan
         apakah materinya layak dibaca, dan pembaca menilai keduanya sekaligus.
         Dari pustaka mentor bersama, jadi satu orang yang mengampu beberapa
         program cukup didaftarkan sekali. --}}
    @if ($service->mentors->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-neutral-50">
            <div class="container">
                <h2 class="font-display text-2xl font-bold leading-snug text-navy md:text-3xl" data-aos="fade-up">
                    {{ $id ? 'Mentor yang akan membimbing Anda' : 'The mentors who will guide you' }}
                </h2>

                <div class="mt-10 grid gap-5 {{ $service->mentors->count() > 1 ? 'lg:grid-cols-2' : '' }}">
                    @foreach ($service->mentors as $mentor)
                        <article class="card flex gap-5 p-5 sm:gap-6 sm:p-6" data-aos="fade-up" data-aos-delay="{{ min($loop->index, 3) * 60 }}">
                            {{-- Potret tegak di kolom kiri. Bila mentornya belum
                                 punya foto, huruf depan namanya yang mengisi —
                                 lingkaran kosong terbaca sebagai gambar yang gagal
                                 dimuat, dan itu menurunkan kepercayaan pada kartu
                                 yang justru tugasnya membangun kepercayaan. --}}
                            <div class="h-28 w-24 shrink-0 overflow-hidden rounded-2xl bg-navy-100 sm:h-32 sm:w-28">
                                @if ($mentor->photo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($mentor->photo) }}"
                                         alt="{{ $mentor->name }}" loading="lazy" decoding="async"
                                         class="h-full w-full object-cover object-top">
                                @else
                                    <span class="grid h-full w-full place-items-center bg-gradient-to-br from-navy-700 to-navy-950 font-display text-3xl font-bold text-white" aria-hidden="true">{{ $mentor->initial }}</span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-display text-lg font-semibold leading-snug text-navy">{{ $mentor->name }}</h3>
                                @if ($mentor->role)
                                    <p class="mt-0.5 font-mono text-[11px] uppercase tracking-normal text-sky-700">{{ $mentor->role }}</p>
                                @endif
                                @if ($mentor->bio)
                                    <p class="mt-2.5 text-pretty text-sm leading-relaxed text-slate-600">{{ $mentor->bio }}</p>
                                @endif
                                @if ($mentor->linkedin_url)
                                    <a href="{{ $mentor->linkedin_url }}" target="_blank" rel="noopener"
                                       class="mt-3 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-navy-50 text-navy-600 transition-colors duration-200 hover:bg-navy-600 hover:text-white"
                                       aria-label="LinkedIn {{ $mentor->name }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.63-1.85 3.36-1.85 3.6 0 4.27 2.37 4.27 5.45zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.55V9h3.57zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.22.79 24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== RANGKAIAN MATERI =====================
         Pindah ke lebar penuh dan menjadi kisi dua kolom. Nomornya besar dan
         samar di belakang judulnya, bukan sebagai lajur tersendiri di kiri:
         di kisi dua kolom lajur nomor memakan lebar yang justru dibutuhkan
         keterangannya, dan urutan materi memang bukan hal pertama yang dicari
         mata. --}}
    @if ($service->activities->isNotEmpty())
        <section class="bg-neutral-50 pb-14 md:pb-20">
            <div class="container">
                <div class="max-w-2xl" data-aos="fade-up">
                    <h2 class="font-display text-2xl font-bold leading-snug text-navy md:text-3xl">
                        {{ $id ? 'Rangkaian materi yang akan Anda dapat' : 'The curriculum you will receive' }}
                    </h2>
                    <p class="mt-4 leading-relaxed text-slate-600 text-pretty">
                        {{ $id
                            ? 'Disusun berurutan dari dasar sampai penerapannya di pekerjaan, bukan kumpulan topik lepas.'
                            : 'Sequenced from fundamentals through to application at work, not a loose collection of topics.' }}
                    </p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2">
                    @foreach ($service->activities as $activity)
                        <article class="card card-hover group relative overflow-hidden p-6 md:p-7" data-aos="fade-up" data-aos-delay="{{ min($loop->index % 4, 3) * 50 }}">
                            {{-- Nomor raksasa yang separuh keluar dari pojok kanan
                                 atas. Ia penanda urutan, bukan isi, jadi ia boleh
                                 terpotong — dan terpotong justru membuatnya tidak
                                 pernah tertukar dengan tulisan yang harus dibaca. --}}
                            <span class="pointer-events-none absolute -right-2 -top-4 select-none font-display text-[5rem] font-bold leading-none text-navy-50 transition-colors duration-300 group-hover:text-navy-100" aria-hidden="true">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <div class="relative">
                                <span class="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-normal text-sky-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-sky-600" aria-hidden="true"></span>
                                    {{ $id ? 'Materi' : 'Topic' }} {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <h3 class="mt-2.5 font-display text-lg font-semibold leading-snug text-navy md:text-xl">{{ $activity->title }}</h3>
                                @if ($activity->description)
                                    <p class="mt-2 text-pretty text-sm leading-relaxed text-slate-600">{{ $activity->description }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== YANG KAMU DAPATKAN =====================
         Daftar benefit: milik program ini bila diisi, kalau tidak daftar
         bawaannya. Resolusinya dikerjakan model, jadi tampilan di sini tidak
         perlu tahu mana yang sedang dipakainya. --}}
    @php
        $consultTitle = $consult['title'] ?: ($id ? 'Konsultasi Program Gratis' : 'Free programme consultation');
        $consultDesc = $consult['desc'] ?: ($id
            ? 'Punya pertanyaan dan ingin berbicara langsung dengan tim kami sebelum mendaftar? Ceritakan kebutuhan Anda, kami bantu memilih jalurnya.'
            : 'Questions before you register? Tell us what you need and we will help you pick the right path.');
        $consultCta = $consult['cta'] ?: ($id ? 'Konsultasi Sekarang' : 'Start a consultation');
        $consultHref = $consult['whatsapp'] !== ''
            ? 'https://wa.me/'.preg_replace('/\D/', '', $consult['whatsapp']).'?text='.rawurlencode(($id ? 'Halo Delta Tiga Enam, saya ingin berkonsultasi soal program: ' : 'Hello Delta Tiga Enam, I would like to ask about this programme: ').$service->title)
            : route('contact.index');
    @endphp
    {{-- Seksinya selalu tampil, bukan hanya ketika ada benefit: ajakan
         konsultasi di dalamnya berlaku bagi program mana pun. Yang bersyarat
         hanya daftar benefitnya. --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
            <div class="absolute inset-0 bg-gradient-to-tr from-navy-950 via-navy-900 to-navy-800"></div>
            <div class="pointer-events-none absolute -right-24 -top-32 h-[32rem] w-[32rem] rounded-full bg-sky-500/20 blur-3xl" data-parallax="0.08"></div>
            <div class="pointer-events-none absolute inset-0 grain opacity-20"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

            <div class="container relative section-sm">
                <div class="max-w-2xl" data-aos="fade-up">
                    <p class="kicker-on-dark">{{ $id ? 'Fasilitas' : 'What is included' }}</p>
                    <h2 class="mt-4 font-display text-2xl font-bold leading-snug text-white md:text-3xl">{{ $id ? 'Yang Anda dapatkan' : 'What you receive' }}</h2>
                </div>

                @if ($benefits->isNotEmpty())
                <ul class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($benefits as $benefit)
                        <li class="glass-surface glass-card flex gap-4 p-5 md:p-6" data-aos="fade-up" data-aos-delay="{{ min($loop->index % 3, 2) * 60 }}">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/10 text-sky-400 ring-1 ring-white/10" aria-hidden="true">
                                <x-category-icon :name="$benefit->icon" class="h-5 w-5" />
                            </span>
                            <div class="min-w-0">
                                <p class="font-display text-base font-semibold leading-snug text-white">{{ $benefit->title }}</p>
                                @if ($benefit->description)
                                    <p class="mt-1.5 text-pretty text-[13.5px] leading-relaxed text-navy-100">{{ $benefit->description }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                @endif

                {{-- Konsultasi gratis, di dalam pita yang sama dengan fasilitas.
                     Sebelumnya ia seksi tersendiri berlatar putih dengan pita
                     biru di dalamnya — tiga latar berbeda beruntun (gelap,
                     putih, biru) untuk dua hal yang sebenarnya satu tawaran:
                     ini yang Anda dapatkan, dan ini cara menanyakannya.

                     Bentuknya mengikuti ajakan di halaman Sertifikat: garis
                     rambut sebagai pemisah, tulisan di kiri, tombol di kanan,
                     tanpa panel sendiri. Panel kaca sempat dipakai di sini dan
                     dilepas — di dalam pita yang kartunya juga kaca, panel kaca
                     kedua terbaca sebagai kartu benefit raksasa, bukan sebagai
                     ajakan yang berbeda jenisnya.

                     Dua tombol, bukan satu, dengan pola yang sama: tindakan
                     utama berisi, konsultasi bergaris di sebelahnya. Tombol
                     jadwal hanya muncul bila programnya memang bisa dipesan dan
                     jadwalnya ada — kalau tidak, konsultasi berdiri sendiri. --}}
                <div class="mt-14 grid gap-6 border-t border-white/10 pt-10 lg:grid-cols-12 lg:items-center lg:gap-10" data-aos="fade-up">
                    <div class="lg:col-span-7">
                        <h3 class="text-2xl leading-snug text-white text-balance md:text-3xl">{{ $consultTitle }}</h3>
                        <p class="mt-3 max-w-xl text-pretty leading-relaxed text-navy-100">{{ $consultDesc }}</p>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row lg:col-span-5 lg:justify-end">
                        @if ($service->is_purchasable && $scheduleCount)
                            <a href="#daftar" class="btn-blue justify-center">{{ $id ? 'Pilih Jadwal' : 'Choose a schedule' }}</a>
                        @endif
                        <a href="{{ $consultHref }}"
                           @if ($consult['whatsapp'] !== '') target="_blank" rel="noopener" @endif
                           class="btn-ghost-light justify-center">{{ $consultCta }}</a>
                    </div>
                </div>
            </div>
        </section>

    @if ($related->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-white">
            <div class="container">
                <p class="kicker mb-8" data-aos="fade-up">{{ $id ? 'Layanan terkait' : 'Related services' }}</p>
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('services.show', $r->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift">
                            <div class="relative aspect-[3/2] overflow-hidden bg-navy-100">
                                @if ($r->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($r->image) }}" alt="{{ $r->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-navy-200 to-navy-100"></div>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                @if ($r->category)<p class="font-mono text-[10px] uppercase tracking-wider text-gold-deep">{{ $r->category->name }}</p>@endif
                                <h3 class="mt-1.5 font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $r->title }}</h3>
                                @if ($r->short_description)<p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $r->short_description }}</p>@endif
                                <span class="mt-auto flex items-center gap-2 pt-4 text-sm font-medium text-navy">
                                    {{ $id ? 'Lihat detail' : 'View details' }}
                                    <svg class="h-4 w-4 text-gold-deep transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
