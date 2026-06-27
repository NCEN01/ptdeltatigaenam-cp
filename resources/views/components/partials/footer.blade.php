@php
    use App\Models\OfficeLocation;
    use App\Models\Setting;
    use App\Support\Locale;

    $offices = OfficeLocation::where('is_active', true)->orderBy('sort_order')->get();
    $email = Setting::get('site_email', 'info@deltatigaenam.com');
    $phone = Setting::get('site_phone', '021-5890 5002');
    $linkedin = Setting::get('linkedin_url', '#');
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
    <div class="pointer-events-none absolute -left-32 -top-32 h-80 w-80 rounded-full bg-navy-700/40 blur-3xl"></div>
    <div class="pointer-events-none absolute right-0 top-1/2 h-64 w-64 rounded-full bg-gold/10 blur-3xl"></div>

    <div class="container relative py-20 lg:py-24">
        <div class="grid gap-14 lg:grid-cols-12">
            {{-- Brand + CTA --}}
            <div class="lg:col-span-5">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-navy">
                        <span class="font-display text-xl font-semibold">D</span>
                    </span>
                    <span class="font-display text-xl font-semibold">PT Delta Tiga Enam</span>
                </a>
                <p class="mt-6 max-w-sm text-pretty text-navy-200">
                    {{ Setting::getLocalized('company_tagline', null, 'Mitra strategis dalam transformasi human capital berkelanjutan.') }}
                </p>
                <a href="{{ route('contact.index') }}" class="btn-gold mt-8">
                    {{ __('site.cta.consult') }}
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>

            {{-- Links --}}
            <div class="lg:col-span-3">
                <p class="eyebrow-muted mb-5">{{ __('site.common.quick_links') }}</p>
                <ul class="space-y-3">
                    @foreach ($nav as $route => $label)
                        <li>
                            <a href="{{ route($route) }}" class="text-navy-100 transition-colors hover:text-gold">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Offices --}}
            <div class="lg:col-span-4">
                <p class="eyebrow-muted mb-5">{{ __('site.common.offices') }}</p>
                <ul class="space-y-5">
                    @foreach ($offices as $office)
                        <li class="border-l border-white/10 pl-4">
                            <p class="font-medium text-white">{{ $office->name }}</p>
                            <p class="mt-1 text-sm text-navy-200">{{ $office->address }}</p>
                            @if ($office->phone)<p class="mt-1 font-mono text-xs text-navy-300">{{ $office->phone }}</p>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="mt-16 flex flex-col items-start justify-between gap-4 border-t border-white/10 pt-8 text-sm text-navy-300 md:flex-row md:items-center">
            <p>© {{ $year }} PT Delta Tiga Enam. {{ __('site.common.all_rights') }}</p>
            <div class="flex items-center gap-5">
                <a href="mailto:{{ $email }}" class="transition-colors hover:text-gold">{{ $email }}</a>
                <span class="text-white/20">·</span>
                <a href="{{ $linkedin }}" class="transition-colors hover:text-gold">LinkedIn</a>
            </div>
        </div>
    </div>
</footer>
