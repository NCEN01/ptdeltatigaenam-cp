@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
])

<section class="relative overflow-hidden bg-navy-950 pt-36 pb-16 text-white md:pt-44 md:pb-24">
    <div class="pointer-events-none absolute inset-0 aurora opacity-70"></div>
    <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
    <div class="container relative">
        @if ($eyebrow)
            <p class="eyebrow mb-5" data-aos="fade-up">{{ $eyebrow }}</p>
        @endif
        <h1 class="max-w-4xl text-display-xl font-semibold text-balance" data-aos="fade-up" data-aos-delay="60">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-6 max-w-2xl text-lg text-navy-100 text-pretty" data-aos="fade-up" data-aos-delay="120">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
