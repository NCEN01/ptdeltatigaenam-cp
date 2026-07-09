@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
    'image' => null, // Unsplash photo id, fallback jika tidak ada banner
    'placement' => null, // e.g. 'about', 'services', 'portfolio', dll.
])

@php
    $bgImage = null;
    $bannerTitle = $title;
    $bannerSubtitle = $subtitle;

    if ($placement) {
        $banner = \App\Models\Banner::activeNow()
            ->where('placement', $placement)
            ->orderBy('sort_order')
            ->first();

        if ($banner) {
            if ($banner->image) {
                $bgImage = str_starts_with($banner->image, 'http')
                    ? $banner->image
                    : \Illuminate\Support\Facades\Storage::url($banner->image);
            }

            // Override title & subtitle from banner if set
            if (! empty($banner->title)) {
                $bannerTitle = $banner->title;
            }
            if (! empty($banner->subtitle)) {
                $bannerSubtitle = $banner->subtitle;
            }
        }
    }

    if (! $bgImage && $image) {
        $bgImage = 'https://images.unsplash.com/' . $image . '?auto=format&fit=crop&w=1920&q=80';
    }

    $hwWords = preg_split('/\s+/', trim((string) $bannerTitle)) ?: [];
    $hwAccent = count($hwWords) ? array_pop($hwWords) : '';
    $hwLead = implode(' ', $hwWords);
@endphp

<section class="relative flex min-h-[22rem] flex-col justify-center overflow-hidden bg-navy-anim pt-24 pb-12 text-center text-white md:min-h-[30rem] md:pt-28 md:pb-16">
    @if ($bgImage)
        <img src="{{ $bgImage }}" alt="" loading="eager"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-950/45 to-navy-950/15"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
    @else
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
        <div class="pointer-events-none absolute -left-20 top-10 h-64 w-64 rounded-full bg-sky-500/12 blur-3xl" data-parallax="0.15"></div>
        <div class="pointer-events-none absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-cyan/10 blur-3xl" data-parallax="0.1"></div>
    @endif
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-sky-500/40 to-transparent"></div>

    <div class="container relative">
        @if ($eyebrow)
            <p class="eyebrow mb-5 inline-flex items-center justify-center text-gold-soft" data-aos="fade-up">
                <span class="rule-gold mr-3 from-gold"></span>{{ $eyebrow }}
            </p>
        @endif
        <h1 class="mx-auto max-w-4xl text-display-xl leading-[1.06] text-balance" data-aos="fade-up" data-aos-delay="60">{{ $hwLead }}@if ($hwLead) @endif<span class="italic-accent text-gradient-hero">{{ $hwAccent }}</span></h1>
        @if ($bannerSubtitle)
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-navy-100 text-pretty" data-aos="fade-up" data-aos-delay="140">{{ $bannerSubtitle }}</p>
        @endif
        @if (trim($slot) !== '')
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3" data-aos="fade-up" data-aos-delay="200">{{ $slot }}</div>
        @endif
    </div>

    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-b from-transparent to-white/[0.04]"></div>
</section>