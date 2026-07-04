@php
    $id = app()->getLocale() === 'id';
    $scheduleCount = $service->schedules->count();

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
                        <p class="mt-2 font-display text-4xl">Rp {{ number_format((float) $service->price, 0, ',', '.') }}<span class="text-base font-normal text-navy-200"> / {{ $id ? 'peserta' : 'person' }}</span></p>
                        @if ($service->price_label)<p class="mt-1 text-sm text-navy-200">{{ $service->price_label }}</p>@endif
                    @else
                        <p class="mt-2 font-display text-3xl">{{ $id ? 'Hubungi kami' : 'Contact us' }}</p>
                    @endif
                    @if ($service->is_purchasable && $scheduleCount)
                        <a href="#daftar" class="btn-blue mt-6 w-full justify-center">{{ $id ? 'Daftar Sekarang' : 'Register Now' }}</a>
                        <p class="mt-4 text-center text-xs text-gold-soft">{{ $id ? '🔥 Kursi terbatas — jangan lewatkan kesempatan ini!' : '🔥 Limited seats — don’t miss this opportunity!' }}</p>
                    @else
                        <a href="{{ route('contact.index') }}" class="btn-blue mt-6 w-full justify-center">{{ __('site.cta.consult') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== EASY REGISTRATION · 3 STEPS (timeline) ===================== --}}
    <section class="section-sm relative overflow-hidden bg-white">
        <div class="pointer-events-none absolute inset-0 aurora-light opacity-70"></div>
        <div class="container relative">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <p class="eyebrow inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $id ? 'Cara Mendaftar' : 'How to Register' }}</p>
                <h2 class="mt-4 font-display text-3xl text-navy text-balance md:text-4xl">{{ $id ? 'Daftar mudah dalam 3 langkah' : 'Register easily in 3 steps' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-navy-500">{{ $id ? 'Prosesnya cepat & aman — amankan kursi Anda dalam hitungan menit.' : 'Fast & secure — reserve your seat in minutes.' }}</p>
            </div>

            <div class="relative mx-auto mt-14 max-w-5xl">
                {{-- connecting line (desktop) --}}
                <div class="pointer-events-none absolute inset-x-[16%] top-10 hidden h-0.5 bg-gradient-to-r from-navy-100 via-gold-soft/60 to-navy-100 md:block"></div>

                <div class="grid gap-10 md:grid-cols-3">
                    @foreach ($steps as $i => $step)
                        <div class="group relative text-center" data-aos="fade-up" data-aos-delay="{{ $i * 90 }}">
                            {{-- icon + number badge --}}
                            <div class="relative mx-auto grid h-20 w-20 place-items-center">
                                <span class="grid h-20 w-20 place-items-center rounded-full bg-white text-navy shadow-lift ring-1 ring-navy-100 transition-all duration-300 group-hover:-translate-y-1 group-hover:bg-navy group-hover:text-white">
                                    @if ($step[2] === 'calendar')
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="16" x="3" y="4" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                                    @elseif ($step[2] === 'users')
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13A4 4 0 0 1 16 11"/></svg>
                                    @else
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.4-3 7.6-7 9-4-1.4-7-4.6-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/></svg>
                                    @endif
                                </span>
                                <span class="absolute -right-1 -top-1 grid h-8 w-8 place-items-center rounded-full bg-gold font-display text-sm text-navy-950 shadow-gold">{{ $i + 1 }}</span>
                            </div>
                            <h3 class="mt-6 font-display text-xl text-navy">{{ $step[0] }}</h3>
                            <p class="mx-auto mt-2 max-w-xs text-sm leading-relaxed text-navy-500">{{ $step[1] }}</p>
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

    {{-- ===================== BODY + PURCHASE PANEL ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-neutral-50">
        <div class="container grid gap-12 lg:grid-cols-12">
            {{-- Description + activities --}}
            <div class="lg:col-span-7">
                @if ($service->description)
                    <div class="prose prose-lg max-w-none
                                prose-headings:font-display prose-headings:font-normal prose-headings:text-navy
                                prose-p:text-navy-600 prose-p:leading-relaxed
                                prose-li:text-navy-600 prose-a:text-sky-700 prose-a:font-medium prose-strong:text-navy
                                prose-img:rounded-2xl">
                        {!! $service->description !!}
                    </div>
                @endif

                @if ($service->activities->isNotEmpty())
                    <div class="mt-12">
                        <p class="eyebrow mb-2"><span class="rule-gold mr-3"></span>{{ $id ? 'Materi & Kegiatan' : 'Topics & Activities' }}</p>
                        <p class="mb-6 text-sm text-navy-500">{{ $id ? 'Rangkaian materi yang dirancang aplikatif dan siap pakai.' : 'A curriculum designed to be practical and ready to apply.' }}</p>
                        <div class="space-y-3">
                            @foreach ($service->activities as $activity)
                                <div class="group flex gap-5 rounded-2xl border border-navy-100 bg-white p-6 transition-all duration-300 hover:border-navy-200 hover:shadow-card">
                                    <span class="font-display text-2xl leading-none text-gold-deep">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div>
                                        <h3 class="font-display text-lg text-navy">{{ $activity->title }}</h3>
                                        @if ($activity->description)<p class="mt-1.5 text-pretty leading-relaxed text-navy-500">{{ $activity->description }}</p>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Purchase panel --}}
            <div class="lg:col-span-5" id="daftar">
                <div class="sticky top-28 overflow-hidden rounded-3xl border border-navy-100 bg-white shadow-lift">
                    {{-- Header --}}
                    <div class="border-b border-navy-100 bg-navy-950 p-6 text-white">
                        <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-gold-soft">{{ $id ? 'Amankan Kursi Anda' : 'Secure Your Seat' }}</p>
                        <h3 class="mt-2 font-display text-xl">{{ $id ? 'Pilih jadwal di bawah' : 'Pick a schedule below' }}</h3>
                        <p class="mt-2 text-sm text-navy-200">{{ $id ? 'Kursi terbatas — daftar sekarang sebelum kehabisan.' : 'Limited seats — register now before they’re gone.' }}</p>
                    </div>

                    {{-- Schedules --}}
                    <div class="space-y-3 p-5">
                        @forelse ($service->schedules as $schedule)
                            @php
                                $left = $schedule->quota ? max(0, $schedule->quota - $schedule->seats_taken) : null;
                                $full = $left !== null && $left <= 0;
                                $low = $left !== null && $left > 0 && $left <= 5;
                            @endphp
                            <div class="rounded-2xl border {{ $low ? 'border-amber-200' : 'border-navy-100' }} bg-white p-5 transition-shadow duration-300 hover:shadow-card">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-display text-lg text-navy">{{ $schedule->start_date?->translatedFormat('d M Y') }}</p>
                                        @if ($schedule->location)<p class="mt-0.5 text-sm text-navy-400">{{ $schedule->location }}</p>@endif
                                    </div>
                                    <span class="shrink-0 rounded-full bg-navy-50 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-navy-500">{{ $schedule->mode }}</span>
                                </div>

                                @if ($left !== null)
                                    <p class="mt-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-mono text-[10px] uppercase tracking-wider {{ $full ? 'bg-rose-50 text-rose-600' : ($low ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $full ? 'bg-rose-500' : ($low ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                        {{ $full ? ($id ? 'Kuota penuh' : 'Fully booked') : ($low ? ($id ? 'Hampir habis · sisa '.$left.' kursi!' : 'Almost gone · '.$left.' left!') : $left.' '.($id ? 'kursi tersisa' : 'seats left')) }}
                                    </p>
                                @endif

                                @if ($schedule->effectivePrice() > 0)
                                    <p class="mt-3 font-display text-xl text-navy">Rp {{ number_format($schedule->effectivePrice(), 0, ',', '.') }}<span class="text-sm font-normal text-navy-400"> / {{ $id ? 'peserta' : 'person' }}</span></p>
                                @endif

                                @if ($service->is_purchasable)
                                    @if ($full)
                                        <a href="{{ route('contact.index') }}" class="btn-ghost mt-4 w-full justify-center !py-2.5 text-sm">{{ $id ? 'Tanya Ketersediaan' : 'Ask Availability' }}</a>
                                    @elseif (Route::has('checkout.create'))
                                        <a href="{{ route('checkout.create', $schedule->id) }}" class="btn-blue mt-4 w-full justify-center !py-2.5 text-sm">{{ $id ? 'Daftar / Beli' : 'Register / Buy' }}</a>
                                    @else
                                        <a href="{{ route('contact.index') }}" class="btn-ghost mt-4 w-full justify-center !py-2.5 text-sm">{{ __('site.cta.consult') }}</a>
                                    @endif
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-navy-200 bg-white p-6 text-center">
                                <p class="text-sm text-navy-500">{{ $id ? 'Belum ada jadwal terbuka. Hubungi kami untuk jadwal khusus atau in-house training.' : 'No open schedules yet. Contact us for a custom or in-house batch.' }}</p>
                                <a href="{{ route('contact.index') }}" class="btn-blue mt-5 w-full justify-center">{{ __('site.cta.consult') }}</a>
                            </div>
                        @endforelse
                    </div>

                    @if ($service->is_purchasable && $service->schedules->isNotEmpty())
                        <div class="space-y-2 border-t border-navy-100 bg-neutral-50 px-6 py-5 text-xs text-navy-500">
                            <p class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ $id ? 'Pembayaran aman: QRIS, e-wallet, VA, & kartu (Midtrans).' : 'Secure payment: QRIS, e-wallet, VA & card (Midtrans).' }}
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.5 5 5.5.8-4 3.9 1 5.5L12 15.5 7 18.2l1-5.5-4-3.9 5.5-.8z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                                {{ $id ? 'Sertifikat resmi untuk setiap peserta yang lulus.' : 'Official certificate for every participant who passes.' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-white">
            <div class="container">
                <p class="eyebrow mb-8" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Layanan terkait' : 'Related services' }}</p>
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('services.show', $r->slug) }}" class="group flex h-full flex-col rounded-2xl border border-navy-100 bg-white p-6 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift">
                            @if ($r->category)<p class="font-mono text-[10px] uppercase tracking-wider text-gold-deep">{{ $r->category->name }}</p>@endif
                            <h3 class="mt-1.5 font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $r->title }}</h3>
                            @if ($r->short_description)<p class="mt-2 line-clamp-2 text-sm leading-relaxed text-navy-500">{{ $r->short_description }}</p>@endif
                            <span class="mt-auto flex items-center gap-2 pt-4 text-sm font-medium text-navy">
                                {{ $id ? 'Lihat detail' : 'View details' }}
                                <svg class="h-4 w-4 text-gold-deep transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
