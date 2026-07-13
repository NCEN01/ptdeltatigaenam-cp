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

            <div class="grid items-stretch gap-5 lg:grid-cols-2">
                {{-- Vision — same glass/blur as the "Portofolio Kami" cards; text sized like the Company Profile body --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5 text-left backdrop-blur-sm md:p-6" data-aos="fade-up">
                    <p class="font-mono text-base uppercase tracking-normal text-gold-soft">{{ $isId ? 'Visi' : 'Vision' }}</p>
                    <p class="mt-5 font-display text-base leading-relaxed text-white text-pretty">&ldquo;{{ $vision }}&rdquo;</p>
                </div>

                {{-- Mission — numbered list, no icon --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5 backdrop-blur-sm md:p-6" data-aos="fade-up" data-aos-delay="80">
                    <p class="font-mono text-base uppercase tracking-normal text-gold-soft">{{ $isId ? 'Misi' : 'Mission' }}</p>
                    @if ($missions->isNotEmpty())
                        <ul class="mt-5 space-y-4">
                            @foreach ($missions as $mission)
                                <li class="flex items-start gap-3 border-b border-white/10 pb-4 last:border-0 last:pb-0">
                                    <span class="shrink-0 text-base leading-relaxed text-gold-soft" aria-hidden="true">—</span>
                                    <p class="text-base leading-relaxed text-white/90">{{ $mission->content }}</p>
                                </li>
                            @endforeach
                        </ul>
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
                    <div class="absolute -bottom-4 -right-4 grid h-14 w-14 place-items-center rounded-2xl bg-navy-950 font-display text-4xl leading-none text-gold shadow-lift ring-1 ring-gold/40">&rdquo;</div>
                </div>

                {{-- Bio --}}
                <div class="lg:col-span-7" data-aos="fade-left" data-aos-delay="100">
                    <p class="eyebrow text-gold-soft"><span class="rule-gold mr-3 from-gold"></span>{{ $isId ? 'Pendiri' : 'Founder' }}</p>
                    <h2 class="mt-5 font-display text-4xl font-bold leading-[1.05] text-balance md:text-5xl">{{ $founderName }}</h2>
                    <p class="mt-3 font-mono text-xs uppercase tracking-normal text-navy-muted">{{ $founderRole }}</p>
                    <p class="mt-8 text-pretty text-xl italic leading-relaxed text-white/90 md:text-2xl">&ldquo;{{ $founderQuote }}&rdquo;</p>
                    <span class="mt-8 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                    <p class="mt-8 max-w-2xl text-pretty text-base leading-relaxed text-navy-100/85">{{ $founderBio }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== VALUES ===================== --}}
    <section class="section border-t border-navy-50 bg-paper">
        <div class="container">
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <p class="eyebrow inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $isId ? 'Prinsip Kami' : 'What We Stand For' }}</p>
                <h2 class="mt-4 font-display text-4xl font-bold text-navy text-balance md:text-5xl">{{ $isId ? 'Nilai Kami' : 'Our Values' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-pretty leading-relaxed text-slate-600">
                    {{ $isId ? 'Prinsip yang memandu cara kami bekerja dan melayani setiap klien.' : 'The principles that guide how we work and serve every client.' }}
                </p>
            </div>
            <div class="mx-auto mt-14 grid max-w-6xl gap-5 sm:grid-cols-2 md:mt-16 lg:grid-cols-4">
                @foreach ($values as $value)
                    <div class="group flex flex-col rounded-2xl border border-navy-100 bg-white p-6 transition-all duration-300 hover:-translate-y-1 hover:border-navy-200 hover:shadow-lift md:p-7" data-aos="fade-up" data-aos-delay="{{ $loop->index * 70 }}">
                        <span class="block h-0.5 w-8 rounded-full bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:w-12"></span>
                        <h3 class="mt-5 font-display text-lg font-semibold text-navy">{{ $value['title'] }}</h3>
                        <p class="mt-2.5 flex-1 text-pretty text-sm leading-relaxed text-slate-600">{{ $value['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layout>
