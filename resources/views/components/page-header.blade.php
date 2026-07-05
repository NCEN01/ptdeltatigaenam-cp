@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
    'image' => null, // Unsplash photo id, e.g. "photo-1524178232363-1fb2b075b655"
])

@php
    // Split the title so the last word becomes an italic, gradient accent —
    // the consistent bold-upright + italic-gradient heading pattern across every page.
    $hwWords = preg_split('/\s+/', trim((string) $title)) ?: [];
    $hwAccent = count($hwWords) ? array_pop($hwWords) : '';
    $hwLead = implode(' ', $hwWords);
@endphp

<section class="relative flex min-h-[30rem] flex-col justify-center overflow-hidden bg-navy-anim pt-32 pb-16 text-center text-white md:min-h-[40rem] md:pt-40 md:pb-24">
    @if ($image)
        {{-- Layer 1: background image --}}
        <img src="https://images.unsplash.com/{{ $image }}?auto=format&fit=crop&w=1920&q=80" alt="" loading="eager"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover">
        {{-- Layer 2: gradient fade bottom → top (keeps text readable, blends into page) --}}
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-950/45 to-navy-950/15"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
    @else
        {{-- Aurora fallback (no image) --}}
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
        <div class="pointer-events-none absolute -left-20 top-10 h-64 w-64 rounded-full bg-sky-500/12 blur-3xl" data-parallax="0.15"></div>
        <div class="pointer-events-none absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-cyan/10 blur-3xl" data-parallax="0.1"></div>
    @endif
    {{-- Top glow line --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-sky-500/40 to-transparent"></div>

    <div class="container relative">
        @if ($eyebrow)
            <p class="eyebrow mb-5 inline-flex items-center justify-center" data-aos="fade-up">
                <span class="rule-gold mr-3"></span>{{ $eyebrow }}
            </p>
        @endif
        <h1 class="mx-auto max-w-4xl text-display-xl leading-[1.06] text-balance" data-aos="fade-up" data-aos-delay="60">{{ $hwLead }}@if ($hwLead) @endif<span class="italic-accent text-gradient-hero">{{ $hwAccent }}</span></h1>
        @if ($subtitle)
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-navy-100 text-pretty" data-aos="fade-up" data-aos-delay="140">{{ $subtitle }}</p>
        @endif
        @if (trim($slot) !== '')
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3" data-aos="fade-up" data-aos-delay="200">{{ $slot }}</div>
        @endif
    </div>

    {{-- Clean fade into the page below (replaces the generic wave) --}}
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-b from-transparent to-white/[0.04]"></div>
</section>
