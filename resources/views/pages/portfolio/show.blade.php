@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
    $coverUrl = $imgUrl($portfolio->cover_image);
@endphp

<x-layout :title="$portfolio->title" :description="$portfolio->short_description" :ogImage="$coverUrl">
    <x-page-header :eyebrow="$portfolio->category?->name" :title="$portfolio->title" />

    <section class="section bg-white">
        <div class="container">
            {{-- Back link --}}
            <a href="{{ route('portfolio.index') }}" class="group mb-8 inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition-colors hover:text-navy">
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $id ? 'Kembali ke Portofolio' : 'Back to Portfolio' }}
            </a>

            <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">

            {{-- Main content --}}
            <div class="lg:col-span-8">
                {{-- Cover — sits with the details, aligned to the sidebar --}}
                @if ($coverUrl)
                    <figure class="mb-8 overflow-hidden rounded-2xl shadow-card ring-1 ring-navy-100" data-aos="fade-up">
                        <img src="{{ $coverUrl }}" alt="{{ $portfolio->title }}" class="aspect-[16/10] w-full object-cover object-center">
                    </figure>
                @endif

                @if ($portfolio->short_description)
                    {{-- Lead paragraph, no gold side-stripe: a coloured left border reads as a
                         leftover callout rather than a decision. Size carries the emphasis.
                         Justify only from sm — it opens rivers in a narrow column. --}}
                    <p class="mb-8 text-lg leading-relaxed text-navy [hyphens:auto] sm:text-justify">{{ $portfolio->short_description }}</p>
                @endif

                @if ($portfolio->content)
                    <div class="prose prose-lg max-w-none [hyphens:auto]
                                prose-headings:font-display prose-headings:font-normal prose-headings:text-navy
                                prose-p:text-slate-700 prose-p:leading-[1.85]
                                prose-p:text-left sm:prose-p:text-justify
                                prose-li:text-slate-700 prose-a:text-sky-700 prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                                prose-strong:text-navy
                                prose-blockquote:border-0 prose-blockquote:bg-navy-50/70 prose-blockquote:px-6 prose-blockquote:py-4 prose-blockquote:rounded-xl prose-blockquote:not-italic prose-blockquote:text-navy
                                prose-img:rounded-2xl prose-img:shadow-card">
                        {!! \App\Helpers\HtmlSanitizer::clean($portfolio->content) !!}
                    </div>
                @endif
            </div>

            {{-- Sidebar: project facts + CTA --}}
            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    {{-- Facts --}}
                    <div class="rounded-2xl border border-navy-100 bg-neutral-50 p-6">
                        <p class="kicker mb-5">{{ $id ? 'Detail Proyek' : 'Project Details' }}</p>
                        <dl class="divide-y divide-navy-100">
                            @if ($portfolio->client_name)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Klien' : 'Client' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->client_name }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->category)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Kategori' : 'Category' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->category->name }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->location)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Lokasi' : 'Location' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->location }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->project_date)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Tanggal' : 'Date' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->project_date->translatedFormat('d M Y') }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->images->isNotEmpty())
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Dokumentasi' : 'Documentation' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->images->count() }} {{ $id ? 'foto' : 'photos' }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- CTA --}}
                    <div class="relative overflow-hidden rounded-2xl bg-navy-950 p-7 text-white">
                        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                        <div class="relative">
                            <p class="font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $id ? 'Tertarik?' : 'Interested?' }}</p>
                            <p class="mt-3 font-display text-xl leading-snug text-balance">{{ $id ? 'Wujudkan proyek serupa untuk organisasi Anda.' : 'Bring a similar project to life for your team.' }}</p>
                            <a href="{{ route('contact.index') }}" class="btn-blue mt-6 w-full justify-center">{{ $id ? 'Konsultasi Gratis' : 'Free Consultation' }}</a>
                        </div>
                    </div>
                </div>
            </aside>
            </div>
        </div>

        {{-- Client proof for THIS project. The relation, the CMS field and the eager load
             already existed; only the render was missing, so every request fetched these
             rows and discarded them. Ratings come from the same record the admin fills in. --}}
        @php
            $quotes = $portfolio->testimonials->where('is_active', true)->sortBy('sort_order');
            $avgRating = $quotes->whereNotNull('rating')->avg('rating');
        @endphp
        @if ($quotes->isNotEmpty())
            <div class="container mt-16">
                <div class="flex flex-col gap-5 border-b border-navy-100 pb-7 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="font-display text-2xl font-semibold text-navy md:text-3xl">{{ $id ? 'Kata klien tentang proyek ini' : 'What the client said' }}</h2>
                    </div>

                    <div class="flex items-center gap-5">
                        {{-- Aggregate only earns its place once there is more than one voice. --}}
                        @if ($avgRating && $quotes->count() > 1)
                            <div class="flex items-center gap-3">
                                <div class="flex gap-0.5" aria-hidden="true">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="h-4 w-4 {{ $i <= round($avgRating) ? 'text-gold' : 'text-navy-200' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8z"/></svg>
                                    @endfor
                                </div>
                                <p class="font-mono text-sm text-slate-500">
                                    <span class="font-bold tabular-nums text-navy">{{ number_format($avgRating, 1) }}</span>/5
                                    <span class="mx-1 text-navy-200">·</span>{{ $quotes->count() }} {{ $id ? 'ulasan' : 'reviews' }}
                                </p>
                            </div>
                        @endif

                        {{-- Same arrow treatment as the "Terus Membaca" rail. Swiper disables them
                             when every card already fits, and the disabled: classes fade them out. --}}
                        <div class="flex shrink-0 items-center gap-2">
                            <button type="button" data-carousel-prev
                                    aria-label="{{ $id ? 'Testimoni sebelumnya' : 'Previous testimonials' }}"
                                    class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 text-white transition hover:-translate-y-0.5 hover:from-navy-600 hover:to-navy-500 active:scale-95 disabled:pointer-events-none disabled:opacity-35">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" data-carousel-next
                                    aria-label="{{ $id ? 'Testimoni berikutnya' : 'Next testimonials' }}"
                                    class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 text-white transition hover:-translate-y-0.5 hover:from-navy-600 hover:to-navy-500 active:scale-95 disabled:pointer-events-none disabled:opacity-35">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- One row with dots, like the "Terus Membaca" rail on an article page.
                     Capped at 2 per view so a quote keeps a readable measure; Swiper's
                     watchOverflow hides the dots by itself when everything already fits,
                     so two testimonials still render as a plain pair. --}}
                <div class="swiper swiper-equal-height mt-8" data-carousel data-carousel-max="2" data-carousel-autoplay="false">
                    <div class="swiper-wrapper items-stretch">
                        @foreach ($quotes as $quote)
                            <div class="swiper-slide h-auto">
                                <figure class="card flex h-full flex-col p-7 md:p-8">
                                    @if ($quote->rating)
                                        <div class="flex gap-0.5" role="img"
                                             aria-label="{{ $quote->rating }} {{ $id ? 'dari 5 bintang' : 'out of 5 stars' }}">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= $quote->rating ? 'text-gold' : 'text-navy-200' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8z"/></svg>
                                            @endfor
                                        </div>
                                    @endif

                                    <blockquote class="mt-5 flex-1">
                                        <p class="text-pretty leading-relaxed text-slate-700">&ldquo;{{ $quote->content }}&rdquo;</p>
                                    </blockquote>

                                    <figcaption class="mt-6 flex items-center gap-3 border-t border-navy-100 pt-5">
                                        @if ($quote->author_photo)
                                            <img src="{{ $imgUrl($quote->author_photo) }}" alt="" loading="lazy" class="h-11 w-11 shrink-0 rounded-full object-cover">
                                        @else
                                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-sky-100 font-display text-sky-700" aria-hidden="true">{{ \Illuminate\Support\Str::substr($quote->author_name, 0, 1) }}</span>
                                        @endif
                                        <span class="min-w-0">
                                            <span class="block font-display text-[15px] font-semibold leading-tight text-navy">{{ $quote->author_name }}</span>
                                            <span class="mt-0.5 block text-xs leading-snug text-slate-500">{{ collect([$quote->author_position, $quote->author_company])->filter()->implode(' · ') }}</span>
                                        </span>
                                    </figcaption>
                                </figure>
                            </div>
                        @endforeach
                    </div>
                </div>
                {{-- Outside .swiper: anything inside it is clipped by Swiper's overflow:hidden. --}}
                <div class="mt-8 flex justify-center gap-2" data-carousel-pagination></div>
            </div>
        @endif

        {{-- Documentation photos (masonry) --}}
        @if ($portfolio->images->isNotEmpty())
            <div class="container mt-16">
                {{-- Was .eyebrow, which is display:none site-wide — this section shipped with no
                     heading at all, so the photos appeared without introduction.
                     "Dokumentasi Kegiatan", not "Galeri": these are records of what happened
                     on the ground, and the sidebar fact row already says "Dokumentasi". --}}
                <h2 class="font-display text-2xl font-semibold text-navy md:text-3xl" data-aos="fade-up">{{ $id ? 'Dokumentasi Kegiatan' : 'Activity Documentation' }}</h2>
                <div class="mt-8 columns-1 gap-4 sm:columns-2 lg:columns-3">
                    @foreach ($portfolio->images as $img)
                        <figure class="group mb-4 break-inside-avoid overflow-hidden rounded-2xl border border-navy-100 bg-navy-900 shadow-card" data-aos="fade-up">
                            <img src="{{ $imgUrl($img->image) }}" alt="{{ $img->caption ?: $portfolio->title }}" loading="lazy" decoding="async" class="w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                            @if ($img->caption)<figcaption class="bg-white px-4 py-3 text-xs text-slate-600">{{ $img->caption }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    @if ($related->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-neutral-50">
            <div class="container">
                <p class="kicker mb-8" data-aos="fade-up">{{ $id ? 'Portofolio terkait' : 'Related portfolio' }}</p>
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('portfolio.show', $r->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift">
                            <div class="relative aspect-[4/3] shrink-0 overflow-hidden bg-navy-900">
                                @if ($imgUrl($r->cover_image))<img src="{{ $imgUrl($r->cover_image) }}" alt="{{ $r->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">@else<div class="absolute inset-0 aurora opacity-60"></div>@endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                @if ($r->client_name)<p class="font-mono text-[10px] uppercase tracking-wider text-gold-deep">{{ $r->client_name }}</p>@endif
                                <h3 class="mt-1.5 line-clamp-2 font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $r->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
