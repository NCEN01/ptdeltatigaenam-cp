@php use Illuminate\Support\Facades\Storage; $isId = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.home')">

    {{-- ===================== HERO CAROUSEL ===================== --}}
    @php
        $heroStats = $isId
            ? [['500+', 'Profesional Terlatih'], ['98%', 'Kepuasan Klien'], ['10+', 'Tahun Pengalaman']]
            : [['500+', 'Professionals Trained'], ['98%', 'Client Satisfaction'], ['10+', 'Years Experience']];
        $heroSlides = $isId ? [
            ['img' => 'photo-1524178232363-1fb2b075b655', 'cat' => 'Pelatihan', 'title' => 'Pelatihan SDM yang Berdampak', 'desc' => 'Program pelatihan karyawan yang relevan dan inovatif untuk meningkatkan kompetensi serta produktivitas tim Anda.'],
            ['img' => 'photo-1542744173-8e7e53415bb0', 'cat' => 'Sertifikasi', 'title' => 'Sertifikasi Profesi Terpercaya', 'desc' => 'Sertifikasi kompetensi yang diakui secara profesional untuk meningkatkan kredibilitas dan daya saing SDM Anda.'],
            ['img' => 'photo-1521737604893-d14cc237f11d', 'cat' => 'Headhunter', 'title' => 'Headhunter & Penempatan Talenta', 'desc' => 'Penyeleksian dan penempatan talenta terbaik yang sesuai dengan kebutuhan strategis perusahaan Anda.'],
        ] : [
            ['img' => 'photo-1524178232363-1fb2b075b655', 'cat' => 'Training', 'title' => 'Impactful HR Training', 'desc' => "Relevant, innovative employee training programs to boost your team's competency and productivity."],
            ['img' => 'photo-1542744173-8e7e53415bb0', 'cat' => 'Certification', 'title' => 'Trusted Professional Certification', 'desc' => "Professionally recognized competency certification to strengthen your people's credibility and edge."],
            ['img' => 'photo-1521737604893-d14cc237f11d', 'cat' => 'Headhunting', 'title' => 'Headhunting & Talent Placement', 'desc' => 'Selection and placement of top talent aligned with your strategic business needs.'],
        ];
    @endphp
    <section class="relative">
        <div class="swiper hero-carousel" data-hero-carousel>
            <div class="swiper-wrapper">
                @foreach ($heroSlides as $slide)
                    <div class="swiper-slide relative min-h-[82svh] overflow-hidden bg-navy-950">
                        <img src="https://images.unsplash.com/{{ $slide['img'] }}?auto=format&fit=crop&w=1920&q=80"
                             alt="{{ $slide['title'] }}" {{ $loop->first ? 'loading=eager fetchpriority=high' : 'loading=lazy' }}
                             class="hero-slide-img absolute inset-0 h-full w-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-navy-950/92 via-navy-950/70 to-navy-950/20"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-navy-950/30"></div>

                        <div class="container relative flex min-h-[82svh] items-center">
                            <div class="hero-content max-w-2xl py-28 text-white">
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 font-mono text-[11px] font-medium uppercase tracking-[0.18em] text-white backdrop-blur">
                                    <svg class="h-3.5 w-3.5 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/></svg>
                                    {{ $slide['cat'] }}
                                </span>
                                <h1 class="mt-6 font-display font-normal leading-[1.05] text-balance [font-size:clamp(2.2rem,5.2vw,4rem)]">{{ $slide['title'] }}</h1>
                                <p class="mt-6 max-w-xl text-base leading-relaxed text-navy-100 text-pretty md:text-lg">{{ $slide['desc'] }}</p>
                                <div class="mt-9 flex flex-wrap items-center gap-4">
                                    <a href="{{ route('services.index') }}" class="btn-blue">{{ __('site.cta.explore') }}</a>
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

            {{-- pagination --}}
            <div class="absolute inset-x-0 bottom-7 z-10 flex justify-center gap-2" data-hero-carousel-pagination></div>
        </div>

        {{-- stats strip --}}
        <div class="border-b border-navy-100 bg-white">
            <div class="container">
                <div class="mx-auto grid max-w-3xl grid-cols-3 gap-6 py-8">
                    @foreach ($heroStats as $stat)
                        @php
                            $numVal = (int) filter_var($stat[0], FILTER_SANITIZE_NUMBER_INT);
                            $suffix = str_replace((string) $numVal, '', $stat[0]);
                        @endphp
                        <div class="group cursor-default text-center transition-all duration-300 hover:-translate-y-1">
                            <p class="font-sans text-3xl font-black tracking-tight text-navy transition-colors duration-300 group-hover:text-sky-600 md:text-4xl" data-counter="{{ $numVal }}" data-counter-suffix="{{ $suffix }}">0{{ $suffix }}</p>
                            <p class="mt-1 font-mono text-[10px] uppercase tracking-wider text-navy-400">{{ $stat[1] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TRAINING CATEGORIES ===================== --}}
    @php
        $catColors = [
            'from-sky-500 to-sky-700',
            'from-rose-400 to-rose-600',
            'from-emerald-400 to-emerald-600',
            'from-violet-500 to-violet-700',
            'from-amber-400 to-amber-600',
            'from-cyan-400 to-cyan-600',
            'from-fuchsia-500 to-fuchsia-700',
        ];
    @endphp
    <section class="relative overflow-hidden bg-gradient-to-br from-navy-950 via-navy-900 to-sky-700 py-20 text-white md:py-28">
        {{-- starfield + glow --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.14]" style="background-image: radial-gradient(rgba(255,255,255,0.6) 1px, transparent 1px); background-size: 42px 42px;"></div>
        <div class="pointer-events-none absolute left-1/2 top-0 h-80 w-[60rem] -translate-x-1/2 rounded-full bg-sky-700/20 blur-[120px]"></div>

        {{-- floating chat bubbles --}}
        <div class="pointer-events-none absolute left-[7%] top-28 hidden animate-float md:block">
            <span class="relative inline-flex items-center gap-1.5 rounded-full bg-teal-300 px-3.5 py-1.5 text-xs font-semibold text-navy-950 shadow-lg">
                <span class="h-2 w-2 rounded-full bg-navy-950/60"></span>{{ $isId ? 'Mentor' : 'Mentor' }}
                <span class="absolute -bottom-1 left-5 h-3 w-3 rotate-45 bg-teal-300"></span>
            </span>
        </div>
        <div class="pointer-events-none absolute right-[7%] top-44 hidden animate-float-slow md:block">
            <span class="relative inline-flex items-center gap-1.5 rounded-full bg-rose-400 px-3.5 py-1.5 text-xs font-semibold text-navy-950 shadow-lg">
                {{ $isId ? 'Peserta' : 'Learner' }}<span class="h-2 w-2 rounded-full bg-navy-950/60"></span>
                <span class="absolute -top-1 right-5 h-3 w-3 rotate-45 bg-rose-400"></span>
            </span>
        </div>

        <div class="container relative">
            <div class="mx-auto max-w-2xl text-center">
                <span class="inline-flex items-center gap-2 font-mono text-[11px] uppercase tracking-[0.18em]" data-aos="fade-up">
                    <svg class="h-3.5 w-3.5 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/></svg>
                    <span class="text-gold">{{ $isId ? 'Kategori' : 'Categories' }}</span>
                    <span class="text-navy-300">— {{ $isId ? 'Pilih Pelatihan Anda' : 'Choose Your Training' }}</span>
                </span>
                <h2 class="mt-6 leading-[1.05] [font-size:clamp(2.1rem,5vw,3.6rem)]" data-aos="fade-up" data-aos-delay="60">
                    <span class="block font-sans font-black tracking-tight text-white">{{ $isId ? 'Pilih Kategori' : 'Choose a Category' }}</span>
                    <span class="block font-display font-normal italic text-navy-300">{{ $isId ? 'Pelatihan Anda' : 'For Your Growth' }}</span>
                </h2>
                <p class="mx-auto mt-5 max-w-xl text-pretty leading-relaxed text-navy-200" data-aos="fade-up" data-aos-delay="120">
                    {{ $isId
                        ? 'Temukan kategori layanan pengembangan SDM kami — dari pelatihan dan sertifikasi hingga headhunter dan penempatan tenaga kerja.'
                        : 'Explore our human capital service categories — from training and certification to headhunting and workforce placement.' }}
                </p>
            </div>

            @if ($categories->isNotEmpty())
                <div class="relative mt-12" data-fan-wrap data-aos="fade-up">
                    <div class="fan-layout" data-fan>
                        @foreach ($categories as $cat)
                            @php $sub = $cat->short_description ?: ($cat->services_count.' '.($isId ? 'layanan' : 'services')); @endphp
                            <a href="{{ route('services.index') }}#{{ $cat->slug }}" class="fan-card group block ring-1 ring-white/10 transition-[box-shadow,transform] duration-300 hover:ring-2 hover:ring-white/40">
                                {{-- colorful surface --}}
                                @if ($cat->image)
                                    <img src="{{ Storage::url($cat->image) }}" alt="{{ $cat->name }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out-soft group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-transparent to-black/35"></div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br {{ $catColors[$loop->index % count($catColors)] }} transition-transform duration-700 group-hover:scale-110"></div>
                                    <div class="absolute inset-0 grain opacity-25"></div>
                                    <div class="absolute inset-0 bg-gradient-to-b from-black/25 via-transparent to-black/30"></div>
                                @endif

                                {{-- title (top-left) + Mulai pill (top-right) --}}
                                <div class="absolute inset-x-4 top-4 flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h3 class="truncate font-sans text-2xl font-black leading-none text-white drop-shadow-md">{{ $cat->name }}</h3>
                                        <p class="mt-1.5 truncate text-xs font-medium text-white/85">{{ $sub }}</p>
                                    </div>
                                    <span class="hidden shrink-0 items-center gap-1.5 rounded-full bg-white px-3 py-1.5 text-[11px] font-semibold text-navy shadow-md transition group-hover:flex sm:flex">
                                        {{ $isId ? 'Mulai' : 'Start' }}
                                        <span class="grid h-4 w-4 place-items-center rounded-full bg-gold text-ink">
                                            <svg class="h-2.5 w-2.5" viewBox="0 0 16 16" fill="none"><path d="M4 12 12 4M6 4h6v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-10 flex flex-col items-center gap-6">
                        @if ($categories->count() > 7)
                            <div class="flex items-center gap-4">
                                <button data-fan-prev class="grid h-11 w-11 place-items-center rounded-full border border-white/20 text-white transition hover:border-white hover:bg-white/10" aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <div class="flex items-center gap-2" data-fan-dots>
                                    @foreach ($categories as $cat)<span class="fan-dot"></span>@endforeach
                                </div>
                                <button data-fan-next class="grid h-11 w-11 place-items-center rounded-full border border-white/20 text-white transition hover:border-white hover:bg-white/10" aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        @endif
                        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-6 py-3 text-sm font-medium text-white transition hover:border-white hover:bg-white hover:text-navy">
                            {{ $isId ? 'Lihat Semua Kategori' : 'View All Categories' }}
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                    </div>
                </div>
            @else
                <p class="mt-14 text-center text-navy-300">{{ $isId ? 'Belum ada kategori layanan.' : 'No service categories yet.' }}</p>
            @endif
        </div>
    </section>

    {{-- ===================== LATEST SERVICES ===================== --}}
    @if ($latestServices->isNotEmpty())
        <section class="section-sm bg-mist">
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
                                <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex w-full flex-col overflow-hidden">
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

    {{-- ===================== WHY CHOOSE US ===================== --}}
    <section class="section bg-mist">
        <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">
            {{-- Left: editorial heading --}}
            <div class="lg:col-span-5" data-aos="fade-up">
                <p class="eyebrow mb-5"><span class="rule-gold mr-3"></span>{{ $isId ? 'Mengapa Kami' : 'Why Us' }}</p>
                <h2 class="text-display-lg font-semibold leading-[1.05] text-navy text-balance">
                    {{ $isId ? 'Mengapa memilih layanan' : 'Why choose' }}
                    <span class="text-navy-400">PT Delta Tiga Enam</span>{{ $isId ? '?' : '?' }}
                </h2>
                <p class="mt-7 max-w-md text-lg leading-relaxed text-navy-500 text-pretty">
                    {{ $isId
                        ? 'Kami memadukan keahlian praktisi, pendekatan yang dipersonalisasi, dan komitmen pada hasil nyata — memastikan setiap pelatihan benar-benar meningkatkan kompetensi dan nilai organisasi Anda.'
                        : 'We combine practitioner expertise, a personalized approach, and a commitment to real results — ensuring every program genuinely elevates your competencies and organizational value.' }}
                </p>
                <a href="{{ route('services.index') }}" class="btn-primary mt-9">{{ $isId ? 'Lihat Layanan Kami' : 'Explore Our Services' }}</a>
            </div>

            {{-- Right: reasons grid --}}
            <div class="lg:col-span-7">
                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- 01 --}}
                    <div class="card card-hover p-7" data-aos="fade-up" data-aos-delay="60">
                        <div class="flex items-center justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy text-gold">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M3 8l9-4 9 4-9 4-9-4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M7 10.5V15c0 1.1 2.2 2 5 2s5-.9 5-2v-4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                            </span>
                            <span class="font-mono text-xs text-navy-200">01</span>
                        </div>
                        <h3 class="mt-6 font-display text-lg font-semibold text-navy">{{ $isId ? 'Dibimbing Praktisi Ahli' : 'Guided by Expert Practitioners' }}</h3>
                        <p class="mt-2 text-pretty text-sm leading-relaxed text-navy-500">{{ $isId ? 'Belajar langsung dari mentor berpengalaman yang memahami tantangan dunia kerja nyata.' : 'Learn directly from seasoned mentors who understand real-world challenges.' }}</p>
                    </div>

                    {{-- 02 --}}
                    <div class="card card-hover p-7" data-aos="fade-up" data-aos-delay="120">
                        <div class="flex items-center justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy text-gold">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.2 5.4L20 9.6l-4 3.9L17 20l-5-3-5 3 1-6.5-4-3.9 5.8-1.2z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="font-mono text-xs text-navy-200">02</span>
                        </div>
                        <h3 class="mt-6 font-display text-lg font-semibold text-navy">{{ $isId ? 'Program yang Dipersonalisasi' : 'Personalized Programs' }}</h3>
                        <p class="mt-2 text-pretty text-sm leading-relaxed text-navy-500">{{ $isId ? 'Kurikulum dan jadwal disesuaikan sepenuhnya dengan kebutuhan serta tujuan Anda.' : 'Curriculum and schedule fully tailored to your needs and goals.' }}</p>
                    </div>

                    {{-- 03 --}}
                    <div class="card card-hover p-7" data-aos="fade-up" data-aos-delay="180">
                        <div class="flex items-center justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy text-gold">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="9" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M9 13.5L8 21l4-2 4 2-1-7.5" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="font-mono text-xs text-navy-200">03</span>
                        </div>
                        <h3 class="mt-6 font-display text-lg font-semibold text-navy">{{ $isId ? 'Sertifikasi Diakui Industri' : 'Industry-Recognized Certification' }}</h3>
                        <p class="mt-2 text-pretty text-sm leading-relaxed text-navy-500">{{ $isId ? 'Tingkatkan kredibilitas dengan sertifikasi kompetensi yang diakui secara profesional.' : 'Boost credibility with professionally recognized competency certification.' }}</p>
                    </div>

                    {{-- 04 --}}
                    <div class="card card-hover p-7" data-aos="fade-up" data-aos-delay="240">
                        <div class="flex items-center justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy text-gold">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.4"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M14.2 9.8L18 6M9.8 14.2L6 18M14.2 14.2L18 18M9.8 9.8L6 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                            </span>
                            <span class="font-mono text-xs text-navy-200">04</span>
                        </div>
                        <h3 class="mt-6 font-display text-lg font-semibold text-navy">{{ $isId ? 'Pendampingan Menyeluruh' : 'End-to-End Support' }}</h3>
                        <p class="mt-2 text-pretty text-sm leading-relaxed text-navy-500">{{ $isId ? 'Didampingi dari analisis kebutuhan, pelaksanaan, hingga evaluasi hasil.' : 'Supported from needs analysis and delivery through to outcome evaluation.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TESTIMONIALS ===================== --}}
    <x-testimonial-columns :testimonials="$testimonials" />

    {{-- ===================== BLOG ===================== --}}
    @if ($posts->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $isId ? 'Blog Terbaru' : 'Latest Blog' }}</p>
                        <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $isId ? 'Wawasan & artikel terbaru kami' : 'Our latest insights & articles' }}</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="link-underline shrink-0 font-medium" data-aos="fade-up">{{ __('site.cta.view_all') }}</a>
                </div>

                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group block" data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                            <article data-tilt class="card-3d flex h-full flex-col">
                                <div class="relative aspect-[16/10] overflow-hidden bg-navy-100">
                                    @if ($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy"
                                             class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="absolute inset-0 aurora opacity-60"></div>
                                    @endif
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/45 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                                </div>
                                <div class="flex flex-1 flex-col p-6">
                                    <p class="eyebrow-muted">{{ optional($post->category)->name }} · {{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                                    <h3 class="mt-3 font-display text-xl font-semibold leading-snug text-navy transition-colors group-hover:text-navy-500">{{ $post->title }}</h3>
                                    @if ($post->excerpt)<p class="mt-2 line-clamp-2 text-sm text-navy-500">{{ $post->excerpt }}</p>@endif
                                    <span class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-navy">
                                        {{ $isId ? 'Baca artikel' : 'Read article' }}
                                        <span class="grid h-8 w-8 place-items-center rounded-full border border-navy-200 transition-all duration-300 group-hover:border-gold group-hover:bg-gold group-hover:text-ink">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    </span>
                                </div>
                            </article>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== PARTNERS & CLIENTS ===================== --}}
    @if ($partners->isNotEmpty() || $clients->isNotEmpty())
        <section class="section">
            {{-- MITRA KAMI --}}
            @if ($partners->isNotEmpty())
                <div class="container text-center" data-aos="fade-up">
                    <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $isId ? 'Mitra Kami' : 'Our Partners' }}</p>
                    <h2 class="mx-auto max-w-xl text-display-lg font-semibold text-navy text-balance">{{ $isId ? 'Mitra Yang Mendukung Dan Berkolaborasi Dengan Kami.' : 'Partners who support and collaborate with us.' }}</h2>
                </div>
                <div class="group mask-fade-x relative mt-12 overflow-hidden" data-aos="fade-up">
                    <div class="flex w-max animate-marquee items-center gap-6 pr-6 group-hover:[animation-play-state:paused]">
                        @foreach ($partners->concat($partners) as $partner)
                            <div class="flex h-24 w-48 shrink-0 items-center justify-center rounded-2xl border border-navy-100 bg-mist px-6 opacity-70 grayscale transition duration-300 hover:-translate-y-1 hover:border-navy-200 hover:opacity-100 hover:grayscale-0">
                                @if ($partner->logo)
                                    <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-12 w-auto object-contain">
                                @else
                                    <span class="text-center font-display text-lg font-semibold text-navy-300">{{ $partner->name }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- KLIEN KAMI — blue panel: text + form (left), 2 vertical logo columns (right) --}}
            @if ($clients->isNotEmpty())
                @php $clientCol1 = $clients->concat($clients); $clientCol2 = $clients->reverse()->concat($clients->reverse()); @endphp
                <div class="container mt-20" data-aos="fade-up">
                    <div class="relative overflow-hidden rounded-[2.5rem] bg-navy-950 text-white shadow-lift">
                        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
                        <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                        <div class="relative grid items-center gap-6 lg:grid-cols-2">
                            {{-- Left: heading + description + form --}}
                            <div class="px-8 py-12 md:px-12 md:py-16">
                                <h2 class="font-sans text-4xl font-extrabold leading-tight text-balance md:text-5xl">{{ $isId ? 'Klien yang telah menggunakan layanan kami' : 'Clients who have used our services' }}</h2>
                                <p class="mt-5 max-w-md text-pretty leading-relaxed text-white/90">{{ $isId ? 'Berbagai organisasi mempercayakan kebutuhan pengembangan SDM mereka kepada kami. Tinggalkan kontak Anda untuk menjadi bagian berikutnya.' : 'Organizations across industries trust us with their human capital development. Leave your details to be the next.' }}</p>
                                <form action="#" onsubmit="event.preventDefault()" class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                                    <input type="text" name="name" placeholder="{{ $isId ? 'Masukkan Nama Anda' : 'Enter Your Name' }}" class="min-w-0 flex-1 rounded-full bg-white/95 px-5 py-3.5 text-sm text-navy placeholder:text-navy-300 focus:outline-none focus:ring-2 focus:ring-white">
                                    <input type="email" name="email" placeholder="{{ $isId ? 'Masukkan Email Anda' : 'Enter Your Email' }}" class="min-w-0 flex-1 rounded-full bg-white/95 px-5 py-3.5 text-sm text-navy placeholder:text-navy-300 focus:outline-none focus:ring-2 focus:ring-white">
                                    <button type="submit" class="rounded-full bg-gradient-to-r from-cyan-500 to-sky-500 px-7 py-3.5 text-sm font-bold uppercase tracking-wide text-white shadow-[0_8px_30px_-12px_rgba(28,125,224,0.6)] transition duration-200 hover:-translate-y-0.5 hover:from-cyan-400 hover:to-sky-400">{{ $isId ? 'Kirim' : 'Send' }}</button>
                                </form>
                            </div>

                            {{-- Right: 2 vertical logo columns (marquee up & down) --}}
                            <div class="mask-fade-y relative grid max-h-[24rem] grid-cols-2 gap-4 overflow-hidden px-6 py-8 md:max-h-[26rem] md:pr-10">
                                <div class="marquee-col flex flex-col gap-4" style="--dur: 22s">
                                    @foreach ($clientCol1 as $client)
                                        <div class="flex h-20 shrink-0 items-center justify-center rounded-2xl bg-white px-5 opacity-90 grayscale transition duration-300 hover:scale-105 hover:opacity-100 hover:grayscale-0">
                                            @if ($client->logo)
                                                <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy" class="max-h-10 w-auto object-contain">
                                            @else
                                                <span class="text-center font-display text-sm font-semibold text-navy">{{ $client->name }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="marquee-col flex flex-col gap-4 [animation-direction:reverse]" style="--dur: 26s">
                                    @foreach ($clientCol2 as $client)
                                        <div class="flex h-20 shrink-0 items-center justify-center rounded-2xl bg-white px-5 opacity-90 grayscale transition duration-300 hover:scale-105 hover:opacity-100 hover:grayscale-0">
                                            @if ($client->logo)
                                                <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy" class="max-h-10 w-auto object-contain">
                                            @else
                                                <span class="text-center font-display text-sm font-semibold text-navy">{{ $client->name }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ===================== CTA ===================== --}}
    <section class="relative overflow-hidden bg-navy-950 py-20 text-center text-white md:py-28">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
        <div class="container relative">
            <p class="eyebrow mb-5">{{ $isId ? 'Mulai langkah berikutnya' : 'Take the next step' }}</p>
            <h2 class="mx-auto max-w-3xl text-display-lg font-semibold text-balance" data-aos="fade-up">
                {{ $isId ? 'Siap meningkatkan kompetensi tim Anda?' : 'Ready to elevate your team’s competencies?' }}
            </h2>
            <div class="mt-9 flex flex-wrap justify-center gap-4" data-aos="fade-up" data-aos-delay="80">
                <a href="{{ route('partnership.index') }}" class="btn-gold">{{ __('site.cta.partner') }}</a>
                <a href="{{ route('contact.index') }}" class="btn-ghost-light">{{ __('site.cta.consult') }}</a>
            </div>
        </div>
    </section>

</x-layout>
