@php
    $isId = app()->getLocale() === 'id';
    $aboutParas = array_filter(array_map('trim', preg_split('/\n\s*\n/', trim((string) $about)))) ?: [$about];


    // Founder (structural content — editable here in the view).
    $founderRole = $isId ? 'Direktur Utama' : 'Managing Director';
    $founderName = 'Dani Taupan';
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
                    <p class="text-pretty">{{ $para }}</p>
                @endforeach
            </div>
        </div>

        {{-- Stats --}}
        <div class="container mt-16">
            <div class="grid grid-cols-2 gap-y-10 sm:grid-cols-4" data-aos="fade-up">
                @foreach ($stats as $stat)
                    @php
                        $num = (int) filter_var($stat['value'], FILTER_SANITIZE_NUMBER_INT);
                        $suffix = str_replace((string) $num, '', (string) $stat['value']);
                    @endphp
                    <div class="px-2 text-center">
                        <p class="font-display text-4xl md:text-5xl {{ $loop->first ? 'text-gold-deep' : 'text-navy' }}"
                           data-counter="{{ $num }}" data-counter-suffix="{{ $suffix }}">0{{ $suffix }}</p>
                        <p class="mt-2 font-mono text-[10px] uppercase tracking-normal text-slate-500 md:text-[11px]">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
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
            {{-- Heading --}}
            <div class="mx-auto mb-8 max-w-2xl text-center" data-aos="fade-up">
                <p class="eyebrow inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $isId ? 'Arah & Komitmen' : 'Direction & Commitment' }}</p>
                <h2 class="mt-4 font-display text-3xl md:text-4xl">{{ $isId ? 'Visi & Misi' : 'Vision & Mission' }}</h2>
            </div>

            <div class="grid items-stretch gap-6 lg:grid-cols-2">
                {{-- Vision — left/top aligned --}}
                <div class="rounded-3xl border border-white/10 bg-navy-950/45 p-6 text-left backdrop-blur-md md:p-8" data-aos="fade-up">
                    <p class="font-mono text-base uppercase tracking-normal text-gold-soft md:text-lg">{{ $isId ? 'Visi' : 'Vision' }}</p>
                    <p class="mt-5 font-display text-base leading-relaxed text-white text-pretty md:text-lg">&ldquo;{{ $vision }}&rdquo;</p>
                </div>

                {{-- Mission — numbered list, no icon --}}
                <div class="rounded-3xl border border-white/10 bg-navy-950/45 p-6 backdrop-blur-md md:p-8" data-aos="fade-up" data-aos-delay="80">
                    <p class="font-mono text-base uppercase tracking-normal text-gold-soft md:text-lg">{{ $isId ? 'Misi' : 'Mission' }}</p>
                    @if ($missions->isNotEmpty())
                        <ul class="mt-5 space-y-4">
                            @foreach ($missions as $mission)
                                <li class="flex items-start gap-3 border-b border-white/10 pb-4 last:border-0 last:pb-0">
                                    <span class="shrink-0 text-base leading-relaxed text-gold-soft md:text-lg" aria-hidden="true">—</span>
                                    <p class="text-base leading-relaxed text-white/90 md:text-lg">{{ $mission->content }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOUNDER ===================== --}}
    <section class="relative overflow-hidden bg-navy-anim py-20 text-white md:py-28">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
        <div class="container relative">
            <div class="text-center" data-aos="fade-up">
                <h2 class="font-display text-3xl md:text-4xl">{{ $isId ? 'Pendiri' : 'Founder' }}</h2>
                <span class="mx-auto mt-4 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-16">
                {{-- Portrait --}}
                <div class="relative mx-auto w-full max-w-md" data-aos="fade-up">
                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-lift">
                        <img src="{{ asset('images/pendiri.png') }}" alt="{{ $founderName }}" class="h-full w-full object-cover object-top">
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-navy-950/70 to-transparent"></div>
                    </div>
                    <div class="absolute -bottom-4 right-6 grid h-16 w-16 place-items-center rounded-xl bg-gold font-display text-4xl leading-none text-navy-950 shadow-lift">”</div>
                </div>

                {{-- Bio --}}
                <div data-aos="fade-up" data-aos-delay="100">
                    <p class="font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $founderRole }}</p>
                    <h3 class="mt-2 font-display text-3xl md:text-4xl">{{ $founderName }}</h3>
                    <p class="mt-6 text-pretty text-lg italic leading-relaxed text-navy-100">"{{ $founderQuote }}"</p>
                    <p class="mt-6 max-w-xl text-pretty text-sm leading-relaxed text-slate-400">{{ $founderBio }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== VALUES ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-white">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="font-display text-3xl text-navy md:text-4xl">{{ $isId ? 'Nilai Kami' : 'Our Values' }}</h2>
                <span class="mx-auto mt-4 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
            </div>
            <div class="mt-14 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($values as $value)
                    <div class="group border-l-2 border-gold pl-5 transition-colors duration-300 hover:border-sky-500" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                        <h3 class="font-display text-lg text-navy">{{ $value['title'] }}</h3>
                        <p class="mt-2.5 text-pretty text-sm leading-relaxed text-slate-600">{{ $value['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== OFFICES ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-neutral-50">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <h2 class="font-display text-3xl text-navy md:text-4xl">{{ $isId ? 'Kantor Kami' : 'Our Offices' }}</h2>
                <span class="mx-auto mt-4 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($offices as $office)
                    <div class="group flex flex-col rounded-2xl border border-navy-100 bg-white p-7 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-navy text-gold transition-colors duration-300 group-hover:bg-sky-600 group-hover:text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M10 21v-4h4v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <h3 class="mt-5 font-display text-xl text-navy">{{ $office['name'] }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-600">{{ $office['address'] }}</p>
                        <p class="mt-5 flex items-center gap-2 border-t border-navy-100 pt-4 text-sm font-medium text-navy">
                            <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><path d="M5 4h4l2 5-3 2a12 12 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                            {{ $office['phone'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layout>
