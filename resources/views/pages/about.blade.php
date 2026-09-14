@php
    $isId = app()->getLocale() === 'id';
    $aboutParas = array_filter(array_map('trim', preg_split('/\n\s*\n/', trim((string) $about)))) ?: [$about];


    // Founder (structural content — editable here in the view).
    $founderRole = $isId ? 'CEO Delta Tiga Enam' : 'CEO of Delta Tiga Enam';
    $founderName = 'Dani Taupan Ramdani, S.T., M.M., Ph.D (Cand.)';
    $founderQuote = $isId
        ? 'Membangun bangsa dimulai dari membangun manusianya. Di Delta Tiga Enam, kami berkomitmen menjadi jembatan bagi talenta Indonesia menuju standar dunia.'
        : 'Building a nation begins with building its people. At Delta Tiga Enam, we are committed to bridging Indonesian talent toward world-class standards.';
    $founderBio = $isId
        ? 'Dengan pengalaman lebih dari 20 tahun di bidang Human Resources dan Manajemen Strategis, Dani telah membantu berbagai perusahaan BUMN dan swasta dalam melakukan restrukturisasi organisasi.'
        : 'With over 20 years of experience in Human Resources and Strategic Management, Dani has helped state-owned and private companies restructure their organizations.';
@endphp

<x-layout :title="$isId ? 'Tentang Kami' : 'About Us'" :description="$tagline">
    <x-page-header
        :eyebrow="'PT Delta Tiga Enam'"
        :title="$isId ? 'Tentang Kami' : 'About Us'"
        placement="about"
        image="photo-1522071820081-009f0129c71c" />

    {{-- ===================== COMPANY PROFILE ===================== --}}
    <section class="section bg-white">
        <div class="container grid gap-10 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-4" data-aos="fade-up">
                <h2 class="font-display text-3xl leading-tight text-navy md:text-4xl">{{ $isId ? 'Profil Perusahaan' : 'Company Profile' }}</h2>
                <span class="mt-5 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
            </div>
            <div class="space-y-5 leading-relaxed text-slate-700 lg:col-span-8" data-aos="fade-up" data-aos-delay="80">
                @foreach ($aboutParas as $para)
                    {{-- The opening paragraph carries the pitch, so it gets a lead size instead of
                         disappearing into an undifferentiated wall of body copy.
                         Justified only from sm up: in a narrow column it opens rivers of space. --}}
                    <p class="text-left [hyphens:auto] sm:text-justify {{ $loop->first ? 'text-lg leading-relaxed text-navy md:text-xl' : '' }}">{{ $para }}</p>
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div class="container mt-16">
            {{-- Hairline dividers, matching the hero stats ledger. Every figure is navy: the
                 first one used to be gold for no reason other than being first. --}}
            <div class="grid grid-cols-2 gap-y-10 sm:grid-cols-4 sm:gap-y-0 sm:divide-x sm:divide-navy-100" data-aos="fade-up">
                @foreach ($stats as $stat)
                    @php
                        $num = (int) filter_var($stat['value'], FILTER_SANITIZE_NUMBER_INT);
                        $suffix = str_replace((string) $num, '', (string) $stat['value']);
                    @endphp
                    <div class="group px-2 text-center sm:px-5">
                        <p class="font-display text-4xl text-navy transition-colors duration-300 group-hover:text-sky-600 md:text-5xl"
                           data-counter="{{ $num }}" data-counter-suffix="{{ $suffix }}">0{{ $suffix }}</p>
                        <span class="mx-auto mt-3 block h-px w-8 bg-gold-soft/70 transition-all duration-300 group-hover:w-12" aria-hidden="true"></span>
                        <p class="mt-3 font-mono text-[10px] uppercase tracking-normal text-slate-500 md:text-[11px]">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== COMPANY PROFILE (PDF DOWNLOAD) ===================== --}}
    @php
        $cpUrl = asset('documents/company-profile.pdf');
        $cpPath = public_path('documents/company-profile.pdf');
        $cpSize = is_file($cpPath) ? number_format(filesize($cpPath) / 1048576, 1).' MB · PDF' : 'PDF';
    @endphp
    <section class="bg-white pb-14 md:pb-20 lg:pb-24">
        <div class="container">
            {{-- shadow-card at rest, not shadow-lift: a 1px border paired with a 48px-blur
                 shadow is the "ghost card" look. One or the other, and hover-shadow-2xl was
                 a Tailwind default that sits outside this site's shadow scale.
                 Radius 2xl to match every other card. --}}
            <div class="group relative mx-auto max-w-4xl overflow-hidden rounded-2xl border border-navy-100 bg-gradient-to-br from-white via-white to-mist p-7 shadow-card transition-shadow duration-300 hover:shadow-lift md:p-10" data-aos="fade-up">
                {{-- soft ambient glows --}}
                <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gold/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -left-16 -bottom-16 h-44 w-44 rounded-full bg-sky-400/10 blur-3xl"></div>

                <div class="relative flex flex-col items-start gap-7 md:flex-row md:items-center md:justify-between md:gap-10">
                    <div class="flex items-center gap-5">
                        {{-- Stylised PDF page (tilts + lifts on hover) --}}
                        <div class="relative shrink-0">
                            <div class="grid h-24 w-[4.5rem] place-items-center rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 shadow-lift ring-1 ring-white/10 transition-transform duration-300 ease-out-soft group-hover:-translate-y-1 group-hover:-rotate-3">
                                <svg class="h-9 w-9 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                            </div>
                            <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 rounded-md bg-gold px-2.5 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider text-navy-950 shadow-gold">PDF</span>
                        </div>
                        <div>
                            <p class="mb-2 font-mono text-[11px] uppercase tracking-normal text-gold-deep">{{ $isId ? 'Dokumen Resmi' : 'Official Document' }}</p>
                            <h2 class="font-display text-2xl font-bold leading-tight text-navy md:text-3xl">{{ $isId ? 'Profil Perusahaan' : 'Company Profile' }}</h2>
                            <p class="mt-2.5 max-w-md text-pretty text-sm leading-relaxed text-slate-600">{{ $isId ? 'Pelajari layanan, nilai, dan rekam jejak PT Delta Tiga Enam secara lengkap dalam satu dokumen.' : 'Explore the services, values, and full track record of PT Delta Tiga Enam in one document.' }}</p>
                            <p class="mt-3 inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-wider text-slate-400">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                {{ $cpSize }}
                            </p>
                        </div>
                    </div>

                    <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row md:w-auto">
                        <a href="{{ $cpUrl }}" target="_blank" rel="noopener" class="btn-ghost justify-center">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            {{ $isId ? 'Lihat' : 'View' }}
                        </a>
                        <a href="{{ $cpUrl }}" download class="btn-blue justify-center">
                            {{ $isId ? 'Unduh PDF' : 'Download PDF' }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== VISION & MISSION ===================== --}}
    <section class="relative overflow-hidden py-14 text-white md:py-20">
        {{-- Background image + overlay --}}
        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1920&q=80" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        {{-- Same gradient treatment as the "Keunggulan" band — image visible through the middle --}}
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/75 via-navy-950/45 to-navy-950/88"></div>
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-20"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>

        <div class="container relative">
            <div class="mb-10 max-w-2xl" data-aos="fade-up">
                <h2 class="font-display text-3xl md:text-4xl">{{ $isId ? 'Visi & Misi' : 'Vision & Mission' }}</h2>
            </div>

            {{-- Asymmetric 2fr/3fr rather than two matching glass boxes: the vision is one
                 statement and the mission is a list, so giving them identical containers
                 flattened the difference and buried the vision at body size. --}}
            <div class="grid items-start gap-10 lg:grid-cols-5 lg:gap-14">

                {{-- Vision: set as the statement it is, not as body copy in a card. --}}
                <div class="lg:col-span-2" data-aos="fade-up">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold-soft">{{ $isId ? 'Visi' : 'Vision' }}</p>
                    <span class="mt-4 block h-px w-12 bg-gold-soft/60" aria-hidden="true"></span>
                    <blockquote class="mt-6">
                        <p class="font-display text-xl font-semibold leading-[1.45] text-white text-pretty md:text-2xl">
                            &ldquo;{{ $vision }}&rdquo;
                        </p>
                    </blockquote>
                </div>

                {{-- Mission: numbered so the count reads at a glance; the em-dash bullets gave
                     no sense of how many commitments there are. --}}
                <div class="lg:col-span-3" data-aos="fade-up" data-aos-delay="80">
                    <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-gold-soft">{{ $isId ? 'Misi' : 'Mission' }}</p>
                    <span class="mt-4 block h-px w-12 bg-gold-soft/60" aria-hidden="true"></span>

                    @if ($missions->isNotEmpty())
                        <ol class="mt-6 divide-y divide-white/10 border-y border-white/10">
                            @foreach ($missions as $mission)
                                <li class="group flex items-start gap-4 py-4 transition-colors duration-300 md:gap-5">
                                    <span class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-white/[0.07] font-mono text-[11px] font-bold tabular-nums text-gold-soft ring-1 ring-white/10 transition-colors duration-300 group-hover:bg-white/[0.14]">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    {{-- Left-aligned on phones: justified text in a narrow column opens
                                         rivers of white space between words. --}}
                                    <p class="text-[15px] leading-relaxed text-white/90 text-left sm:text-justify [hyphens:auto]">{{ $mission->content }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOUNDER ===================== --}}
    <section class="relative overflow-hidden bg-navy-anim py-20 text-white md:py-24 lg:flex lg:min-h-[88vh] lg:items-center">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-35"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-30"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        <div class="container relative">
            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
                {{-- Portrait — substantial, balanced, with a gold backing card jutting out top-left --}}
                <div class="relative mx-auto w-full max-w-md lg:col-span-5 lg:mx-0" data-aos="fade-right">
                    {{-- Gold backing card — offset behind the photo so it peeks out on the top & left --}}
                    <div class="pointer-events-none absolute -left-4 -top-4 h-full w-full rounded-3xl bg-gradient-to-br from-gold to-gold-soft md:-left-6 md:-top-6"></div>
                    {{-- Photo (front layer) --}}
                    <div class="relative aspect-[4/5] overflow-hidden rounded-3xl border border-white/10 shadow-lift">
                        <img src="{{ asset('images/pendiri.png') }}" alt="{{ $founderName }}" class="h-full w-full object-cover object-top">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/55 via-transparent to-transparent"></div>
                    </div>
                </div>

                {{-- Bio --}}
                <div class="lg:col-span-7" data-aos="fade-left" data-aos-delay="100">
                    <p class="eyebrow text-gold-soft"><span class="rule-gold mr-3 from-gold"></span>{{ $isId ? 'Pendiri' : 'Founder' }}</p>

                    <h2 class="mt-5 font-display text-2xl font-bold leading-tight text-white text-balance md:text-3xl">{{ $founderName }}</h2>
                    <p class="mt-2.5 font-mono text-[11px] uppercase tracking-wider text-gold-soft">{{ $founderRole }}</p>

                    {{-- Quote --}}
                    <p class="mt-9 max-w-2xl text-pretty text-xl font-light italic leading-relaxed text-white md:text-2xl">&ldquo;{{ $founderQuote }}&rdquo;</p>
                    <span class="mt-7 block h-0.5 w-16 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                    <p class="mt-7 max-w-xl text-pretty text-[15px] leading-[1.8] text-navy-100/80">{{ $founderBio }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== VALUES ===================== --}}
    <section class="section border-t border-navy-50 bg-paper">
        <div class="container">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <h2 class="font-display text-4xl font-bold text-navy text-balance md:text-5xl">{{ $isId ? 'Nilai Kami' : 'Our Values' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-pretty leading-relaxed text-slate-600">
                    {{ $isId ? 'Prinsip yang memandu cara kami bekerja dan melayani setiap klien.' : 'The principles that guide how we work and serve every client.' }}
                </p>
            </div>

            {{-- Value strip — interactive carousel on mobile (swipe + tappable dots + arrows), 5-up grid on desktop --}}
            <div class="mt-10 md:mt-14" data-aos="fade-up"
                 x-data="{
                    active: 0,
                    n: {{ count($values) }},
                    step() {
                        const t = this.$refs.track, f = t.firstElementChild;
                        if (! f) return t.clientWidth;
                        const gap = parseFloat(getComputedStyle(t).columnGap || '0') || 0;
                        return f.getBoundingClientRect().width + gap;
                    },
                    sync() {
                        const t = this.$refs.track;
                        this.active = Math.min(this.n - 1, Math.max(0, Math.round(t.scrollLeft / this.step())));
                    },
                    go(i) {
                        i = Math.min(this.n - 1, Math.max(0, i));
                        this.$refs.track.scrollTo({ left: this.step() * i, behavior: 'smooth' });
                    },
                 }">
                <div x-ref="track" @scroll.passive="sync()"
                     class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden lg:grid lg:grid-cols-5 lg:gap-0 lg:overflow-hidden lg:rounded-3xl lg:pb-0 lg:shadow-lift">
                @foreach ($values as $i => $value)
                    <div class="group flex w-[80%] min-w-[80%] max-w-[80%] shrink-0 snap-start flex-col overflow-hidden rounded-2xl shadow-card sm:w-[46%] sm:min-w-[46%] sm:max-w-[46%] lg:w-auto lg:min-w-0 lg:max-w-none lg:rounded-none lg:shadow-none">
                        <div class="relative aspect-[4/3] overflow-hidden bg-navy-100 sm:aspect-[4/5] lg:aspect-square">
                            <img src="{{ asset('images/values/'.$value['img'].'.jpg') }}" alt="" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/70 via-navy-950/10 to-transparent"></div>
                            {{-- The acronym letter, outlined so it reads as a mark rather than a caption. --}}
                            <span class="pointer-events-none absolute bottom-3 left-4 font-display text-5xl font-bold leading-none text-transparent transition-all duration-500 ease-out-soft group-hover:text-gold-soft/25 md:text-6xl"
                                  style="-webkit-text-stroke: 1px rgba(237,214,138,.55);" aria-hidden="true">{{ $value['letter'] }}</span>
                        </div>
                        {{-- One navy for all five: the previous odd/even navy-900 / navy-600 zebra
                             carried no meaning, it just striped the strip. --}}
                        <div class="flex flex-1 flex-col justify-center bg-navy-900 px-4 py-6 text-center text-white transition-colors duration-300 group-hover:bg-navy-800 md:px-5 md:py-7">
                            <h3 class="w-full font-display text-base font-bold md:text-lg">{{ $value['title'] }}</h3>
                            <span class="mx-auto mt-2 block h-px w-8 bg-gold-soft/50 transition-all duration-300 group-hover:w-12" aria-hidden="true"></span>
                            <p class="mx-auto mt-2.5 w-full max-w-[34ch] text-[13px] leading-relaxed text-white/85">{{ $value['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
                </div>

                {{-- Mobile controls: prev / dots / next — clear swipe affordance (hidden on desktop grid) --}}
                <div class="mt-6 flex items-center justify-center gap-4 lg:hidden">
                    <button type="button" @click="go(active - 1)" :disabled="active === 0"
                            class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-navy-200 text-navy transition enabled:hover:border-gold enabled:hover:text-gold disabled:opacity-30"
                            aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="flex items-center gap-2">
                        @foreach ($values as $i => $value)
                            <button type="button" @click="go({{ $i }})"
                                    :class="active === {{ $i }} ? 'w-6 bg-navy' : 'w-2.5 bg-navy-200'"
                                    class="h-2.5 rounded-full transition-all duration-300"
                                    aria-label="{{ ($isId ? 'Nilai' : 'Value').' '.($i + 1) }}"></button>
                        @endforeach
                    </div>
                    <button type="button" @click="go(active + 1)" :disabled="active === n - 1"
                            class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-navy-200 text-navy transition enabled:hover:border-gold enabled:hover:text-gold disabled:opacity-30"
                            aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="m6 3 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

</x-layout>
