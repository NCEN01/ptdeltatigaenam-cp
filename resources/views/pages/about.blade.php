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
                    <p class="text-justify [hyphens:auto]">{{ $para }}</p>
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

    {{-- ===================== COMPANY PROFILE (PDF DOWNLOAD) ===================== --}}
    @php
        $cpUrl = asset('documents/company-profile.pdf');
        $cpPath = public_path('documents/company-profile.pdf');
        $cpSize = is_file($cpPath) ? number_format(filesize($cpPath) / 1048576, 1).' MB · PDF' : 'PDF';
    @endphp
    <section class="bg-white pb-14 md:pb-20 lg:pb-24">
        <div class="container">
            <div class="group relative mx-auto max-w-4xl overflow-hidden rounded-3xl border border-navy-100 bg-gradient-to-br from-white via-white to-mist p-7 shadow-lift transition-shadow duration-300 hover:shadow-2xl md:p-10" data-aos="fade-up">
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
            {{-- Heading --}}
            <div class="mx-auto mb-8 max-w-2xl text-center" data-aos="fade-up">
                <p class="eyebrow inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $isId ? 'Arah & Komitmen' : 'Direction & Commitment' }}</p>
                <h2 class="mt-4 font-display text-3xl md:text-4xl">{{ $isId ? 'Visi & Misi' : 'Vision & Mission' }}</h2>
            </div>

            <div class="grid items-stretch gap-5 lg:grid-cols-2">
                {{-- Vision — same glass/blur as the "Portofolio Kami" cards; text sized like the Company Profile body --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5 text-left backdrop-blur-sm md:p-6" data-aos="fade-up">
                    <p class="font-mono text-base uppercase tracking-normal text-gold-soft">{{ $isId ? 'Visi' : 'Vision' }}</p>
                    <p class="mt-5 font-display text-base leading-relaxed text-white text-justify [hyphens:auto]">&ldquo;{{ $vision }}&rdquo;</p>
                </div>

                {{-- Mission — numbered list, no icon --}}
                <div class="rounded-2xl border border-white/10 bg-white/[0.06] p-5 backdrop-blur-sm md:p-6" data-aos="fade-up" data-aos-delay="80">
                    <p class="font-mono text-base uppercase tracking-normal text-gold-soft">{{ $isId ? 'Misi' : 'Mission' }}</p>
                    @if ($missions->isNotEmpty())
                        <ul class="mt-5 space-y-4">
                            @foreach ($missions as $mission)
                                <li class="flex items-start gap-3 border-b border-white/10 pb-4 last:border-0 last:pb-0">
                                    <span class="shrink-0 text-base leading-relaxed text-gold-soft" aria-hidden="true">—</span>
                                    <p class="text-base leading-relaxed text-white/90 text-justify [hyphens:auto]">{{ $mission->content }}</p>
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
                <p class="eyebrow inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $isId ? 'Prinsip Kami' : 'What We Stand For' }}</p>
                <h2 class="mt-4 font-display text-4xl font-bold text-navy text-balance md:text-5xl">{{ $isId ? 'Nilai Kami' : 'Our Values' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-pretty leading-relaxed text-slate-600">
                    {{ $isId ? 'Prinsip yang memandu cara kami bekerja dan melayani setiap klien.' : 'The principles that guide how we work and serve every client.' }}
                </p>
            </div>

            {{-- Value strip — inside the container so left/right padding matches the other sections; swipeable on mobile, 5-up on desktop --}}
            <div class="mt-10 flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden md:mt-14 lg:grid lg:grid-cols-5 lg:gap-0 lg:overflow-hidden lg:rounded-3xl lg:pb-0 lg:shadow-lift" data-aos="fade-up">
            @foreach ($values as $i => $value)
                <div class="group flex min-w-[72%] shrink-0 snap-start flex-col overflow-hidden rounded-2xl shadow-card sm:min-w-[44%] lg:min-w-0 lg:rounded-none lg:shadow-none">
                    <div class="relative aspect-[4/5] overflow-hidden bg-navy-100 lg:aspect-square">
                        <img src="{{ asset('images/values/'.$value['img'].'.jpg') }}" alt="" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                    <div class="flex flex-1 flex-col items-center px-4 py-6 text-center text-white {{ $i % 2 === 0 ? 'bg-[#0e1f4d]' : 'bg-[#3a55a8]' }} md:px-5 md:py-7">
                        <h3 class="font-display text-base font-bold italic md:text-lg">{{ $value['title'] }}</h3>
                        <p class="mt-2.5 text-[13px] leading-relaxed text-white/85">{{ $value['desc'] }}</p>
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </section>

</x-layout>
