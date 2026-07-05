@props(['partners' => null, 'clients' => null])

@php
    $partners = $partners ?? collect();
    $clients = $clients ?? collect();
    $isId = app()->getLocale() === 'id';

    // Repeat a collection so a single marquee "half" is wide enough to fill the
    // viewport — then we render two identical halves for a seamless -50% loop.
    $repeat = function ($col, $min = 8) {
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

    $mitra = $repeat($partners, 8);
    $klien = $repeat($clients, 10);
@endphp

@if ($partners->isNotEmpty() || $clients->isNotEmpty())
    <section class="section overflow-hidden bg-white">

        {{-- ===================== MITRA — white, two rows, opposite directions ===================== --}}
        @if ($partners->isNotEmpty())
            <div class="container text-center" data-aos="fade-up">
                <p class="eyebrow"><span class="rule-gold mr-3"></span>{{ $isId ? 'Mitra Kami' : 'Our Partners' }}</p>
                <h2 class="mx-auto mt-4 max-w-2xl text-display-lg font-semibold text-navy text-balance">
                    {{ $isId ? 'Mitra yang mendukung dan berkolaborasi dengan kami' : 'Partners who support and collaborate with us' }}
                </h2>
                <p class="mx-auto mt-4 max-w-md text-sm leading-relaxed text-navy-450">
                    {{ $isId ? 'Siap berkolaborasi menciptakan tenaga kerja yang berkualitas.' : 'Ready to collaborate in creating a quality workforce.' }}
                </p>
            </div>

            <div class="mask-fade-x relative mt-14 space-y-5 overflow-hidden" data-aos="fade-up">
                {{-- Row 1 → moves RIGHT --}}
                <div class="flex w-max items-center gap-5 animate-marquee-reverse [will-change:transform]">
                    @for ($h = 0; $h < 2; $h++)
                        @foreach ($mitra as $partner)
                            <div class="group flex h-28 w-56 shrink-0 flex-col items-center justify-center gap-2.5 rounded-2xl border border-navy-100 bg-white px-6 shadow-card transition duration-300 hover:-translate-y-1 hover:border-navy-200 hover:shadow-lift" aria-hidden="{{ $h ? 'true' : 'false' }}">
                                @if ($partner->logo)
                                    <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-11 w-auto object-contain">
                                    <span class="max-w-full truncate font-mono text-[11px] uppercase tracking-[0.14em] text-navy-400">{{ $partner->name }}</span>
                                @else
                                    <span class="text-center font-display text-lg font-semibold text-navy">{{ $partner->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    @endfor
                </div>

                {{-- Row 2 → moves LEFT --}}
                <div class="flex w-max items-center gap-5 animate-marquee-slow [will-change:transform]">
                    @for ($h = 0; $h < 2; $h++)
                        @foreach ($mitra->reverse() as $partner)
                            <div class="group flex h-28 w-56 shrink-0 flex-col items-center justify-center gap-2.5 rounded-2xl border border-navy-100 bg-white px-6 shadow-card transition duration-300 hover:-translate-y-1 hover:border-navy-200 hover:shadow-lift" aria-hidden="{{ $h ? 'true' : 'false' }}">
                                @if ($partner->logo)
                                    <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-11 w-auto object-contain">
                                    <span class="max-w-full truncate font-mono text-[11px] uppercase tracking-[0.14em] text-navy-400">{{ $partner->name }}</span>
                                @else
                                    <span class="text-center font-display text-lg font-semibold text-navy">{{ $partner->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>
        @endif

        {{-- ===================== KLIEN — signature blue, single row, scrolls left forever ===================== --}}
        @if ($clients->isNotEmpty())
            <div class="container {{ $partners->isNotEmpty() ? 'mt-16 md:mt-20' : '' }}" data-aos="fade-up">
                <div class="relative overflow-hidden rounded-[2.5rem] px-6 py-12 shadow-lift md:py-14"
                     style="background: radial-gradient(120% 130% at 12% -10%, #1b4d8a 0%, transparent 52%), linear-gradient(155deg, #10305a 0%, #0b2247 48%, #081831 100%);">
                    <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>
                    <div class="pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-sky-500/20 blur-3xl"></div>

                    <div class="relative text-center">
                        <p class="eyebrow text-gold-soft"><span class="rule-gold mr-3"></span>{{ $isId ? 'Klien Kami' : 'Our Clients' }}</p>
                        <h2 class="mx-auto mt-3 max-w-2xl text-display-lg font-semibold text-white text-balance">
                            {{ $isId ? 'Dipercaya oleh berbagai organisasi' : 'Trusted by leading organizations' }}
                        </h2>
                    </div>

                    {{-- Single row → moves LEFT, never stops --}}
                    <div class="mask-fade-x relative mt-10 overflow-hidden">
                        <div class="flex w-max items-center gap-4 animate-marquee [will-change:transform]">
                            @for ($h = 0; $h < 2; $h++)
                                @foreach ($klien as $client)
                                    <div class="flex h-20 w-40 shrink-0 items-center justify-center rounded-2xl bg-white px-6 shadow-lift ring-1 ring-white/10 transition duration-300 hover:-translate-y-1" aria-hidden="{{ $h ? 'true' : 'false' }}">
                                        @if ($client->logo)
                                            <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy" class="max-h-10 w-auto object-contain">
                                        @else
                                            <span class="text-center font-display text-sm font-semibold text-navy">{{ $client->name }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </section>
@endif
