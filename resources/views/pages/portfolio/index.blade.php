@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.portfolio')">
    <x-page-header
        :eyebrow="__('site.home.portfolio_kicker')"
        :title="__('site.home.portfolio_title')"
        :subtitle="$id ? 'Kegiatan, klien, dan kisah dampak yang kami banggakan.' : 'Projects, clients, and the impact stories we are proud of.'"
        placement="portfolio"
        image="photo-1531403009284-440f080d1e12" />

    {{-- Portfolio grid (asymmetric) --}}
    <section class="section">
        <div class="container">
            <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Karya Pilihan' : 'Selected Work' }}</p>
                    <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $id ? 'Dampak nyata bersama klien kami' : 'Real impact alongside our clients' }}</h2>
                </div>
                @if ($portfolios->isNotEmpty())
                    <p class="font-mono text-sm text-navy-400" data-aos="fade-up">{{ str_pad($portfolios->count(), 2, '0', STR_PAD_LEFT) }} {{ $id ? 'proyek' : 'projects' }}</p>
                @endif
            </div>

            @if ($portfolios->isNotEmpty())
                <div class="grid gap-x-6 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($portfolios as $p)
                        @php $cover = $p->cover_image ? (str_starts_with($p->cover_image, 'http') ? $p->cover_image : Storage::url($p->cover_image)) : null; @endphp
                        <a href="{{ route('portfolio.show', $p->slug) }}" class="group block" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                            {{-- Visual with hover reveal --}}
                            <div data-spotlight class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-navy-100 ring-1 ring-navy-100 transition-shadow duration-500 group-hover:shadow-lift">
                                @if ($cover)
                                    <img src="{{ $cover }}" alt="{{ $p->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-[900ms] ease-out-soft group-hover:scale-[1.07]">
                                @else
                                    <div class="absolute inset-0 aurora opacity-70"></div>
                                    <div class="absolute inset-0 grid place-items-center font-display text-6xl font-semibold text-white/85">{{ \Illuminate\Support\Str::substr($p->title, 0, 1) }}</div>
                                @endif

                                @if ($p->category)
                                    <span class="absolute left-4 top-4 z-10 rounded-full bg-white/90 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-navy backdrop-blur">{{ $p->category->name }}</span>
                                @endif

                                {{-- reveal --}}
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/85 via-navy-950/10 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                                <div class="pointer-events-none absolute inset-x-0 bottom-0 flex translate-y-3 items-center p-5 opacity-0 transition-all duration-500 ease-out-soft group-hover:translate-y-0 group-hover:opacity-100">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-xs font-semibold text-navy shadow-lift">
                                        {{ $id ? 'Lihat Detail' : 'View Case' }}
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </div>
                            </div>

                            {{-- Title + small caption below --}}
                            <div class="mt-4 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="font-display text-lg font-semibold leading-snug text-navy transition-colors duration-300 group-hover:text-sky-600">{{ $p->title }}</h3>
                                    <p class="mt-1 flex flex-wrap items-center gap-x-2 text-sm text-navy-400">
                                        @if ($p->client_name)<span>{{ $p->client_name }}</span>@endif
                                        @if ($p->client_name && $p->project_date)<span class="text-navy-200">·</span>@endif
                                        @if ($p->project_date)<span class="font-mono text-xs">{{ $p->project_date->translatedFormat('Y') }}</span>@endif
                                    </p>
                                </div>
                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full border border-navy-200 text-navy transition-all duration-300 group-hover:-rotate-45 group-hover:border-sky-500 group-hover:bg-sky-500 group-hover:text-white">
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-navy-200 bg-mist p-16 text-center" data-aos="fade-up">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v12H4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M4 11h16M9 7V5h6v2" stroke="currentColor" stroke-width="1.4"/></svg>
                    </span>
                    <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Portofolio segera hadir' : 'Portfolio coming soon' }}</p>
                    <p class="mt-2 text-sm text-navy-500">{{ $id ? 'Kami sedang menyiapkan kisah proyek terbaik untuk ditampilkan di sini.' : 'We are preparing our best project stories to showcase here.' }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Testimonials --}}
    <x-testimonial-columns :testimonials="$testimonials" />

    {{-- Partners & Clients --}}
    <x-partners-clients :partners="$partners" :clients="$clients" />

</x-layout>
