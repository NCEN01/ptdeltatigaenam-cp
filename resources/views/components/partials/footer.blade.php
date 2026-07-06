@php
    use App\Models\Setting;
    use App\Models\OfficeLocation;

    $id = app()->getLocale() === 'id';
    $email = 'info@deltatigaenam.com';
    $website = 'www.deltatigaenam.com';
    $linkedin = Setting::get('linkedin_url', 'https://linkedin.com/company/deltatigaenam');
    $year = now()->year;

    $offices = OfficeLocation::where('is_active', true)->orderBy('sort_order')->get();

    $nav = [
        'about' => __('site.nav.about'),
        'services.index' => __('site.nav.services'),
        'portfolio.index' => __('site.nav.portfolio'),
        'blog.index' => __('site.nav.blog'),
        'partnership.index' => __('site.nav.partnership'),
        'contact.index' => __('site.nav.contact'),
    ];

    // Contextual CTA — the footer card's copy + button adapt to the current page.
    $rn = request()->route()?->getName() ?? '';
    $ctaKey = match (true) {
        str_contains($rn, 'services') => 'services',
        str_contains($rn, 'certificate') => 'certificates',
        str_contains($rn, 'portfolio') => 'portfolio',
        str_contains($rn, 'blog') => 'blog',
        str_contains($rn, 'agenda') => 'agenda',
        str_contains($rn, 'partnership') => 'partnership',
        str_contains($rn, 'contact') => 'contact',
        $rn === 'about' => 'about',
        default => 'home',
    };
    // [eyebrow, title, subtitle, button label, button route]
    $ctaSet = [
        'home' => [
            $id ? 'Konsultasi Gratis' : 'Free Consultation',
            $id ? 'Siap melakukan transformasi human capital?' : 'Ready to transform your human capital?',
            $id ? 'Konsultasikan kebutuhan pelatihan, sertifikasi, atau rekrutmen organisasi Anda bersama tim ahli kami.' : 'Consult your training, certification, or recruitment needs with our team of experts.',
            $id ? 'Konsultasi Gratis' : 'Free Consultation', 'contact.index',
        ],
        'about' => [
            $id ? 'Tentang Kami' : 'About Us',
            $id ? 'Mari berkembang bersama Delta Tiga Enam.' : "Let's grow together with Delta Tiga Enam.",
            $id ? 'Pelajari bagaimana pengalaman dan keahlian kami dapat mendukung tujuan organisasi Anda.' : "Discover how our experience and expertise can support your organization's goals.",
            $id ? 'Jelajahi Layanan' : 'Explore Services', 'services.index',
        ],
        'services' => [
            $id ? 'Layanan' : 'Services',
            $id ? 'Temukan layanan yang tepat untuk tim Anda.' : 'Find the right service for your team.',
            $id ? 'Diskusikan kebutuhan spesifik organisasi Anda dan dapatkan rekomendasi terbaik dari kami.' : "Discuss your organization's specific needs and get our best recommendations.",
            $id ? 'Konsultasi Gratis' : 'Free Consultation', 'contact.index',
        ],
        'certificates' => [
            $id ? 'Sertifikasi' : 'Certification',
            $id ? 'Tingkatkan kredibilitas lewat sertifikasi resmi.' : 'Boost credibility with official certification.',
            $id ? 'Ikuti program sertifikasi kompetensi yang diakui secara nasional bersama LSP mitra kami.' : 'Join nationally recognized competency certification programs with our partner LSPs.',
            $id ? 'Konsultasi Sertifikasi' : 'Certification Enquiry', 'contact.index',
        ],
        'portfolio' => [
            $id ? 'Portofolio' : 'Portfolio',
            $id ? 'Wujudkan proyek pengembangan SDM Anda.' : 'Bring your human capital project to life.',
            $id ? 'Lihat bagaimana kami membantu berbagai organisasi, lalu mulai proyek Anda bersama kami.' : "See how we've helped organizations, then start your project with us.",
            $id ? 'Mulai Proyek' : 'Start a Project', 'contact.index',
        ],
        'blog' => [
            $id ? 'Wawasan' : 'Insights',
            $id ? 'Dapatkan wawasan human capital terbaru.' : 'Get the latest human capital insights.',
            $id ? 'Selain membaca artikel kami, konsultasikan langsung tantangan SDM Anda dengan tim ahli.' : 'Beyond our articles, discuss your HR challenges directly with our experts.',
            $id ? 'Konsultasi Gratis' : 'Free Consultation', 'contact.index',
        ],
        'agenda' => [
            $id ? 'Agenda' : 'Agenda',
            $id ? 'Ikuti agenda & pelatihan mendatang.' : 'Join our upcoming events & training.',
            $id ? 'Jangan lewatkan program pelatihan dan sertifikasi kami — hubungi kami untuk info & pendaftaran.' : "Don't miss our training and certification programs — contact us for info & registration.",
            $id ? 'Hubungi Kami' : 'Contact Us', 'contact.index',
        ],
        'partnership' => [
            $id ? 'Kemitraan' : 'Partnership',
            $id ? 'Jalin kemitraan strategis dengan kami.' : 'Build a strategic partnership with us.',
            $id ? 'Bergabunglah sebagai mitra dan tumbuh bersama dalam ekosistem pengembangan SDM kami.' : 'Join as a partner and grow within our human capital ecosystem.',
            $id ? 'Ajukan Kemitraan' : 'Apply for Partnership', 'contact.index',
        ],
        'contact' => [
            $id ? 'Layanan' : 'Services',
            $id ? 'Sudah tahu kebutuhan Anda?' : 'Know what you need?',
            $id ? 'Jelajahi ragam layanan pengembangan human capital kami untuk menemukan solusi yang pas.' : 'Explore our range of human capital services to find the right fit.',
            $id ? 'Lihat Layanan' : 'View Services', 'services.index',
        ],
    ];
    [$ctaEyebrow, $ctaTitle, $ctaSub, $ctaBtn, $ctaRoute] = $ctaSet[$ctaKey];
@endphp

<footer class="bg-slate-100">

    {{-- Light band that holds the overlapping CTA card --}}
    <div class="container pt-16 md:pt-20">
        <div class="relative z-20 mx-auto max-w-7xl overflow-hidden rounded-3xl bg-navy-anim px-6 py-10 text-center text-white shadow-lift ring-1 ring-white/5 md:px-20 md:py-14" data-aos="fade-up">
            {{-- faint flowing line-art --}}
            <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-[0.07]" viewBox="0 0 800 400" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                <path d="M-40 300C160 180 260 420 460 300S760 120 900 240" stroke="white" stroke-width="60" stroke-linecap="round"/>
                <path d="M-40 160C180 60 300 240 520 140S820 20 900 120" stroke="white" stroke-width="40" stroke-linecap="round"/>
            </svg>
            <div class="pointer-events-none absolute -right-20 -top-24 h-64 w-64 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>

            <div class="relative">
                <p class="eyebrow mb-4 inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $ctaEyebrow }}</p>
                <h3 class="mx-auto max-w-2xl text-3xl leading-[1.1] text-white text-balance md:text-[2.75rem]">{{ $ctaTitle }}</h3>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-navy-100 md:text-base">{{ $ctaSub }}</p>
                <a href="{{ route($ctaRoute) }}" class="group mt-7 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-semibold text-navy shadow-lg ring-1 ring-transparent transition duration-200 hover:-translate-y-0.5 hover:bg-navy-50 hover:ring-2 hover:ring-gold-soft">
                    {{ $ctaBtn }}
                    <svg class="h-4 w-4 text-gold-deep transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="8" x2="13" y2="8"/><polyline points="9 4 13 8 9 12"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Full-width footer area — pulled up so the card overlaps its top edge --}}
    <div class="relative z-10 -mt-24 bg-slate-100">
        <div class="container pb-10 pt-32 md:pt-36">
            <div class="grid gap-12 lg:grid-cols-12 lg:gap-8">

                {{-- Brand --}}
                <div class="lg:col-span-4 space-y-6">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/logodelta36.png') }}" alt="PT. Delta Tiga Enam" class="h-10 w-10 shrink-0">
                        <span class="font-display text-lg font-semibold tracking-wide text-navy">PT. Delta Tiga Enam</span>
                    </a>
                    <p class="max-w-sm text-sm leading-relaxed text-navy-500">
                        {{ $id
                            ? 'Sertifikasi, pelatihan, penyeleksian, dan penempatan tenaga kerja di Indonesia.'
                            : 'Certification, training, selection, and placement of workforce in Indonesia.' }}
                    </p>
                    <div class="flex items-center gap-3 pt-1">
                        <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-full bg-navy text-white ring-1 ring-transparent transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="LinkedIn">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                        <a href="mailto:{{ $email }}" class="grid h-9 w-9 place-items-center rounded-full bg-navy text-white ring-1 ring-transparent transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="Email">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        </a>
                        <a href="https://{{ $website }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-full bg-navy text-white ring-1 ring-transparent transition-all duration-200 hover:-translate-y-0.5 hover:bg-gold hover:text-navy-950 hover:ring-gold-soft" aria-label="Website">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Services + Quick Links --}}
                <div class="lg:col-span-4">
                    <div class="grid gap-x-12 gap-y-8 sm:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-gold">{{ $id ? 'Layanan' : 'Services' }}</p>
                            <span class="mt-2.5 block h-0.5 w-7 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                            <ul class="mt-5 space-y-3.5 text-sm">
                                <li><a href="{{ route('services.index') }}" class="group inline-flex items-center text-navy-500 transition-colors hover:text-navy">
                                    <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $id ? 'Sertifikasi Kompetensi' : 'Competence Certification' }}
                                </a></li>
                                <li><a href="{{ route('services.index') }}" class="group inline-flex items-center text-navy-500 transition-colors hover:text-navy">
                                    <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $id ? 'Pelatihan & Pengembangan' : 'Training & Development' }}
                                </a></li>
                                <li><a href="{{ route('services.index') }}" class="group inline-flex items-center text-navy-500 transition-colors hover:text-navy">
                                    <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $id ? 'Asesmen & Rekrutmen' : 'Assessment & Recruitment' }}
                                </a></li>
                                <li><a href="{{ route('services.index') }}" class="group inline-flex items-center text-navy-500 transition-colors hover:text-navy">
                                    <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $id ? 'Konsultasi Human Capital' : 'Human Capital Consulting' }}
                                </a></li>
                            </ul>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gold">{{ __('site.common.quick_links') }}</p>
                            <span class="mt-2.5 block h-0.5 w-7 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                            <ul class="mt-5 space-y-3.5 text-sm">
                                @foreach ($nav as $route => $label)
                                    <li><a href="{{ route($route) }}" class="group inline-flex items-center text-navy-500 transition-colors hover:text-navy">
                                        <span class="h-px w-0 bg-gradient-to-r from-gold to-gold-soft transition-all duration-300 group-hover:mr-2.5 group-hover:w-4"></span>{{ $label }}
                                    </a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Contact --}}
                <div class="lg:col-span-4">
                    <p class="text-sm font-semibold text-gold">{{ $id ? 'Kontak' : 'Contact' }}</p>
                    <span class="mt-2.5 block h-0.5 w-7 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                    <ul class="mt-4 space-y-3.5 text-sm text-navy-500">
                        <li class="flex items-start gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-sky-600 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="leading-relaxed">
                                {{ $id
                                    ? 'Gedung BEI Tower 1 Lantai 3, Unit 304, SCBD, Senayan, Jakarta Selatan'
                                    : 'BEI Tower 1 Floor 3, Unit 304, SCBD, Senayan, South Jakarta' }}
                            </span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span>021-5890 5002</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <a href="mailto:{{ $email }}" class="transition-colors hover:text-sky-600">{{ $email }}</a>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <a href="https://{{ $website }}" target="_blank" rel="noopener" class="transition-colors hover:text-sky-600">{{ $website }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Offices — full width below --}}
            <div class="mt-12">
                <p class="text-sm font-semibold text-gold">{{ $id ? 'Kantor Kami' : 'Our Offices' }}</p>
                <span class="mt-2.5 block h-0.5 w-7 rounded-full bg-gradient-to-r from-gold to-gold-soft"></span>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @if ($offices->isNotEmpty())
                        @foreach ($offices as $office)
                            <div class="p-4">
                                <p class="text-xs font-semibold uppercase tracking-wider text-gold-deep">{{ $office->name }}</p>
                                <p class="mt-2 text-sm leading-relaxed text-navy-600">
                                    {!! nl2br(e($office->address)) !!}
                                </p>
                                @if ($office->phone)
                                    <p class="mt-3 text-xs font-medium text-navy-500">PH. {{ $office->phone }}</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        {{-- Kantor Pusat --}}
                        <div class="p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-deep">{{ $id ? 'Kantor Pusat' : 'Head Office' }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-navy-600">
                                Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304<br>
                                Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan,<br>
                                Kebayoran Baru, Jakarta Selatan, DKI Jakarta
                            </p>
                            <p class="mt-3 text-xs font-medium text-navy-500">PH. 021-5890 5002, 0818 834 766</p>
                        </div>

                        {{-- Kantor Pemasaran --}}
                        <div class="p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-deep">{{ $id ? 'Kantor Pemasaran' : 'Marketing Office' }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-navy-600">
                                Cikarang Technopark, Jalan Inti I Blok C1 No. 7<br>
                                Cibatu, Cikarang Selatan, Kabupaten Bekasi<br>
                                Jawa Barat 17530
                            </p>
                            <p class="mt-3 text-xs font-medium text-navy-500">PH. 021-8988 1110</p>
                        </div>

                        {{-- Kantor Operasional --}}
                        <div class="p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gold-deep">{{ $id ? 'Kantor Operasional' : 'Operational Office' }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-navy-600">
                                Taman Widya Asri Blok GG No. 18, Serang<br>
                                Kota Serang, Banten 46111
                            </p>
                            <p class="mt-3 text-xs font-medium text-navy-500">PH. 0817 018 6104</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bottom bar --}}
            <div class="mt-14 flex flex-col items-start justify-between gap-4 border-t border-navy-100 pt-8 text-xs text-navy-400 md:flex-row md:items-center">
                <p>© {{ $year }} PT. Delta Tiga Enam. {{ $id ? 'Hak Cipta Dilindungi Undang-Undang.' : 'All rights reserved.' }}</p>
                <div class="flex items-center gap-5">
                    <a href="#" class="transition-colors hover:text-sky-600">{{ $id ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a>
                    <span class="text-navy-200">|</span>
                    <a href="#" class="transition-colors hover:text-sky-600">{{ $id ? 'Syarat & Ketentuan' : 'Terms & Conditions' }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>