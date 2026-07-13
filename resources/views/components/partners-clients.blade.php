@props(['partners' => null, 'clients' => null])

@php
    $partners = $partners ?? collect();
    $clients = $clients ?? collect();
    $isId = app()->getLocale() === 'id';

    // Repeat clients so a single marquee "half" fills the viewport, then render
    // two identical halves for a seamless, never-ending -50% loop.
    $repeat = function ($col, $min = 16) {
        if (! $col || $col->isEmpty()) {
            return collect();
        }
        $times = max(2, (int) ceil($min / max(1, $col->count())));
        $out = collect();
        for ($i = 0; $i < $times; $i++) {
            $out = $out->concat($col);
        }
        return $out;
    };

    $klien = $repeat($clients, 16);

    // Desktop column count grows with the number of partners, so the cards get
    // smaller automatically as more mitra are added (kept to the wireframe's 3 up to 6).
    $pCount = $partners->count();
    $lgCols = match (true) {
        $pCount <= 6 => 3,
        $pCount <= 12 => 4,
        $pCount <= 20 => 5,
        default => 6,
    };
@endphp

@if ($partners->isNotEmpty() || $clients->isNotEmpty())
    {{-- ONE unified section (Mitra + Klien) on a single background image, using the same
         gradient treatment as the "Alasan Memilih Kami / Keunggulan" band. --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <img src="https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?auto=format&fit=crop&w=1920&q=80"
             alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-navy-950/82"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/75 via-navy-950/45 to-navy-950/88"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-20"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        {{-- ===================== MITRA — centered; logo box on top, name + reg. number below ===================== --}}
        @if ($partners->isNotEmpty())
            <div class="relative container pt-14 md:pt-20 {{ $clients->isEmpty() ? 'pb-14 md:pb-20' : 'pb-10 md:pb-12' }}">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="text-display-lg font-semibold text-white text-balance">{{ $isId ? 'Mitra Kami' : 'Our Partners' }}</h2>
                    <p class="mx-auto mt-4 max-w-xl text-sm italic leading-relaxed text-white/75 md:text-base">
                        {{ $isId ? 'Siap berkolaborasi menciptakan tenaga yang berkualitas.' : 'Ready to collaborate in creating quality talent.' }}
                    </p>
                </div>

                <div class="mitra-grid mx-auto mt-10 max-w-3xl gap-3.5 md:mt-12 md:gap-4" style="--mitra-cols: {{ $lgCols }};">
                    @foreach ($partners as $partner)
                        <div class="group flex flex-col rounded-2xl bg-white/10 p-2.5 ring-1 ring-white/15 backdrop-blur-md transition duration-300 hover:-translate-y-1 hover:bg-white/[0.16] hover:ring-white/25"
                             data-aos="fade-up" data-aos-delay="{{ ($loop->index % $lgCols) * 70 }}">
                            {{-- White logo box (upper area) — logo centered --}}
                            <div class="flex aspect-[4/3] w-full items-center justify-center overflow-hidden rounded-xl bg-white p-3">
                                @if ($partner->logo)
                                    <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-full max-w-full object-contain">
                                @else
                                    <span class="text-center font-display text-xs font-bold italic text-navy">{{ $partner->name }}</span>
                                @endif
                            </div>
                            {{-- Name + registration number --}}
                            <div class="mt-2.5 text-center">
                                <p class="font-display text-[13px] font-bold italic leading-tight text-white text-balance md:text-sm">{{ $partner->name }}</p>
                                @if ($partner->registration_number)
                                    <p class="mt-0.5 font-mono text-[11px] text-white/55">{{ $partner->registration_number }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===================== KLIEN — clean white logo band: the logos' white backgrounds blend
             in (no boxes), shown grayscale and revealing their colour on hover ===================== --}}
        @if ($clients->isNotEmpty())
            <div class="relative bg-white pb-11 pt-10 md:pb-12 md:pt-12" data-aos="fade-up">
                <div class="container flex flex-col gap-6 md:flex-row md:items-center md:gap-10">
                    {{-- Left: heading + description --}}
                    <div class="shrink-0 md:w-60 lg:w-72">
                        <h3 class="font-display text-2xl font-bold text-navy text-balance md:text-3xl">{{ $isId ? 'Klien Kami' : 'Our Clients' }}</h3>
                        <span class="mt-2.5 block h-0.5 w-10 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            {{ $isId ? 'Klien yang telah menggunakan layanan kami.' : 'Clients who have used our services.' }}
                        </p>
                    </div>
                    {{-- Right: logos run left, grayscale → colour on hover. Fade both edges (clean on
                         mobile where it stacks full-width; near the heading on desktop). --}}
                    <div class="mask-fade-x relative w-full min-w-0 flex-1 overflow-hidden">
                        <div class="flex w-max items-center gap-10 animate-marquee [will-change:transform] md:gap-14">
                            @for ($h = 0; $h < 2; $h++)
                                @foreach ($klien as $client)
                                    @if ($client->logo)
                                        {{-- White logo bg blends into the white band; grayscale by default, colour on hover --}}
                                        <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy"
                                             class="h-10 w-auto max-w-[150px] shrink-0 object-contain opacity-70 grayscale transition duration-300 hover:scale-105 hover:opacity-100 hover:grayscale-0 md:h-12"
                                             aria-hidden="{{ $h ? 'true' : 'false' }}">
                                    @else
                                        <span class="shrink-0 text-base font-semibold text-slate-500 transition hover:text-navy" aria-hidden="{{ $h ? 'true' : 'false' }}">{{ $client->name }}</span>
                                    @endif
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endif
