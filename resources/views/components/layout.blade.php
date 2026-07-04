@props([
    'title' => null,
    'description' => null,
    'ogImage' => null,
])

@php
    use App\Support\Locale;
    use App\Models\Setting;

    $siteName = Setting::get('site_name', 'PT Delta Tiga Enam');
    $pageTitle = $title ? $title.' — '.$siteName : $siteName;
    $desc = $description ?: Setting::getLocalized('company_tagline', null, 'Human capital, pelatihan & sertifikasi profesi.');
    $locale = Locale::current();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A2A5E">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $desc }}">

    {{-- hreflang for ID/EN --}}
    @foreach (Locale::supported() as $alt)
        <link rel="alternate" hreflang="{{ $alt }}" href="{{ Locale::alternate($alt) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ Locale::alternate('id') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $desc }}">
    @if ($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-white text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[200] focus:rounded-full focus:bg-navy focus:px-5 focus:py-2 focus:text-white">
        {{ __('site.nav.home') }}
    </a>

    <x-partials.header />

    {{-- Flash toast (checkout, forms, etc.) --}}
    @php $flash = session('success') ?? session('status') ?? session('error'); @endphp
    @if ($flash)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="transition ease-out-soft duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed inset-x-0 top-24 z-[120] flex justify-center px-4">
            <div class="flex items-start gap-3 rounded-2xl border px-5 py-3.5 shadow-lift backdrop-blur-xl {{ session('error') ? 'border-red-200 bg-red-50/95 text-red-800' : 'border-sky-200 bg-sky-50/95 text-navy' }}">
                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full {{ session('error') ? 'bg-red-500' : 'bg-sky-500' }} text-white">
                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none">
                        @if (session('error'))<path d="M8 5v4m0 3h.01" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        @else<path d="M3.5 8.5 6.5 11.5 12.5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>@endif
                    </svg>
                </span>
                <p class="text-sm font-medium">{{ $flash }}</p>
                <button @click="show = false" class="ml-2 text-current/60 hover:text-current" aria-label="Close">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
            </div>
        </div>
    @endif

    <main id="main">
        {{ $slot }}
    </main>

    <x-partials.footer />

    @stack('scripts')
</body>
</html>
