@php use Illuminate\Support\Facades\Storage; $isId = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.home')">

    {{-- ===================== HERO CAROUSEL ===================== --}}
    @php
        $heroStats = $isId
            ? [['500+', 'Profesional Terlatih'], ['98%', 'Kepuasan Klien'], ['10+', 'Tahun Pengalaman']]
            : [['500+', 'Professionals Trained'], ['98%', 'Client Satisfaction'], ['10+', 'Years Experience']];
        
        $slides = [];
        if (isset($heroBanners) && $heroBanners->isNotEmpty()) {
            foreach ($heroBanners as $banner) {
                $slides[] = [
                    'img_url' => str_starts_with($banner->image, 'http') ? $banner->image : Storage::url($banner->image),
                    'cat' => optional($banner->category)->name ?: ($isId ? 'Layanan' : 'Services'),
                    'title' => $banner->title,
                    'desc' => $banner->subtitle,
                    'link' => $banner->link_url ?: route('services.index'),
                    'btn_text' => $banner->button_text ?: ($isId ? 'Jelajahi' : 'Explore'),
                ];
            }
        } else {
            $hardcodedSlides = $isId ? [
                ['img' => 'photo-1524178232363-1fb2b075b655', 'cat' => 'Pelatihan', 'title' => 'Pelatihan SDM yang Berdampak', 'desc' => 'Program pelatihan karyawan yang relevan dan inovatif untuk meningkatkan kompetensi serta produktivitas tim Anda.'],
                ['img' => 'photo-1542744173-8e7e53415bb0', 'cat' => 'Sertifikasi', 'title' => 'Sertifikasi Profesi Terpercaya', 'desc' => 'Sertifikasi kompetensi yang diakui secara profesional untuk meningkatkan kredibilitas dan daya saing SDM Anda.'],
                ['img' => 'photo-1521737604893-d14cc237f11d', 'cat' => 'Headhunter', 'title' => 'Headhunter & Penempatan Talenta', 'desc' => 'Penyeleksian dan penempatan talenta terbaik yang sesuai dengan kebutuhan strategis perusahaan Anda.'],
            ] : [
                ['img' => 'photo-1524178232363-1fb2b075b655', 'cat' => 'Training', 'title' => 'Impactful HR Training', 'desc' => "Relevant, innovative employee training programs to boost your team's competency and productivity."],
                ['img' => 'photo-1542744173-8e7e53415bb0', 'cat' => 'Certification', 'title' => 'Trusted Professional Certification', 'desc' => "Professionally recognized competency certification to strengthen your people's credibility and edge."],
                ['img' => 'photo-1521737604893-d14cc237f11d', 'cat' => 'Headhunting', 'title' => 'Headhunting & Talent Placement', 'desc' => 'Selection and placement of top talent aligned with your strategic business needs.'],
            ];
            foreach ($hardcodedSlides as $s) {
                $slides[] = [
                    'img_url' => "https://images.unsplash.com/" . $s['img'] . "?auto=format&fit=crop&w=1920&q=80",
                    'cat' => $s['cat'],
                    'title' => $s['title'],
                    'desc' => $s['desc'],
                    'link' => route('services.index'),
                    'btn_text' => __('site.cta.explore'),
                ];
            }
        }
    @endphp
    <section class="relative">
        <div class="swiper hero-carousel" data-hero-carousel>
            <div class="swiper-wrapper">
                @foreach ($slides as $slide)
                    <div class="swiper-slide relative min-h-[100svh] overflow-hidden bg-navy-950">
                        <img src="{{ $slide['img_url'] }}"
                             alt="{{ $slide['title'] }}" {{ $loop->first ? 'loading=eager fetchpriority=high' : 'loading=lazy' }}
                             class="hero-slide-img absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-navy-950/92 via-navy-950/70 to-navy-950/20"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-navy-950/30"></div>

                        <div class="container relative flex min-h-[100svh] items-center">
                            <div class="hero-content max-w-2xl py-28 text-white">
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 font-mono text-[11px] font-medium uppercase tracking-[0.18em] text-white backdrop-blur">
                                    <svg class="h-3.5 w-3.5 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/></svg>
                                    {{ $slide['cat'] }}
                                </span>
                                @php $hw = preg_split('/\s+/', trim($slide['title'])); $ha = count($hw) ? array_pop($hw) : ''; $hl = implode(' ', $hw); @endphp
                                <h1 class="mt-6 font-display font-normal leading-[1.05] text-balance [font-size:clamp(2.2rem,5.2vw,4rem)]">{{ $hl }}@if ($hl) @endif<span class="italic-accent text-gradient-hero">{{ $ha }}</span></h1>
                                <p class="mt-6 max-w-xl text-base leading-relaxed text-navy-100 text-pretty md:text-lg">{{ $slide['desc'] }}</p>
                                <div class="mt-9 flex flex-wrap items-center gap-4">
                                    <a href="{{ $slide['link'] }}" class="btn-blue">{{ $slide['btn_text'] }}</a>
                                    <a href="{{ route('contact.index') }}" class="btn-ghost-light">{{ __('site.cta.consult') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- arrows --}}
            <button data-hero-prev class="group absolute left-4 top-1/2 z-10 hidden h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-navy md:grid" aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                <svg class="h-5 w-5" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button data-hero-next class="group absolute right-4 top-1/2 z-10 hidden h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-navy md:grid" aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                <svg class="h-5 w-5" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            {{-- pagination — lifted above the stats ledger --}}
            <div class="absolute inset-x-0 bottom-24 z-20 flex justify-center gap-2" data-hero-carousel-pagination></div>

            {{-- stats ledger — merged onto the image (no top rule) --}}
            <div class="absolute inset-x-0 bottom-0 z-20">
                <div class="bg-gradient-to-t from-navy-950/90 via-navy-950/55 to-transparent backdrop-blur-[2px]">
                    <div class="container">
                        <div class="mx-auto grid max-w-2xl grid-cols-3 divide-x divide-white/10 py-4 md:py-5">
                            @foreach ($heroStats as $stat)
                                @php
                                    $numVal = (int) filter_var($stat[0], FILTER_SANITIZE_NUMBER_INT);
                                    $suffix = str_replace((string) $numVal, '', $stat[0]);
                                @endphp
                                <div class="group px-3 text-center md:px-6">
                                    <p class="font-display text-xl text-white transition-colors duration-300 group-hover:text-gold-soft md:text-[1.7rem]" data-counter="{{ $numVal }}" data-counter-suffix="{{ $suffix }}">0{{ $suffix }}</p>
                                    <p class="mt-1 font-mono text-[8px] uppercase tracking-[0.18em] text-navy-100 md:text-[9px]">{{ $stat[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== SERVICE CATEGORIES · 3D COVERFLOW ===================== --}}
    <section class="relative overflow-hidden py-20 text-white md:py-28"
             style="background:
                radial-gradient(65% 55% at 50% 105%, rgba(90,120,220,0.16), transparent 70%),
                linear-gradient(160deg, #10305a 0%, #0c2245 35%, #081831 70%, #050f22 100%);">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-30"></div>

        <div class="container relative">
            {{-- Header --}}
            <div class="mx-auto max-w-2xl text-center">
                <p class="font-mono text-[11px] uppercase tracking-[0.22em] text-gold-soft" data-aos="fade-up">
                    {{ $isId ? 'Kategori — Layanan Pengembangan SDM' : 'Categories — Human Capital Services' }}
                </p>
                <h2 class="mt-5 text-display-lg leading-[1.08]" data-aos="fade-up" data-aos-delay="60">
                    <span class="block font-semibold text-white">{{ $isId ? 'Pilih Kategori' : 'Choose a Category' }}</span>
                    <span class="italic-accent text-gradient-hero block">{{ $isId ? 'Layanan Anda.' : 'For Your Growth.' }}</span>
                </h2>
                <p class="mx-auto mt-5 max-w-xl text-pretty leading-relaxed text-navy-100" data-aos="fade-up" data-aos-delay="120">
                    {{ $isId
                        ? 'Geser untuk menjelajah kategori layanan pengembangan SDM kami — dari pelatihan dan sertifikasi hingga headhunter dan penempatan tenaga kerja.'
                        : 'Drag to explore our human capital service categories — from training and certification to headhunting and workforce placement.' }}
                </p>
            </div>

            @if ($categories->isNotEmpty())
                <div class="mt-14 select-none" data-coverflow data-aos="fade-up">
                    <div class="cf-stage" data-cf-stage>
                        @foreach ($categories as $cat)
                            @php $img = $cat->image ? (str_starts_with($cat->image, 'http') ? $cat->image : Storage::url($cat->image)) : null; @endphp
                            <a href="{{ route('services.index') }}#{{ $cat->slug }}" class="cf-card group" data-cf-card aria-label="{{ $cat->name }}">
                                @if ($img)
                                    <img src="{{ $img }}" alt="{{ $cat->name }}" loading="lazy" draggable="false" class="absolute inset-0 h-full w-full object-cover">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-sky-600 to-navy-900"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-black/5"></div>
                                <div class="absolute inset-x-5 bottom-5">
                                    <p class="font-mono text-sm font-medium text-[#7ec2ff]">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                    <h3 class="mt-1 font-display text-xl leading-[1.12] text-white text-balance md:text-2xl">{{ $cat->name }}</h3>
                                </div>
                                <span class="cf-card-ring pointer-events-none absolute inset-0"></span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Controls --}}
                    <div class="mt-12 flex flex-col items-center gap-7">
                        <div class="flex items-center gap-4">
                            <button type="button" data-cf-prev class="grid h-12 w-12 place-items-center rounded-full border border-white/25 text-white transition hover:border-white hover:bg-white/10 active:scale-95" aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <div class="flex items-center gap-2" data-cf-dots></div>
                            <button type="button" data-cf-next class="grid h-12 w-12 place-items-center rounded-full border border-white/25 text-white transition hover:border-white hover:bg-white/10 active:scale-95" aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <a href="{{ route('services.index') }}" class="btn-blue group mt-2">
                            {{ $isId ? 'Lihat Semua Kategori' : 'View All Categories' }}
                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            @else
                <p class="mt-14 text-center text-navy-200">{{ $isId ? 'Belum ada kategori layanan.' : 'No service categories yet.' }}</p>
            @endif
        </div>
    </section>

    {{-- ===================== LATEST SERVICES ===================== --}}
    @if ($latestServices->isNotEmpty())
        <section class="section-sm bg-white border-t border-navy-50">
            <div class="container">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow mb-3" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $isId ? 'Pelatihan Terbaru' : 'Latest Training' }}</p>
                        <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $isId ? 'Pelatihan Terbaru Kami' : 'Our Latest Training' }}</h2>
                    </div>
                    <a href="{{ route('services.index') }}" class="link-underline shrink-0 font-medium" data-aos="fade-up">
                        {{ $isId ? 'Lihat Semua' : 'View All' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                {{-- Single-row carousel: smaller cards, slides sideways when there are many --}}
                <div class="swiper mt-10" data-carousel data-aos="fade-up">
                    <div class="swiper-wrapper">
                        @foreach ($latestServices as $service)
                            <div class="swiper-slide flex h-auto">
                                <a href="{{ route('services.show', $service->slug) }}" data-spotlight class="card card-hover group flex w-full flex-col overflow-hidden">
                                    <div class="relative aspect-[16/10] overflow-hidden bg-navy-100">
                                        @if ($service->image)
                                            <img src="{{ Storage::url($service->image) }}" alt="{{ $service->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-navy-200 to-navy-100"></div>
                                        @endif
                                        <span class="absolute left-3 top-3 rounded-full bg-sky-500 px-2.5 py-0.5 font-mono text-[9px] uppercase tracking-wider text-white">{{ $isId ? 'Baru' : 'New' }}</span>
                                    </div>
                                    <div class="flex flex-1 flex-col p-4">
                                        <p class="eyebrow-muted line-clamp-2 min-h-[2.4em] text-[10px] leading-[1.2]">{{ optional($service->category)->name }}</p>
                                        <h3 class="mt-2 line-clamp-2 font-display text-base font-semibold leading-snug text-navy">{{ $service->title }}</h3>
                                        @if ($service->short_description)<p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-navy-500">{{ $service->short_description }}</p>@endif
                                        <div class="mt-auto flex items-end justify-between border-t border-navy-100 pt-3">
                                            <div>
                                                @if ($service->price > 0)
                                                    <p class="font-mono text-[9px] uppercase tracking-wider text-navy-300">{{ __('site.common.from') }}</p>
                                                    <p class="font-display text-base font-semibold text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="font-display text-sm font-semibold text-navy">{{ $isId ? 'Hubungi kami' : 'Contact us' }}</p>
                                                @endif
                                            </div>
                                            <span class="grid h-8 w-8 place-items-center rounded-full border border-navy-200 transition-all group-hover:border-sky-500 group-hover:bg-sky-500 group-hover:text-white">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8 flex justify-center gap-2" data-carousel-pagination></div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== WHY CHOOSE US (image band + numbered row) ===================== --}}
    @php
        $reasons = $isId ? [
            'Dibimbing Praktisi Ahli', 'Program Dipersonalisasi', 'Sertifikasi Diakui Industri',
            'Pendampingan Menyeluruh', 'Materi Aplikatif', 'Jaringan Profesional', 'Harga Kompetitif',
        ] : [
            'Expert Practitioners', 'Personalized Programs', 'Recognized Certification',
            'End-to-End Support', 'Applied Material', 'Professional Network', 'Competitive Pricing',
        ];
    @endphp
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1920&q=80" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-navy-950/82"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/75 via-navy-950/45 to-navy-950/88"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        <div class="container relative flex min-h-[34rem] flex-col justify-between py-16 md:min-h-[40rem] md:py-20">
            {{-- Heading --}}
            <div class="text-center" data-aos="fade-up">
                <p class="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.22em] text-gold-soft">
                    <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $isId ? 'Keunggulan' : 'Advantages' }}
                </p>
                <h2 class="mt-4 font-display text-3xl text-white text-balance md:text-5xl">{{ $isId ? 'Alasan Memilih Kami?' : 'Why Choose Us?' }}</h2>
                <p class="mx-auto mt-5 max-w-xl text-navy-100 text-pretty">{{ $isId ? 'Keahlian praktisi, pendekatan yang dipersonalisasi, dan komitmen pada hasil nyata bagi organisasi Anda.' : 'Practitioner expertise, a personalized approach, and a commitment to real results for your organization.' }}</p>
            </div>

            {{-- Numbered row --}}
            <div class="mt-16 grid grid-cols-2 gap-y-8 border-t border-white/10 pt-8 sm:grid-cols-4 lg:grid-cols-7 lg:gap-y-0 lg:divide-x lg:divide-white/10">
                @foreach ($reasons as $i => $r)
                    <div class="lg:px-5" data-aos="fade-up" data-aos-delay="{{ $i * 55 }}">
                        <p class="font-mono text-lg text-gold-soft">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}.</p>
                        <p class="mt-3 font-display text-[15px] leading-snug text-white">{{ $r }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== BLOG (heading + horizontal carousel) ===================== --}}
    @if ($posts->isNotEmpty())
        <section class="section bg-white">
            <div class="container">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-center lg:gap-12">
                    {{-- Left --}}
                    <div class="lg:col-span-4" data-aos="fade-up">
                        <p class="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.22em] text-sky-600">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $isId ? 'Blog Terbaru' : 'Latest Blog' }}
                        </p>
                        <h2 class="mt-4 font-display text-3xl leading-tight text-navy text-balance md:text-4xl">{{ $isId ? 'Wawasan & artikel terbaru kami' : 'Our latest insights & articles' }}</h2>
                        <p class="mt-5 max-w-sm leading-relaxed text-navy-500 text-pretty">{{ $isId ? 'Perspektif, riset, dan praktik terbaik seputar human capital.' : 'Perspectives, research, and best practices on human capital.' }}</p>
                        <a href="{{ route('blog.index') }}" class="btn-blue mt-8">{{ $isId ? 'Semua Artikel' : 'All Articles' }}
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>

                    {{-- Right: carousel --}}
                    <div class="lg:col-span-8" data-hscroll data-hscroll-auto>
                        <div class="mb-6 flex items-center gap-3">
                            <button type="button" data-hscroll-prev class="grid h-10 w-10 place-items-center rounded-full bg-sky-600 text-white transition hover:bg-sky-700 active:scale-95" aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" data-hscroll-next class="grid h-10 w-10 place-items-center rounded-full bg-sky-600 text-white transition hover:bg-sky-700 active:scale-95" aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div data-hscroll-track class="flex cursor-grab snap-x snap-mandatory select-none gap-6 overflow-x-auto scroll-smooth pb-2 active:cursor-grabbing [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            @foreach ($posts as $post)
                                @php $bimg = $post->featured_image ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : Storage::url($post->featured_image)) : null; @endphp
                                <a href="{{ route('blog.show', $post->slug) }}" data-spotlight class="group min-w-0 shrink-0 basis-[82%] snap-start overflow-hidden rounded-2xl sm:basis-[calc((100%_-_1.5rem)/2)] lg:basis-[calc((100%_-_3rem)/3)]">
                                    <p class="font-mono text-lg text-navy-300">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.</p>
                                    <h3 class="mt-2 line-clamp-2 min-h-[3.25rem] font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $post->title }}</h3>
                                    @if (optional($post->category)->name)
                                        <p class="mt-1 font-mono text-[10px] uppercase tracking-wider text-gold-deep">{{ $post->category->name }}</p>
                                    @endif
                                    <p class="mt-0.5 font-mono text-[10px] uppercase tracking-wider text-navy-300">{{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                                    <div class="relative mt-4 aspect-[4/3] overflow-hidden rounded-2xl border border-navy-100 bg-navy-900">
                                        @if ($bimg)
                                            <img src="{{ $bimg }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                                        @else
                                            <div class="absolute inset-0 aurora opacity-60"></div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== PORTFOLIO ===================== --}}
    @if ($portfolios->isNotEmpty())
        <section class="section-sm relative overflow-hidden">
            {{-- Background image + dark overlay (kept light enough for the photo to show) --}}
            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1920&q=80" alt="" loading="lazy"
                 class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-navy-950/85 via-navy-950/55 to-navy-950/80"></div>
            <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>

            <div class="container relative">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-xl">
                        <p class="eyebrow mb-3" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ __('site.home.portfolio_kicker') }}</p>
                        <h2 class="text-2xl font-semibold text-white text-balance md:text-3xl" data-aos="fade-up">{{ __('site.home.portfolio_title') }}</h2>
                    </div>
                    <a href="{{ route('portfolio.index') }}" data-aos="fade-up"
                       class="group inline-flex shrink-0 items-center gap-2 rounded-full border border-white/25 px-6 py-3 text-sm font-medium text-white transition duration-300 hover:border-white hover:bg-white hover:text-navy-950">
                        {{ $isId ? 'Lihat Semua Portofolio' : 'View All Portfolio' }}
                        <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($portfolios as $p)
                        <a href="{{ route('portfolio.show', $p->slug) }}"
                           class="group overflow-hidden rounded-2xl border border-white/10 bg-white/[0.06] backdrop-blur-sm transition duration-300 hover:-translate-y-1 hover:border-white/25 hover:bg-white/[0.1]"
                           data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                            <div class="relative aspect-[16/10] overflow-hidden bg-navy-900">
                                @if ($p->cover_image)
                                    <img src="{{ str_starts_with($p->cover_image, 'http') ? $p->cover_image : Storage::url($p->cover_image) }}" alt="{{ $p->title }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="h-full w-full bg-gradient-to-br from-navy-700 to-navy-900"></div>
                                @endif
                            </div>
                            <div class="p-5">
                                @if ($p->category)<p class="font-mono text-[10px] uppercase tracking-label text-gold">{{ $p->category->name }}</p>@endif
                                <h3 class="mt-2 font-display text-base font-semibold text-white">{{ $p->title }}</h3>
                                @if ($p->client_name)<p class="mt-1 text-xs text-white/50">{{ $p->client_name }}</p>@endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== INSTAGRAM UPDATES ===================== --}}
    <section class="section bg-white border-t border-navy-50">
        <div class="container">
            <div class="mx-auto max-w-2xl text-center mb-12" data-aos="fade-up">
                <span class="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.18em]">
                    <svg class="h-4 w-4 text-sky" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                    <span class="text-sky">{{ $isId ? 'Instagram' : 'Instagram' }}</span>
                    <span class="text-navy-300">— @deltatigaenam</span>
                </span>
                <h2 class="mt-4 leading-[1.05] [font-size:clamp(2.1rem,5vw,3.6rem)]">
                    <span class="block font-display tracking-tight text-navy">{{ $isId ? 'Update Terbaru' : 'Latest Updates' }}</span>
                </h2>
                <p class="mx-auto mt-4 max-w-xl text-pretty leading-relaxed text-navy-400 text-sm">
                    {{ $isId
                        ? 'Ikuti kegiatan terbaru dan informasi menarik dari kami di Instagram.'
                        : 'Follow our latest activities and interesting information on Instagram.' }}
                </p>
            </div>

            <div class="mx-auto grid gap-8 md:grid-cols-2 max-w-4xl">
                {{-- Left Post --}}
                @php
                    $igLink1 = 'https://www.instagram.com/deltatigaenam/?utm_source=ig_embed&ig_rid=AoWIewf3tcwwvM1ipJJhX_8';
                @endphp
                <div class="card overflow-hidden border-navy-100 shadow-card transition-all duration-300 hover:shadow-lift hover:-translate-y-1" data-aos="fade-right">
                    <div class="flex items-center justify-between p-3.5 border-b border-navy-50 bg-white">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/logodelta36.png') }}" alt="Delta Tiga Enam" class="h-6 w-6 shrink-0 rounded-lg object-contain">

                            <div class="leading-tight">
                                <p class="text-xs font-semibold text-navy">deltatigaenam</p>
                                <p class="text-[9px] text-navy-300">Original audio</p>
                            </div>
                        </div>
                        <a href="{{ $igLink1 }}" target="_blank" rel="noopener" class="rounded bg-sky hover:bg-sky-600 px-3 py-1 text-[10px] font-semibold text-white transition">View profile</a>
                    </div>
                    <div class="relative aspect-square overflow-hidden bg-navy-950 group">
                        <img src="/images/certified-risk-management.png" alt="Certified Risk Management Batch IV" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-transparent p-5 pt-12 flex flex-col justify-end text-white">
                            <span class="inline-block self-start rounded bg-sky px-2 py-0.5 font-mono text-[9px] uppercase tracking-wider text-white">BATCH IV</span>
                            <h3 class="font-display text-lg font-bold mt-2 leading-tight">Certified Risk Management</h3>
                            <p class="text-[11px] text-navy-200 mt-0.5">PT Delta Tiga Enam</p>
                            <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-3 text-[10px] font-mono text-navy-300"><span>19 - 22 Agustus 2025</span></div>
                        </div>
                    </div>
                    <div class="p-3.5 border-t border-navy-50 flex items-center justify-between bg-white text-navy-400">
                        <div class="flex items-center gap-3.5">
                            <a href="{{ $igLink1 }}" target="_blank" rel="noopener" class="hover:text-rose-500 transition"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg></a>
                            <a href="{{ $igLink1 }}" target="_blank" rel="noopener" class="hover:text-sky transition"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-1.923 2.202 4.968 4.968 0 0 0 2.803-.832c.43-.284.974-.354 1.5-.178 1.12.375 2.316.577 3.561.577z"/></svg></a>
                        </div>
                        <a href="{{ $igLink1 }}" target="_blank" rel="noopener" class="text-xs font-semibold text-sky hover:underline inline-flex items-center gap-1"><span>{{ $isId ? 'Lihat di Instagram' : 'View on Instagram' }}</span><span>&rarr;</span></a>
                    </div>
                </div>

                {{-- Right Post --}}
                @php
                    $igLink2 = 'https://www.instagram.com/deltatigaenam/?utm_source=ig_embed&ig_rid=AzGOKnldj_0aFItwS2zdG-q';
                @endphp
                <div class="card overflow-hidden border-navy-100 shadow-card transition-all duration-300 hover:shadow-lift hover:-translate-y-1" data-aos="fade-left">
                    <div class="flex items-center justify-between p-3.5 border-b border-navy-50 bg-white">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/logodelta36.png') }}" alt="Delta Tiga Enam" class="h-6 w-6 shrink-0 rounded-lg object-contain">

                            <div class="leading-tight">
                                <p class="text-xs font-semibold text-navy">deltatigaenam</p>
                                <p class="text-[9px] text-navy-300">Original audio</p>
                            </div>
                        </div>
                        <a href="{{ $igLink2 }}" target="_blank" rel="noopener" class="rounded bg-sky hover:bg-sky-600 px-3 py-1 text-[10px] font-semibold text-white transition">View profile</a>
                    </div>
                    <div class="relative aspect-square overflow-hidden bg-navy-950 group">
                        <img src="/images/training-seminar.png" alt="Delta Tiga Enam Training Seminar" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-transparent p-5 pt-12 flex flex-col justify-end text-white">
                            <span class="inline-block self-start rounded bg-sky px-2 py-0.5 font-mono text-[9px] uppercase tracking-wider text-white">SEMINAR</span>
                            <h3 class="font-display text-lg font-bold mt-2 leading-tight">Pengembangan Kompetensi SDM</h3>
                            <p class="text-[11px] text-navy-200 mt-0.5">PT Delta Tiga Enam</p>
                            <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-3 text-[10px] font-mono text-navy-300"><span>05 - 06 Agustus 2025</span></div>
                        </div>
                    </div>
                    <div class="p-3.5 border-t border-navy-50 flex items-center justify-between bg-white text-navy-400">
                        <div class="flex items-center gap-3.5">
                            <a href="{{ $igLink2 }}" target="_blank" rel="noopener" class="hover:text-rose-500 transition"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg></a>
                            <a href="{{ $igLink2 }}" target="_blank" rel="noopener" class="hover:text-sky transition"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-1.923 2.202 4.968 4.968 0 0 0 2.803-.832c.43-.284.974-.354 1.5-.178 1.12.375 2.316.577 3.561.577z"/></svg></a>
                        </div>
                        <a href="{{ $igLink2 }}" target="_blank" rel="noopener" class="text-xs font-semibold text-sky hover:underline inline-flex items-center gap-1"><span>{{ $isId ? 'Lihat di Instagram' : 'View on Instagram' }}</span><span>&rarr;</span></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== UPCOMING AGENDAS ===================== --}}
    <section class="section relative overflow-hidden border-t border-navy-50 bg-paper">
        {{-- Animated background --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-1/2 top-1/2 h-[560px] w-[560px] -translate-x-1/2 -translate-y-1/2 rounded-full" style="background: radial-gradient(circle, rgba(28,125,224,0.10), transparent 70%);"></div>
            <div class="absolute -left-24 top-16 h-72 w-72 animate-float-slow rounded-full bg-sky-300/25 blur-3xl"></div>
            <div class="absolute -right-24 top-1/3 h-80 w-80 animate-float rounded-full bg-gold-soft/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-72 w-72 animate-float-slow rounded-full bg-sky-200/30 blur-3xl"></div>
            <div class="absolute -left-20 -top-20 h-64 w-64 animate-spin-slow rounded-full border border-dashed border-sky-300/40"></div>
        </div>

        <div class="container relative">
            {{-- Centered header --}}
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <p class="eyebrow inline-flex items-center justify-center gap-2">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4.5" width="18" height="17" rx="2.5"/><path d="M3 9h18M8 2.5v4M16 2.5v4" stroke-linecap="round"/></svg>
                    {{ $isId ? 'Jadwal Kegiatan' : 'Agenda' }}
                </p>
                <h2 class="mt-3 text-display-xl font-semibold text-navy text-balance">{{ $isId ? 'Agenda Mendatang Kami' : 'Our Upcoming Agenda' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-navy-450">
                    {{ $isId
                        ? 'Ikuti kelas pelatihan, sertifikasi, dan kegiatan terbaru dari kami. Geser untuk melihat jadwal lengkapnya.'
                        : 'Join our latest training classes, certifications, and events. Swipe to explore the full schedule.' }}
                </p>
            </div>

            @if ($upcomingAgendas->isNotEmpty())
                <div class="relative mt-12" data-hscroll data-hscroll-auto data-aos="fade-up">
                    {{-- Arrows (desktop) --}}
                    <button type="button" data-hscroll-prev aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}"
                        class="absolute left-0 top-1/2 z-20 hidden h-12 w-12 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 lg:grid">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" data-hscroll-next aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}"
                        class="absolute right-0 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 lg:grid">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div data-hscroll-track class="flex cursor-grab snap-x snap-mandatory select-none gap-6 overflow-x-auto scroll-smooth pb-4 active:cursor-grabbing [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($upcomingAgendas as $agenda)
                            <a href="{{ route('agenda.index') }}" data-spotlight
                               class="group relative flex min-w-0 shrink-0 basis-[80%] snap-start flex-col overflow-hidden rounded-3xl border border-navy-100 bg-white shadow-card transition-all duration-500 ease-out-soft hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-lift sm:basis-[calc((100%_-_1.5rem)/2)] md:basis-[calc((100%_-_3rem)/3)] lg:basis-[calc((100%_-_4.5rem)/4)]">

                            {{-- Media (compact) --}}
                            <div class="relative aspect-[16/10] overflow-hidden bg-navy-900">
                                @if ($agenda->image)
                                    <img src="{{ Storage::url($agenda->image) }}" alt="{{ $agenda->title }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 ease-out-soft group-hover:scale-[1.05]">
                                @else
                                    <div class="absolute inset-0" style="background: linear-gradient(150deg,#12365f 0%,#0a1f3c 100%);"></div>
                                    <div class="absolute inset-0 aurora opacity-40"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-navy-950/55 via-navy-950/5 to-transparent"></div>

                                {{-- Compact date badge --}}
                                <div class="absolute left-3 top-3 flex w-11 flex-col items-center rounded-xl bg-white/95 py-1.5 text-center shadow-lift ring-1 ring-white/60 backdrop-blur">
                                    <span class="font-display text-lg font-semibold leading-none text-navy">{{ $agenda->starts_at->format('d') }}</span>
                                    <span class="font-mono text-[9px] font-bold uppercase tracking-[0.12em] text-gold-deep">{{ $agenda->starts_at->translatedFormat('M') }}</span>
                                </div>

                                {{-- Status pill --}}
                                <span class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-sky-600/90 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-[0.1em] text-white shadow-lift backdrop-blur">
                                    <span class="h-1 w-1 rounded-full bg-white/90"></span>
                                    {{ $isId ? 'Akan Datang' : 'Upcoming' }}
                                </span>
                            </div>

                            {{-- Body (compact) --}}
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center gap-2 font-mono text-[11px] text-navy-400">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="shrink-0">{{ $agenda->starts_at->translatedFormat('H:i') }} WIB</span>
                                    @if ($agenda->location)
                                        <span class="text-navy-200">&middot;</span>
                                        <span class="truncate">{{ $agenda->location }}</span>
                                    @endif
                                </div>

                                <h3 class="mt-2.5 line-clamp-2 font-display text-base font-semibold leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">
                                    {{ $agenda->title }}
                                </h3>

                                <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-xs font-bold uppercase tracking-[0.1em] text-sky-600 transition-colors group-hover:text-sky-700">
                                    {{ $isId ? 'Selengkapnya' : 'Read More' }}
                                    <svg class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10 text-center" data-aos="fade-up">
                    <a href="{{ route('agenda.index') }}" class="inline-flex items-center gap-2 rounded-full border border-navy-200 px-6 py-3 text-sm font-medium text-navy transition duration-300 hover:border-navy hover:bg-navy hover:text-white">
                        {{ $isId ? 'Lihat Semua Agenda' : 'View All Agendas' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            @else
                {{-- Premium Empty State / WhatsApp Lead Generation --}}
                <div class="mt-10 card border-dashed border-2 border-navy-200 bg-navy-50/20 p-8 md:p-12 text-center rounded-3xl" data-aos="fade-up">
                    <div class="max-w-md mx-auto space-y-4">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy-50 text-navy-400 mx-auto">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                        </span>
                        <h3 class="font-display text-lg font-semibold text-navy">
                            {{ $isId ? 'Belum Ada Agenda Terdekat' : 'No Upcoming Agendas' }}
                        </h3>
                        <p class="text-xs text-navy-400 leading-relaxed text-balance">
                            {{ $isId
                                ? 'Kami sedang merancang berbagai kelas pelatihan dan jadwal sertifikasi kompetensi terbaru. Ingin tahu jadwal terdekat atau berdiskusi?'
                                : 'We are planning various training classes and new competence certification schedules. Want to know the nearest schedule or discuss?' }}
                        </p>
                        <div class="pt-2">
                            <a href="https://wa.me/62818834766?text=Halo%20Delta%20Tiga%20Enam,%20saya%20ingin%20tahu%20jadwal%20pelatihan%20dan%20sertifikasi%20terdekat..." target="_blank" rel="noopener" class="btn bg-emerald-500 hover:bg-emerald-600 text-white shadow-lift inline-flex items-center gap-2">
                                <svg class="h-4.5 w-4.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.861-1.868-4.339-2.897-6.97-2.899-5.437 0-9.862 4.37-9.866 9.801-.001 1.737.457 3.432 1.328 4.935l-.995 3.631 3.723-.979zm7.53-7.535c-.175-.29-.64-.464-.875-.58-.233-.115-1.393-.683-1.605-.765-.213-.083-.368-.124-.523.11-.155.233-.6 1.015-.736 1.173-.136.158-.271.176-.505.06-.233-.116-.988-.364-1.882-1.163-.695-.62-1.164-1.386-1.3-1.62-.137-.233-.015-.359.102-.475.106-.104.233-.272.35-.407.115-.136.154-.233.232-.387.078-.155.039-.29-.02-.406-.058-.115-.523-1.26-.716-1.725-.19-.453-.383-.39-.523-.397-.135-.007-.29-.008-.445-.008-.156 0-.41.058-.625.29-.215.233-.82.802-.82 1.953s.836 2.26 1.07 2.57c.233.29 1.644 2.51 3.982 3.52.556.24 1 .383 1.343.492.56.179 1.07.154 1.472.094.448-.067 1.393-.57 1.587-1.12.195-.55.195-1.022.136-1.122-.058-.1-.233-.175-.407-.29z"/></svg>
                                <span>WhatsApp Admin</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== TESTIMONIALS ===================== --}}
    <x-testimonial-columns :testimonials="$testimonials" />

    {{-- ===================== FAQ SECTION ===================== --}}
    <section class="section bg-white border-t border-navy-50">
        <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">
            
            {{-- Left Side: Heading & WhatsApp CTA --}}
            <div class="lg:col-span-5 flex flex-col space-y-8 lg:sticky lg:top-28 lg:self-start" data-aos="fade-right">
                <div class="space-y-4">
                    <p class="eyebrow"><span class="rule-gold mr-3"></span>FAQ</p>
                    <h2 class="text-display-lg font-semibold text-navy text-balance">
                        {{ $isId ? 'Pertanyaan yang Sering Diajukan' : 'Frequently Asked Questions' }}
                    </h2>
                    <p class="text-sm leading-relaxed text-navy-450">
                        {{ $isId
                            ? 'Temukan jawaban cepat untuk pertanyaan umum mengenai sertifikasi, pelatihan, rekrutmen, dan kemitraan di PT Delta Tiga Enam.'
                            : 'Find quick answers to common questions about certification, training, recruitment, and partnerships at PT Delta Tiga Enam.' }}
                    </p>
                </div>

                {{-- WhatsApp CTA Card --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-navy-900 to-navy-950 p-7 text-white shadow-lift ring-1 ring-white/5 space-y-4 md:p-8">
                    <div class="pointer-events-none absolute -right-14 -top-14 h-40 w-40 rounded-full bg-emerald-500/15 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-16 -left-10 h-40 w-40 rounded-full bg-gold/10 blur-3xl"></div>
                    <div class="relative flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-500/15 text-emerald-400 ring-1 ring-emerald-400/25">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.861-1.868-4.339-2.897-6.97-2.899-5.437 0-9.862 4.37-9.866 9.801-.001 1.737.457 3.432 1.328 4.935l-.995 3.631 3.723-.979zm7.53-7.535c-.175-.29-.64-.464-.875-.58-.233-.115-1.393-.683-1.605-.765-.213-.083-.368-.124-.523.11-.155.233-.6 1.015-.736 1.173-.136.158-.271.176-.505.06-.233-.116-.988-.364-1.882-1.163-.695-.62-1.164-1.386-1.3-1.62-.137-.233-.015-.359.102-.475.106-.104.233-.272.35-.407.115-.136.154-.233.232-.387.078-.155.039-.29-.02-.406-.058-.115-.523-1.26-.716-1.725-.19-.453-.383-.39-.523-.397-.135-.007-.29-.008-.445-.008-.156 0-.41.058-.625.29-.215.233-.82.802-.82 1.953s.836 2.26 1.07 2.57c.233.29 1.644 2.51 3.982 3.52.556.24 1 .383 1.343.492.56.179 1.07.154 1.472.094.448-.067 1.393-.57 1.587-1.12.195-.55.195-1.022.136-1.122-.058-.1-.233-.175-.407-.29z"/></svg>
                        </span>
                        <h4 class="font-display font-semibold text-white text-base">{{ $isId ? 'Punya pertanyaan lain?' : 'Have more questions?' }}</h4>
                    </div>
                    <p class="relative text-xs text-navy-200 leading-relaxed">
                        {{ $isId
                            ? 'Jika Anda tidak menemukan jawaban yang dicari, hubungi admin kami melalui WhatsApp untuk konsultasi cepat.'
                            : 'If you cannot find the answer you are looking for, contact our admin via WhatsApp for a quick consultation.' }}
                    </p>
                    <div class="relative pt-2">
                        <a href="https://wa.me/62818834766?text=Halo%20Delta%20Tiga%20Enam,%20saya%20ingin%20bertanya%20mengenai..." target="_blank" rel="noopener" class="w-full btn bg-emerald-500 hover:bg-emerald-600 text-white shadow-lift inline-flex items-center justify-center gap-2">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.5-5.739-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.97-1.861-1.868-4.339-2.897-6.97-2.899-5.437 0-9.862 4.37-9.866 9.801-.001 1.737.457 3.432 1.328 4.935l-.995 3.631 3.723-.979zm7.53-7.535c-.175-.29-.64-.464-.875-.58-.233-.115-1.393-.683-1.605-.765-.213-.083-.368-.124-.523.11-.155.233-.6 1.015-.736 1.173-.136.158-.271.176-.505.06-.233-.116-.988-.364-1.882-1.163-.695-.62-1.164-1.386-1.3-1.62-.137-.233-.015-.359.102-.475.106-.104.233-.272.35-.407.115-.136.154-.233.232-.387.078-.155.039-.29-.02-.406-.058-.115-.523-1.26-.716-1.725-.19-.453-.383-.39-.523-.397-.135-.007-.29-.008-.445-.008-.156 0-.41.058-.625.29-.215.233-.82.802-.82 1.953s.836 2.26 1.07 2.57c.233.29 1.644 2.51 3.982 3.52.556.24 1 .383 1.343.492.56.179 1.07.154 1.472.094.448-.067 1.393-.57 1.587-1.12.195-.55.195-1.022.136-1.122-.058-.1-.233-.175-.407-.29z"/></svg>
                            <span>Hubungi WA Admin</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Side: Accordion driven by AlpineJS --}}
            <div class="lg:col-span-7" data-aos="fade-left" x-data="{ activeFaq: 0 }">
                @php
                    $faqs = $isId ? [
                        [
                            'q' => 'Apa saja layanan utama yang ditawarkan oleh PT Delta Tiga Enam?',
                            'a' => 'Kami menawarkan solusi human capital terintegrasi yang mencakup Pelatihan SDM, Sertifikasi Kompetensi Profesi (BNSP), Asesmen & Seleksi Karyawan, serta Headhunter & Penempatan Tenaga Kerja.',
                        ],
                        [
                            'q' => 'Apa manfaat bekerja sama dengan konsultan human capital seperti Delta Tiga Enam?',
                            'a' => 'Sebagai konsultan human capital, kami membantu perusahaan mengelola SDM secara strategis — mulai dari pemetaan kompetensi, peningkatan produktivitas, hingga efisiensi rekrutmen. Anda mendapatkan pendampingan ahli sehingga setiap keputusan terkait talenta menjadi lebih terukur, hemat biaya, dan berdampak langsung pada kinerja organisasi.',
                        ],
                        [
                            'q' => 'Apakah sertifikasi kompetensi dari Delta Tiga Enam resmi?',
                            'a' => 'Ya, seluruh program sertifikasi kompetensi kami diselenggarakan secara resmi bekerja sama dengan LSP berlisensi BNSP (Badan Nasional Sertifikasi Profesi) sehingga diakui di tingkat nasional oleh berbagai sektor industri.',
                        ],
                        [
                            'q' => 'Berapa lama durasi pelatihan dan apakah peserta memperoleh sertifikat?',
                            'a' => 'Durasi pelatihan bervariasi mulai dari 1–5 hari tergantung program dan tingkat kompetensinya. Setiap peserta yang menyelesaikan pelatihan akan memperoleh sertifikat; khusus program sertifikasi kompetensi, sertifikat diterbitkan resmi oleh LSP berlisensi BNSP setelah peserta dinyatakan kompeten dalam uji sertifikasi.',
                        ],
                        [
                            'q' => 'Bagaimana cara mendaftar program pelatihan atau sertifikasi?',
                            'a' => 'Anda dapat mendaftar langsung dengan memilih layanan di website kami, menghubungi admin kami melalui WhatsApp, atau mengirimkan detail kebutuhan Anda melalui formulir kontak.',
                        ],
                        [
                            'q' => 'Apakah program pelatihan bisa diadakan khusus untuk internal perusahaan (In-House)?',
                            'a' => 'Tentu saja. Kami menyediakan layanan In-House Training dengan materi, jadwal, dan metode penyampaian yang dirancang khusus menyesuaikan kebutuhan peningkatan kompetensi staf di perusahaan Anda.',
                        ],
                    ] : [
                        [
                            'q' => 'What are the main services offered by PT Delta Tiga Enam?',
                            'a' => 'We offer integrated human capital solutions covering HR Training, Professional Competence Certification (BNSP), Employee Assessment & Selection, and Headhunting & Talent Placement.',
                        ],
                        [
                            'q' => 'What are the benefits of working with a human capital consultant like Delta Tiga Enam?',
                            'a' => 'As a human capital consultant, we help companies manage their workforce strategically — from competency mapping and productivity improvement to more efficient recruitment. You gain expert guidance so every talent-related decision becomes measurable, cost-effective, and directly impacts organizational performance.',
                        ],
                        [
                            'q' => 'Are the competence certifications official?',
                            'a' => 'Yes, all our competency certification programs are officially conducted in partnership with BNSP-licensed LSPs, ensuring national recognition across industries.',
                        ],
                        [
                            'q' => 'How long is the training and do participants receive a certificate?',
                            'a' => 'Training duration varies from 1–5 days depending on the program and competency level. Every participant who completes the training receives a certificate; for competency certification programs, certificates are officially issued by BNSP-licensed LSPs once the participant is declared competent in the assessment.',
                        ],
                        [
                            'q' => 'How can I register for a training or certification program?',
                            'a' => 'You can register directly by selecting the service on our website, contacting our admin via WhatsApp, or sending your details through the contact form.',
                        ],
                        [
                            'q' => 'Can training programs be customized for internal company needs (In-House)?',
                            'a' => 'Absolutely. We provide customized In-House Training with curriculum, schedule, and methods tailored to address specific competence needs in your organization.',
                        ],
                    ];
                @endphp

                <div class="space-y-3">
                    @foreach ($faqs as $i => $faq)
                        <div class="overflow-hidden rounded-2xl border bg-white transition-all duration-300"
                             :class="activeFaq === {{ $i }} ? 'border-gold/40 shadow-card' : 'border-navy-100 hover:border-navy-200'">
                            <button type="button"
                                    x-on:click="activeFaq = activeFaq === {{ $i }} ? null : {{ $i }}"
                                    :aria-expanded="activeFaq === {{ $i }}"
                                    class="flex w-full items-center gap-4 px-5 py-5 text-left focus:outline-none md:px-6">
                                <span class="font-mono text-sm font-bold tabular-nums transition-colors duration-300"
                                      :class="activeFaq === {{ $i }} ? 'text-gold-deep' : 'text-navy-300'">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="flex-1 font-display text-[15px] font-semibold leading-snug text-navy md:text-[17px]">{{ $faq['q'] }}</span>
                                <span class="relative grid h-8 w-8 shrink-0 place-items-center rounded-full transition-colors duration-300"
                                      :class="activeFaq === {{ $i }} ? 'bg-navy text-white' : 'bg-navy-50 text-navy-500'">
                                    <span class="absolute h-0.5 w-3.5 rounded-full bg-current"></span>
                                    <span class="absolute h-3.5 w-0.5 rounded-full bg-current transition-transform duration-300"
                                          :class="activeFaq === {{ $i }} ? 'scale-y-0' : ''"></span>
                                </span>
                            </button>
                            <div x-show="activeFaq === {{ $i }}"
                                 x-transition:enter="transition-all ease-out duration-300"
                                 x-transition:enter-start="opacity-0 max-h-0"
                                 x-transition:enter-end="opacity-100 max-h-[600px]"
                                 x-transition:leave="transition-all ease-in duration-200"
                                 x-transition:leave-start="opacity-100 max-h-[600px]"
                                 x-transition:leave-end="opacity-0 max-h-0"
                                 class="overflow-hidden">
                                <div class="px-5 pb-6 md:px-6">
                                    <div class="ml-9 border-l-2 border-gold/40 pl-4 text-sm leading-relaxed text-navy-500">
                                        {{ $faq['a'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>

    {{-- ===================== PARTNERS & CLIENTS ===================== --}}
    <x-partners-clients :partners="$partners" :clients="$clients" />


</x-layout>
