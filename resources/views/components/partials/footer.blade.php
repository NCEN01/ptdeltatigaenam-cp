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

    <div class="container relative py-20 lg:py-24">
        {{-- Top: brand · quick links · contact --}}
        <div class="grid gap-12 lg:grid-cols-12">
            {{-- Brand --}}
            <div class="lg:col-span-5">
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-white to-navy-100 text-navy transition-shadow duration-300 group-hover:shadow-lift">
                        <span class="font-display text-xl font-semibold">D</span>
                    </span>
                    <span class="font-display text-xl font-semibold tracking-wide transition-colors duration-300 group-hover:text-sky-400">DELTA TIGA ENAM</span>
                </a>
                <p class="mt-6 max-w-sm text-pretty leading-relaxed text-navy-200">
                    {{ $id
                        ? 'Sertifikasi, pelatihan, penyeleksian, dan penempatan tenaga kerja di Indonesia.'
                        : 'Certification, training, selection, and placement of workforce in Indonesia.' }}
                </p>
                <a href="{{ route('contact.index') }}" class="btn-blue mt-8">
                    {{ __('site.cta.consult') }}
                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            {{-- Quick links --}}
            <div class="lg:col-span-3">
                <p class="eyebrow-muted mb-5">{{ __('site.common.quick_links') }}</p>
                <ul class="space-y-3">
                    @foreach ($nav as $route => $label)
                        <li>
                            <a href="{{ route($route) }}" class="inline-flex items-center gap-2 text-navy-100 transition-colors hover:text-sky-400">
                                <span class="h-1 w-1 rounded-full bg-sky-500/60"></span>{{ $label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div class="lg:col-span-4">
                <p class="eyebrow-muted mb-5">{{ $id ? 'Kontak' : 'Contact' }}</p>
                <ul class="space-y-3.5 text-sm">
                    <li>
                        <a href="https://{{ $website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-3 text-navy-100 transition-colors hover:text-sky-400">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-white/10 text-sky-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.4"/><path d="M3 12h18M12 3c2.5 2.5 2.5 15 0 18M12 3c-2.5 2.5-2.5 15 0 18" stroke="currentColor" stroke-width="1.4"/></svg>
                            </span>
                            {{ $website }}
                        </a>
                    </li>
                    <li>
                        <a href="mailto:{{ $email }}" class="inline-flex items-center gap-3 text-navy-100 transition-colors hover:text-sky-400">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-white/10 text-sky-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                            </span>
                            {{ $email }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="inline-flex items-center gap-3 text-navy-100 transition-colors hover:text-sky-400">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-white/10 text-sky-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.94 5a1.94 1.94 0 11-3.88 0 1.94 1.94 0 013.88 0zM3.4 8.4h3.1V21H3.4V8.4zM9.1 8.4h2.97v1.72h.04c.41-.78 1.42-1.6 2.93-1.6 3.13 0 3.71 2.06 3.71 4.74V21h-3.1v-5.7c0-1.36-.02-3.1-1.89-3.1-1.9 0-2.19 1.48-2.19 3v5.8H9.1V8.4z"/></svg>
                            </span>
                            LinkedIn
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-14 flex flex-col items-start justify-between gap-4 border-t border-white/10 pt-8 text-sm text-navy-300 md:flex-row md:items-center">
            <p>© {{ $year }} PT. Delta Tiga Enam. {{ $id ? 'Hak cipta dilindungi undang-undang.' : 'All rights reserved.' }}</p>
            <div class="flex items-center gap-5">
                <a href="mailto:{{ $email }}" class="transition-colors hover:text-sky-400">{{ $email }}</a>
                <span class="text-white/20">·</span>
                <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="transition-colors hover:text-sky-400">LinkedIn</a>
            </div>
        </div>
    </div>
</footer>
