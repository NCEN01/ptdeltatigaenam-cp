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
    {{-- Behind an HTTPS tunnel/proxy (e.g. ngrok) the page is HTTPS but some URLs may still
         be http — tell the browser to upgrade them so nothing is blocked as mixed content.
         Only emitted on secure/forwarded-https requests so local http isn't affected. --}}
    @if (request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    <meta name="theme-color" content="#2b83df">
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

    <link rel="icon" href="{{ asset('images/logodelta36.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/logodelta36.png') }}">
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

    {{-- Floating helpers (all pages): scroll-to-top (upper) + WhatsApp with "Butuh bantuan?" bubble (lower) --}}
    <div x-data="floatingHelpers()"
         class="fixed bottom-5 left-4 z-[130] flex flex-col items-start gap-3 print:hidden">
        {{-- Scroll to top — appears after ~1 screen --}}
        <button type="button" x-cloak x-show="showTop"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                @click="toTop()" aria-label="{{ $locale === 'id' ? 'Kembali ke atas' : 'Back to top' }}"
                class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 text-white shadow-lift ring-1 ring-white/20 transition hover:-translate-y-0.5 hover:from-navy-600 hover:to-navy-500 active:scale-95">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
        </button>

        {{-- WhatsApp + help bubble --}}
        <div class="relative" @mouseenter="showBubble = true">
            <div x-cloak x-show="showBubble"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute bottom-1/2 left-[4.5rem] flex translate-y-1/2 items-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 py-2.5 text-sm font-medium text-navy shadow-lift ring-1 ring-navy-100">
                <span>{{ $locale === 'id' ? 'Butuh bantuan?' : 'Need help?' }}</span>
                <button type="button" @click.stop="showBubble = false" aria-label="{{ $locale === 'id' ? 'Tutup' : 'Close' }}" class="text-slate-400 transition-colors hover:text-navy">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </button>
                <span class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-white"></span>
            </div>
            <a href="https://wa.me/62818834766?text={{ rawurlencode($locale === 'id' ? 'Halo Delta Tiga Enam, saya butuh bantuan.' : 'Hello Delta Tiga Enam, I need some help.') }}"
               target="_blank" rel="noopener" aria-label="WhatsApp"
               class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 text-white shadow-[0_10px_30px_-8px_rgba(43,131,223,0.55)] ring-1 ring-white/20 transition hover:-translate-y-0.5 hover:from-navy-600 hover:to-navy-500 active:scale-95">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.2 4.79 1.2h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.95 1.36-.5.05-.96.24-3.23-.67-2.73-1.08-4.47-3.86-4.6-4.04-.14-.18-1.11-1.48-1.11-2.82 0-1.34.7-2 .95-2.28.24-.27.53-.34.7-.34.18 0 .35 0 .5.01.16.01.38-.06.59.45.22.52.73 1.8.8 1.93.06.13.11.28.02.46-.09.18-.13.29-.26.45-.13.16-.28.36-.4.48-.13.13-.27.28-.12.54.15.26.66 1.09 1.42 1.76.97.87 1.79 1.14 2.05 1.27.26.13.41.11.56-.07.15-.18.65-.76.82-1.02.17-.26.35-.22.59-.13.24.09 1.52.72 1.78.85.26.13.43.2.5.31.06.11.06.64-.18 1.32z"/></svg>
            </a>
        </div>
    </div>

    <script>
        function floatingHelpers() {
            return {
                showTop: false,
                showBubble: false,
                init() {
                    const onScroll = () => { this.showTop = window.scrollY > window.innerHeight * 0.9; };
                    window.addEventListener('scroll', onScroll, { passive: true });
                    onScroll();
                    setTimeout(() => { this.showBubble = true; }, 2800);
                },
                toTop() {
                    const rm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    window.scrollTo({ top: 0, behavior: rm ? 'auto' : 'smooth' });
                },
            };
        }
    </script>

    @stack('scripts')
</body>
</html>
