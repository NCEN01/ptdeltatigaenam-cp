@php use Illuminate\Support\Facades\Storage; @endphp

<x-layout :title="__('site.nav.home')">

    {{-- ===================== HERO ===================== --}}
    <section class="relative min-h-[100svh] overflow-hidden bg-navy-950 text-white">
        {{-- animated aurora field --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute inset-0 aurora animate-aurora-drift"></div>
            <div class="absolute inset-0 grain opacity-60"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-navy-950/30 via-transparent to-navy-950"></div>
        </div>

        {{-- hairline grid accent --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/15 to-transparent"></div>

        <div class="container relative flex min-h-[100svh] flex-col justify-center pt-32 pb-16">
            <div class="grid items-end gap-12 lg:grid-cols-12">
                {{-- Headline block --}}
                <div class="lg:col-span-7">
                    <p class="eyebrow mb-7" data-aos="fade-up">{{ __('site.home.hero_kicker') }}</p>
                    <h1 class="text-display-2xl font-semibold text-balance" data-aos="fade-up" data-aos-delay="60">
                        {{ __('site.home.hero_title') }}
                    </h1>
                    <p class="mt-8 max-w-xl text-lg leading-relaxed text-navy-100 text-pretty" data-aos="fade-up" data-aos-delay="120">
                        {{ __('site.home.hero_sub') }}
                    </p>
                    <div class="mt-10 flex flex-wrap items-center gap-4" data-aos="fade-up" data-aos-delay="180">
                        <a href="{{ route('services.index') }}" class="btn-gold">{{ __('site.cta.explore') }}</a>
                        <a href="{{ route('contact.index') }}" class="btn-ghost-light">{{ __('site.cta.consult') }}</a>
                    </div>
                </div>

                {{-- Rotating featured categories (Swiper) --}}
                @if ($featuredCategories->isNotEmpty())
                    <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="120">
                        <div class="rounded-3xl border border-white/10 bg-white/[0.04] p-2 backdrop-blur-xl">
                            <div class="swiper overflow-hidden rounded-[20px]" data-hero-swiper>
                                <div class="swiper-wrapper">
                                    @foreach ($featuredCategories as $cat)
                                        <a href="{{ route('services.index') }}#{{ $cat->slug }}" class="swiper-slide group block">
                                            <div class="relative aspect-[4/5] overflow-hidden rounded-[20px] bg-navy-800">
                                                @if ($cat->image)
                                                    <img src="{{ Storage::url($cat->image) }}" alt="{{ $cat->name }}"
                                                         class="absolute inset-0 h-full w-full object-cover opacity-70 transition-transform duration-700 group-hover:scale-105" loading="eager">
                                                @else
                                                    <div class="absolute inset-0 bg-gradient-to-br from-navy-700 to-navy-950"></div>
                                                @endif
                                                <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/30 to-transparent"></div>
                                                <div class="absolute inset-x-0 bottom-0 p-6">
                                                    <p class="font-mono text-[10px] uppercase tracking-label text-gold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($featuredCategories->count(), 2, '0', STR_PAD_LEFT) }}</p>
                                                    <h3 class="mt-2 font-display text-2xl font-semibold leading-tight">{{ $cat->name }}</h3>
                                                    <span class="mt-3 inline-flex items-center gap-1.5 text-sm text-navy-100">
                                                        {{ __('site.cta.read_more') }}
                                                        <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3">
                                <div class="flex gap-2" data-hero-pagination></div>
                                <div class="flex gap-2">
                                    <button data-hero-prev class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-white transition-colors hover:bg-white hover:text-navy" aria-label="Previous">
                                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                    <button data-hero-next class="grid h-9 w-9 place-items-center rounded-full border border-white/15 text-white transition-colors hover:bg-white hover:text-navy" aria-label="Next">
                                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="pointer-events-none absolute bottom-6 left-1/2 -translate-x-1/2 text-navy-300">
            <svg class="h-6 w-6 animate-bounce" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M6 13l6 6 6-6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    </section>

    {{-- ===================== SERVICES ===================== --}}
    <section class="section">
        <div class="container">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ __('site.home.services_kicker') }}</p>
                    <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ __('site.home.services_title') }}</h2>
                </div>
                <a href="{{ route('services.index') }}" class="link-underline shrink-0 font-medium" data-aos="fade-up">
                    {{ __('site.cta.view_all') }}
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            <div class="mt-14 grid gap-px overflow-hidden rounded-3xl border border-navy-100 bg-navy-100 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($featuredCategories as $cat)
                    <a href="{{ route('services.index') }}#{{ $cat->slug }}"
                       class="group relative flex flex-col bg-white p-8 transition-colors duration-300 hover:bg-mist"
                       data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                        <span class="font-mono text-xs text-navy-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-6 font-display text-2xl font-semibold text-navy">{{ $cat->name }}</h3>
                        @if ($cat->short_description)
                            <p class="mt-3 text-pretty text-navy-500">{{ $cat->short_description }}</p>
                        @endif
                        <span class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-navy">
                            <span class="grid h-9 w-9 place-items-center rounded-full border border-navy-200 transition-all duration-300 group-hover:border-gold group-hover:bg-gold group-hover:text-ink">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </span>
                    </a>
                @empty
                    <div class="bg-white p-8 text-navy-400">Belum ada kategori layanan.</div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===================== PORTFOLIO ===================== --}}
    @if ($portfolios->isNotEmpty())
        <section class="section bg-mist">
            <div class="container">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ __('site.home.portfolio_kicker') }}</p>
                <h2 class="max-w-2xl text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ __('site.home.portfolio_title') }}</h2>

                <div class="mt-14 grid gap-6 lg:grid-cols-3">
                    @foreach ($portfolios as $p)
                        <a href="{{ route('portfolio.show', $p->slug) }}" class="card card-hover group overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                            <div class="relative aspect-[4/3] overflow-hidden bg-navy-100">
                                @if ($p->cover_image)
                                    <img src="{{ Storage::url($p->cover_image) }}" alt="{{ $p->title }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-navy-200 to-navy-100"></div>
                                @endif
                            </div>
                            <div class="p-6">
                                @if ($p->category)<p class="eyebrow-muted">{{ $p->category->name }}</p>@endif
                                <h3 class="mt-3 font-display text-xl font-semibold text-navy">{{ $p->title }}</h3>
                                @if ($p->client_name)<p class="mt-1 text-sm text-navy-400">{{ $p->client_name }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== TESTIMONIALS ===================== --}}
    @if ($testimonials->isNotEmpty())
        <section class="section">
            <div class="container">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ __('site.home.testimonials_kicker') }}</p>
                <h2 class="max-w-2xl text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ __('site.home.testimonials_title') }}</h2>

                <div class="swiper mt-14" data-carousel>
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $t)
                            <div class="swiper-slide h-auto">
                                <figure class="card flex h-full flex-col p-8">
                                    <div class="flex gap-1 text-gold">
                                        @for ($i = 0; $i < ($t->rating ?? 5); $i++)
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8z"/></svg>
                                        @endfor
                                    </div>
                                    <blockquote class="mt-5 flex-1 text-pretty text-navy-700">“{{ $t->content }}”</blockquote>
                                    <figcaption class="mt-6 flex items-center gap-3 border-t border-navy-100 pt-5">
                                        <div class="h-10 w-10 overflow-hidden rounded-full bg-navy-100">
                                            @if ($t->author_photo)<img src="{{ Storage::url($t->author_photo) }}" alt="{{ $t->author_name }}" class="h-full w-full object-cover">@endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-navy">{{ $t->author_name }}</p>
                                            <p class="text-xs text-navy-400">{{ $t->author_position }}{{ $t->author_company ? ', '.$t->author_company : '' }}</p>
                                        </div>
                                    </figcaption>
                                </figure>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8 flex justify-center gap-2" data-carousel-pagination></div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== PARTNERS MARQUEE ===================== --}}
    @if ($partners->isNotEmpty())
        <section class="border-y border-navy-100 bg-white py-12">
            <div class="container">
                <p class="eyebrow-muted mb-8 text-center">{{ __('site.home.partners_kicker') }}</p>
            </div>
            <div class="mask-fade-x relative overflow-hidden">
                <div class="flex w-max animate-marquee items-center gap-16 pr-16">
                    @foreach ($partners->concat($partners) as $partner)
                        <div class="flex h-10 shrink-0 items-center opacity-60 transition-opacity hover:opacity-100">
                            @if ($partner->logo)
                                <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" class="h-full w-auto object-contain">
                            @else
                                <span class="font-display text-xl font-semibold text-navy-300">{{ $partner->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== BLOG ===================== --}}
    @if ($posts->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ __('site.home.blog_kicker') }}</p>
                        <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ __('site.home.blog_title') }}</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="link-underline shrink-0 font-medium" data-aos="fade-up">{{ __('site.cta.view_all') }}</a>
                </div>

                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col" data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                            <div class="relative aspect-[16/10] overflow-hidden rounded-2xl bg-navy-100">
                                @if ($post->featured_image)
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col pt-5">
                                <p class="eyebrow-muted">{{ optional($post->category)->name }} · {{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                                <h3 class="mt-3 font-display text-xl font-semibold leading-snug text-navy transition-colors group-hover:text-navy-500">{{ $post->title }}</h3>
                                @if ($post->excerpt)<p class="mt-2 line-clamp-2 text-sm text-navy-500">{{ $post->excerpt }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== CTA ===================== --}}
    <section class="section">
        <div class="container">
            <div class="relative overflow-hidden rounded-[2rem] bg-navy-950 px-8 py-16 text-center text-white md:px-16 md:py-24">
                <div class="pointer-events-none absolute inset-0 aurora opacity-70"></div>
                <div class="relative">
                    <h2 class="mx-auto max-w-3xl text-display-lg font-semibold text-balance" data-aos="fade-up">
                        {{ app()->getLocale() === 'id' ? 'Siap meningkatkan kompetensi tim Anda?' : 'Ready to elevate your team’s competencies?' }}
                    </h2>
                    <div class="mt-9 flex flex-wrap justify-center gap-4" data-aos="fade-up" data-aos-delay="80">
                        <a href="{{ route('partnership.index') }}" class="btn-gold">{{ __('site.cta.partner') }}</a>
                        <a href="{{ route('contact.index') }}" class="btn-ghost-light">{{ __('site.cta.consult') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layout>
