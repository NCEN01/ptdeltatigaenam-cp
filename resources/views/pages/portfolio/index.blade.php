@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.portfolio')">
    <x-page-header
        :eyebrow="__('site.home.portfolio_kicker')"
        :title="__('site.home.portfolio_title')"
        :subtitle="$id ? 'Kegiatan, klien, dan kisah dampak yang kami banggakan.' : 'Projects, clients, and the impact stories we are proud of.'" />

    {{-- Portfolio grid (asymmetric) --}}
    <section class="section">
        <div class="container">
            @if ($portfolios->isNotEmpty())
                <div class="grid gap-6 md:grid-cols-6">
                    @foreach ($portfolios as $p)
                        <a href="{{ route('portfolio.show', $p->slug) }}"
                           class="group relative overflow-hidden rounded-3xl bg-navy-100 {{ $loop->index % 5 === 0 ? 'md:col-span-4' : 'md:col-span-2' }}"
                           data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                            <div class="relative aspect-[16/11] overflow-hidden">
                                @if ($p->cover_image)
                                    <img src="{{ Storage::url($p->cover_image) }}" alt="{{ $p->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-navy-300 to-navy-100"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-950/10 to-transparent"></div>
                            </div>
                            <div class="absolute inset-x-0 bottom-0 p-7 text-white">
                                @if ($p->category)<p class="font-mono text-[10px] uppercase tracking-label text-gold">{{ $p->category->name }}</p>@endif
                                <h3 class="mt-2 font-display text-2xl font-semibold">{{ $p->title }}</h3>
                                @if ($p->client_name)<p class="mt-1 text-sm text-navy-100">{{ $p->client_name }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-navy-400">{{ $id ? 'Portofolio segera hadir.' : 'Portfolio coming soon.' }}</p>
            @endif
        </div>
    </section>

    {{-- Clients --}}
    @if ($clients->isNotEmpty())
        <section class="border-y border-navy-100 bg-mist py-16">
            <div class="container">
                <p class="eyebrow-muted mb-10 text-center">{{ $id ? 'Klien yang mempercayai kami' : 'Clients who trust us' }}</p>
                <div class="grid grid-cols-2 items-center gap-x-8 gap-y-10 sm:grid-cols-3 lg:grid-cols-5">
                    @foreach ($clients as $client)
                        <div class="flex h-12 items-center justify-center opacity-60 transition-opacity hover:opacity-100">
                            @if ($client->logo)
                                <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" class="max-h-full w-auto object-contain">
                            @else
                                <span class="font-display text-lg font-semibold text-navy-300">{{ $client->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Testimonials --}}
    @if ($testimonials->isNotEmpty())
        <section class="section">
            <div class="container">
                <p class="eyebrow mb-10"><span class="rule-gold mr-3"></span>{{ __('site.home.testimonials_kicker') }}</p>
                <div class="columns-1 gap-6 md:columns-2 lg:columns-3 [&>*]:mb-6">
                    @foreach ($testimonials as $t)
                        <figure class="card break-inside-avoid p-7">
                            <div class="flex gap-1 text-gold">
                                @for ($i = 0; $i < ($t->rating ?? 5); $i++)<svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8z"/></svg>@endfor
                            </div>
                            <blockquote class="mt-4 text-pretty text-navy-700">“{{ $t->content }}”</blockquote>
                            <figcaption class="mt-5 border-t border-navy-100 pt-4">
                                <p class="font-medium text-navy">{{ $t->author_name }}</p>
                                <p class="text-xs text-navy-400">{{ $t->author_position }}{{ $t->author_company ? ', '.$t->author_company : '' }}</p>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-cta-band />
</x-layout>
