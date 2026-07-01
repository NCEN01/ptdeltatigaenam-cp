<x-layout :title="__('site.nav.about')" :description="$about">
    <x-page-header
        :eyebrow="__('site.nav.about')"
        :title="app()->getLocale() === 'id' ? 'Membangun human capital yang berdampak' : 'Building human capital that matters'"
        :subtitle="$tagline"
        image="photo-1522071820081-009f0129c71c" />

    {{-- ===================== PROFILE ===================== --}}
    <section class="section">
        <div class="container grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
            {{-- Narrative --}}
            <div class="lg:col-span-7" data-aos="fade-up">
                <p class="eyebrow mb-5"><span class="rule-gold mr-3"></span>{{ app()->getLocale() === 'id' ? 'Profil Perusahaan' : 'Company Profile' }}</p>
                <h2 class="max-w-2xl text-display-lg font-semibold text-navy text-balance" data-text-reveal>
                    {{ app()->getLocale() === 'id' ? 'Mitra pengembangan kompetensi & talenta' : 'A partner in competency & talent development' }}
                </h2>
                <div class="prose prose-lg mt-7 max-w-none text-navy-700 prose-headings:font-display prose-headings:text-navy">
                    <p class="text-pretty leading-relaxed">{{ $about }}</p>
                </div>

                {{-- Value chips --}}
                <div class="mt-9 flex flex-wrap gap-3">
                    @foreach ($values as $value)
                        <span class="inline-flex items-center gap-2 rounded-full border border-navy-100 bg-mist px-4 py-2 text-sm font-medium text-navy">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $value['title'] }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Photo card — drop a real <img> here later --}}
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="100">
                <figure class="group relative aspect-[4/5] overflow-hidden rounded-3xl border border-navy-100 bg-navy-950 shadow-lift">
                    {{--
                        Ganti blok placeholder di bawah dengan foto perusahaan, contoh:
                        <img src="{{ asset('images/about/kantor.jpg') }}" alt="PT Delta Tiga Enam"
                             class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                    --}}
                    <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
                    <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
                    <div class="absolute inset-0 grid place-items-center p-8 text-center text-white">
                        <div>
                            <span class="mx-auto grid h-16 w-16 place-items-center rounded-full border border-white/20 text-gold">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 7h3l2-2h6l2 2h3v12H4V7z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><circle cx="12" cy="13" r="3.2" stroke="currentColor" stroke-width="1.3"/></svg>
                            </span>
                            <p class="mt-5 font-display text-lg font-semibold">{{ app()->getLocale() === 'id' ? 'Foto Perusahaan' : 'Company Photo' }}</p>
                            <p class="mt-1 text-sm text-navy-100">{{ app()->getLocale() === 'id' ? 'Akan ditambahkan' : 'Coming soon' }}</p>
                        </div>
                    </div>
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950 to-transparent p-6">
                        <p class="font-mono text-[10px] uppercase tracking-label text-gold">PT Delta Tiga Enam</p>
                    </div>
                </figure>

                {{-- Floating accent badge --}}
                <div class="relative">
                    <div class="absolute -top-10 left-6 hidden rounded-2xl border border-navy-100 bg-white px-5 py-4 shadow-card sm:block lg:left-auto lg:right-6">
                        <p class="font-display text-3xl font-semibold text-navy">10+</p>
                        <p class="text-xs text-navy-500">{{ app()->getLocale() === 'id' ? 'Tahun pengalaman' : 'Years of experience' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats strip --}}
        <div class="container mt-16">
            <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-3xl border border-navy-100 bg-navy-100 sm:grid-cols-4" data-aos="fade-up">
                @foreach ($stats as $stat)
                    <div class="bg-white p-7 md:p-8">
                        <dt class="font-display text-4xl font-semibold tracking-tight text-navy md:text-5xl">{{ $stat['value'] }}</dt>
                        <dd class="mt-2 text-sm text-navy-500">{{ $stat['label'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ===================== VISION & MISSION ===================== --}}
    <section class="section bg-mist">
        <div class="container">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ app()->getLocale() === 'id' ? 'Visi & Misi' : 'Vision & Mission' }}</p>
                <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ app()->getLocale() === 'id' ? 'Arah dan komitmen kami' : 'Our direction and commitments' }}</h2>
            </div>

            {{-- Vision — premium dark band --}}
            <div class="relative mt-12 overflow-hidden rounded-3xl bg-navy-950 p-8 text-white md:p-12" data-aos="fade-up">
                <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-70"></div>
                <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
                <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                <div class="relative grid gap-8 lg:grid-cols-12 lg:items-center">
                    <div class="lg:col-span-3">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl border border-gold/40 text-gold">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
                        </span>
                        <p class="eyebrow mt-5">{{ app()->getLocale() === 'id' ? 'Visi' : 'Vision' }}</p>
                    </div>
                    <p class="font-display text-2xl font-semibold leading-snug text-balance md:text-3xl lg:col-span-9">{{ $vision }}</p>
                </div>
            </div>

            {{-- Missions --}}
            @if ($missions->isNotEmpty())
                <p class="eyebrow mt-16 mb-8" data-aos="fade-up">{{ app()->getLocale() === 'id' ? 'Misi Kami' : 'Our Mission' }}</p>
                <div class="grid gap-px overflow-hidden rounded-3xl border border-navy-100 bg-navy-100 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($missions as $mission)
                        <div class="group relative flex flex-col gap-6 bg-white p-8 transition-colors duration-300 hover:bg-mist" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 90 }}">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy font-display text-lg text-white transition-colors duration-300 group-hover:bg-gold group-hover:text-ink">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="text-pretty leading-relaxed text-navy-700">{{ $mission->content }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== VALUES ===================== --}}
    <section class="section">
        <div class="container">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ app()->getLocale() === 'id' ? 'Nilai Kami' : 'Our Values' }}</p>
                <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ app()->getLocale() === 'id' ? 'Prinsip yang menjadi fondasi kami' : 'The principles at our foundation' }}</h2>
            </div>

            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4" data-stagger>
                @foreach ($values as $value)
                    <div class="card card-hover group p-8" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <span class="font-mono text-xs text-navy-300 transition-colors duration-300 group-hover:text-gold">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-5 font-display text-xl font-semibold text-navy transition-colors duration-300 group-hover:text-sky-600">{{ $value['title'] }}</h3>
                        <p class="mt-3 text-pretty text-sm leading-relaxed text-navy-500">{{ $value['desc'] }}</p>
                        <span class="mt-5 inline-block h-0.5 w-0 rounded-full bg-gradient-to-r from-sky-500 to-gold transition-all duration-500 group-hover:w-12"></span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== OFFICES ===================== --}}
    @php
        $isIdAbout = app()->getLocale() === 'id';
        $aboutOffices = [
            ['name' => 'Kantor Pusat', 'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan, Kebayoran Baru, Jakarta Selatan, DKI Jakarta', 'phone' => '021-5890 5002, 0818 834 766'],
            ['name' => 'Kantor Pemasaran', 'address' => 'Cikarang Technopark, Jalan Inti I Blok C1 No. 7, Cibatu, Cikarang Selatan, Kabupaten Bekasi, Jawa Barat 17530', 'phone' => '021-8988 1110'],
            ['name' => 'Kantor Operasional', 'address' => 'Taman Widya Asri Blok GG No. 18, Serang, Kota Serang, Banten 46111', 'phone' => '0817 018 6104'],
        ];
    @endphp
    <section class="section bg-mist">
        <div class="container">
            <div class="max-w-2xl">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $isIdAbout ? 'Kantor Kami' : 'Our Offices' }}</p>
                <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $isIdAbout ? 'Temui kami di lokasi berikut' : 'Find us at these locations' }}</h2>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @foreach ($aboutOffices as $office)
                    <div class="card card-hover group flex flex-col p-8" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-navy text-gold transition-colors duration-300 group-hover:bg-sky-600 group-hover:text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-5.2 7-11a7 7 0 10-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.4"/></svg>
                        </span>
                        <h3 class="mt-5 font-display text-xl font-semibold text-navy">{{ $office['name'] }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-relaxed text-navy-500">{{ $office['address'] }}</p>
                        <p class="mt-5 flex items-center gap-2 border-t border-navy-100 pt-4 font-mono text-xs text-navy-500">
                            <svg class="h-3.5 w-3.5 shrink-0 text-gold" viewBox="0 0 24 24" fill="none"><path d="M5 4h4l2 5-3 2a12 12 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                            {{ $office['phone'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layout>
