@props(['partners' => null, 'clients' => null])

@php
    $partners = $partners ?? collect();
    $clients = $clients ?? collect();
    $isId = app()->getLocale() === 'id';

    // Repeat clients so a single marquee "half" fills the viewport, then render
    // two identical halves for a seamless, never-ending -50% loop.
    $repeat = function ($col, $min = 14) {
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

    $klien = $repeat($clients, 14);

    // Slightly scattered / tilted arrangement for the partner cards on desktop
    // (kept straight on smaller screens so the grid stays tidy).
    $mitraTransforms = [
        'lg:rotate-[-3deg] lg:translate-y-[8px]',
        'lg:rotate-[2deg] lg:-translate-y-[8px]',
        'lg:rotate-[-1.5deg] lg:translate-y-[12px]',
        'lg:rotate-[2.5deg] lg:translate-y-[4px]',
        'lg:rotate-[-2deg] lg:-translate-y-[10px]',
        'lg:rotate-[1.5deg] lg:translate-y-[10px]',
    ];
@endphp

@if ($partners->isNotEmpty() || $clients->isNotEmpty())
    {{-- ONE unified block: Mitra (white, tilted grid) + Klien (blue band) attached below --}}
    <section class="overflow-hidden bg-white pt-16 md:pt-24 {{ $clients->isEmpty() ? 'pb-16 md:pb-24' : '' }}">

        {{-- ===================== MITRA — scattered/tilted cards: logo + no. registrasi ===================== --}}
        @if ($partners->isNotEmpty())
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="text-display-xl font-semibold text-navy text-balance">{{ $isId ? 'Mitra Kami' : 'Our Partners' }}</h2>
                    <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-navy-450">
                        {{ $isId ? 'Mitra Yang Mendukung Dan Berkolaborasi Dengan Kami.' : 'Partners who support and collaborate with us.' }}
                    </p>
                </div>

                <div class="mx-auto mt-16 grid max-w-5xl gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-8 lg:gap-y-10">
                    @foreach ($partners as $partner)
                        <div class="group relative flex aspect-[16/10] flex-col rounded-3xl border border-navy-100 bg-white p-6 shadow-card transition duration-300 will-change-transform hover:z-10 hover:scale-[1.04] hover:shadow-lift {{ $mitraTransforms[$loop->index % count($mitraTransforms)] }}"
                             data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 90 }}">
                            {{-- Logo fills the upper area --}}
                            <div class="flex flex-1 items-center justify-center">
                                @if ($partner->logo)
                                    <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-16 w-auto max-w-[75%] object-contain">
                                @else
                                    <span class="text-center font-display text-xl font-semibold text-navy">{{ $partner->name }}</span>
                                @endif
                            </div>
                            {{-- Registration number anchored at the bottom (matches reference) --}}
                            @if ($partner->registration_number)
                                <p class="mt-3 text-center font-mono text-sm tracking-wide text-navy-500">{{ $partner->registration_number }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===================== KLIEN — blue band, logos only (no wrapper boxes), scrolls left forever ===================== --}}
        @if ($clients->isNotEmpty())
            <div class="relative w-full overflow-hidden py-10 md:py-12 {{ $partners->isNotEmpty() ? 'mt-10 md:mt-14' : '' }}"
                 style="background: radial-gradient(120% 160% at 12% -20%, #1b4d8a 0%, transparent 55%), linear-gradient(155deg, #10305a 0%, #0b2247 50%, #081831 100%);"
                 data-aos="fade-up">
                <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>

                <h2 class="relative text-center text-display-lg font-semibold text-white text-balance">{{ $isId ? 'Klien Kami' : 'Our Clients' }}</h2>
                <p class="relative mx-auto mt-3 max-w-xl text-center text-sm leading-relaxed text-white/80">
                    {{ $isId ? 'Klien Yang Telah Menggunakan Layanan Kami.' : 'Clients who have used our services.' }}
                </p>

                <div class="mask-fade-x relative mt-9 overflow-hidden">
                    <div class="flex w-max items-center gap-12 animate-marquee [will-change:transform] md:gap-16">
                        @for ($h = 0; $h < 2; $h++)
                            @foreach ($klien as $client)
                                @if ($client->logo)
                                    {{-- logo only: no wrapper box, no filter — shown in its original form --}}
                                    <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy"
                                         class="h-11 w-auto max-w-[160px] shrink-0 rounded-lg object-contain transition duration-300 hover:scale-105 md:h-14"
                                         aria-hidden="{{ $h ? 'true' : 'false' }}">
                                @else
                                    <span class="shrink-0 font-display text-lg font-semibold text-white/90" aria-hidden="{{ $h ? 'true' : 'false' }}">{{ $client->name }}</span>
                                @endif
                            @endforeach
                        @endfor
                    </div>
                </div>
            </div>
        @endif

    </section>
@endif
