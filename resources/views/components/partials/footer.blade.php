@php
    use App\Models\Setting;

    $id = app()->getLocale() === 'id';
    $email = 'info@deltatigaenam.com';
    $website = 'www.deltatigaenam.com';
    $linkedin = Setting::get('linkedin_url', 'https://linkedin.com/company/deltatigaenam');
    $year = now()->year;

    $nav = [
        'about' => __('site.nav.about'),
        'services.index' => __('site.nav.services'),
        'portfolio.index' => __('site.nav.portfolio'),
        'blog.index' => __('site.nav.blog'),
        'partnership.index' => __('site.nav.partnership'),
        'contact.index' => __('site.nav.contact'),
    ];
@endphp

<footer class="relative overflow-hidden bg-navy-950 text-white">
    {{-- Ambient glows --}}
    <div class="pointer-events-none absolute -left-32 -top-32 h-80 w-80 rounded-full bg-sky-500/10 blur-3xl"></div>
    <div class="pointer-events-none absolute right-0 top-1/2 h-64 w-64 rounded-full bg-cyan/10 blur-3xl"></div>
    {{-- Top gradient line --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-sky-500/40 to-transparent"></div>

    <div class="container relative py-16 lg:py-24">
        
        {{-- Interactive Consultation Banner (Top Footer Band) --}}
        <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-r from-sky-600 to-navy-800 p-8 md:p-12 shadow-lift mb-16" data-aos="fade-up">
            <div class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-cyan/20 blur-2xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <h3 class="font-display text-2xl md:text-3xl font-semibold text-white">
                        {{ $id ? 'Siap melakukan transformasi human capital?' : 'Ready to transform your human capital?' }}
                    </h3>
                    <p class="text-sm text-sky-100 max-w-xl">
                        {{ $id ? 'Konsultasikan kebutuhan pelatihan, sertifikasi, atau rekrutmen organisasi Anda secara gratis bersama tim ahli kami.' : 'Consult your training, certification, or recruitment needs for free with our team of experts.' }}
                    </p>
                </div>
                <a href="{{ route('contact.index') }}" class="btn bg-white text-navy hover:bg-navy-50 shadow-lift group shrink-0 inline-flex items-center gap-2">
                    <span>{{ __('site.cta.consult') }}</span>
                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="8" x2="13" y2="8"/><polyline points="9 4 13 8 9 12"/></svg>
                </a>
            </div>
        </div>

        {{-- 4-Column Grid Layout --}}
        <div class="grid gap-12 lg:grid-cols-12 lg:gap-8">
            
            {{-- Column 1: Brand & Profile --}}
            <div class="lg:col-span-4 space-y-6">
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-white to-navy-100 text-navy transition-all duration-300 group-hover:shadow-lift group-hover:scale-105">
                        <span class="font-display text-xl font-bold">D</span>
                    </span>
                    <span class="font-display text-lg font-bold tracking-wide transition-colors duration-300 group-hover:text-sky">DELTA TIGA ENAM</span>
                </a>
                <p class="text-sm leading-relaxed text-navy-200 max-w-sm">
                    {{ $id
                        ? 'Sertifikasi kompetensi, pelatihan profesi, asesmen, penyeleksian, dan penempatan tenaga kerja unggul serta terintegrasi di Indonesia.'
                        : 'Competence certification, professional training, assessment, selection, and placement of superior integrated workforce in Indonesia.' }}
                </p>
                {{-- Social Icons Row --}}
                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-xl bg-white/5 border border-white/10 text-navy-100 transition-all hover:bg-sky hover:border-sky hover:text-white" aria-label="LinkedIn">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.779-1.75-1.75s.784-1.75 1.75-1.75 1.75.779 1.75 1.75-.784 1.75-1.75 1.75zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <a href="mailto:{{ $email }}" class="grid h-9 w-9 place-items-center rounded-xl bg-white/5 border border-white/10 text-navy-100 transition-all hover:bg-sky hover:border-sky hover:text-white" aria-label="Email">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </a>
                    <a href="https://{{ $website }}" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-xl bg-white/5 border border-white/10 text-navy-100 transition-all hover:bg-sky hover:border-sky hover:text-white" aria-label="Website">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Column 2: Key Services --}}
            <div class="lg:col-span-3 space-y-4">
                <p class="eyebrow-muted">{{ $id ? 'Layanan Kami' : 'Our Services' }}</p>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('services.index') }}" class="text-navy-200 transition-colors hover:text-sky inline-flex items-center gap-1.5 group">
                            <span class="h-1 w-1 rounded-full bg-sky/50 transition-all group-hover:w-2 group-hover:bg-sky"></span>
                            {{ $id ? 'Sertifikasi Kompetensi' : 'Competence Certification' }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}" class="text-navy-200 transition-colors hover:text-sky inline-flex items-center gap-1.5 group">
                            <span class="h-1 w-1 rounded-full bg-sky/50 transition-all group-hover:w-2 group-hover:bg-sky"></span>
                            {{ $id ? 'Pelatihan & Pengembangan' : 'Training & Development' }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}" class="text-navy-200 transition-colors hover:text-sky inline-flex items-center gap-1.5 group">
                            <span class="h-1 w-1 rounded-full bg-sky/50 transition-all group-hover:w-2 group-hover:bg-sky"></span>
                            {{ $id ? 'Asesmen & Rekrutmen' : 'Assessment & Recruitment' }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}" class="text-navy-200 transition-colors hover:text-sky inline-flex items-center gap-1.5 group">
                            <span class="h-1 w-1 rounded-full bg-sky/50 transition-all group-hover:w-2 group-hover:bg-sky"></span>
                            {{ $id ? 'Konsultasi Human Capital' : 'Human Capital Consulting' }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Column 3: Navigation --}}
            <div class="lg:col-span-2 space-y-4">
                <p class="eyebrow-muted">{{ __('site.common.quick_links') }}</p>
                <ul class="space-y-2.5 text-sm">
                    @foreach ($nav as $route => $label)
                        <li>
                            <a href="{{ route($route) }}" class="text-navy-200 transition-colors hover:text-sky inline-flex items-center gap-1.5 group">
                                <span class="h-1 w-1 rounded-full bg-sky/50 transition-all group-hover:w-2 group-hover:bg-sky"></span>
                                {{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Column 4: Contact Info --}}
            <div class="lg:col-span-3 space-y-4">
                <p class="eyebrow-muted">{{ $id ? 'Hubungi Kami' : 'Contact Us' }}</p>
                <ul class="space-y-3.5 text-xs text-navy-200">
                    <li class="flex items-start gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-sky mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span class="leading-relaxed">
                            {{ $id 
                                ? 'Gedung BEI Tower 1 Lantai 3, Unit 304, SCBD, Senayan, Jakarta Selatan' 
                                : 'BEI Tower 1 Floor 3, Unit 304, SCBD, Senayan, South Jakarta' }}
                        </span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-sky" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span>021-5890 5002</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-sky" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <a href="mailto:{{ $email }}" class="hover:underline hover:text-sky">{{ $email }}</a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="mt-16 flex flex-col items-start justify-between gap-4 border-t border-white/10 pt-8 text-xs text-navy-300 md:flex-row md:items-center">
            <p>© {{ $year }} PT. Delta Tiga Enam. {{ $id ? 'Hak Cipta Dilindungi Undang-Undang.' : 'All rights reserved.' }}</p>
            <div class="flex items-center gap-5">
                <a href="#" class="transition-colors hover:text-sky">{{ $id ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a>
                <span class="text-white/10">|</span>
                <a href="#" class="transition-colors hover:text-sky">{{ $id ? 'Syarat & Ketentuan' : 'Terms & Conditions' }}</a>
            </div>
        </div>
    </div>
</footer>
