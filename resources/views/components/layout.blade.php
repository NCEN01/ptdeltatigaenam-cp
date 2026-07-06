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
                class="grid h-12 w-12 place-items-center rounded-full bg-navy text-white shadow-lift ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-navy-800 active:scale-95">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
        </button>

        {{-- WhatsApp + help bubble --}}
        <div class="relative" @mouseenter="showBubble = true">
            <div x-cloak x-show="showBubble"
                 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-2" x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="absolute bottom-1/2 left-[4.5rem] flex translate-y-1/2 items-center gap-2 whitespace-nowrap rounded-xl bg-white px-4 py-2.5 text-sm font-medium text-navy shadow-lift ring-1 ring-navy-100">
                <span>{{ $locale === 'id' ? 'Butuh bantuan?' : 'Need help?' }}</span>
                <button type="button" @click.stop="showBubble = false" aria-label="{{ $locale === 'id' ? 'Tutup' : 'Close' }}" class="text-navy-300 transition-colors hover:text-navy">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </button>
                <span class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-white"></span>
            </div>
            <a href="https://wa.me/62818834766?text={{ rawurlencode($locale === 'id' ? 'Halo Delta Tiga Enam, saya butuh bantuan.' : 'Hello Delta Tiga Enam, I need some help.') }}"
               target="_blank" rel="noopener" aria-label="WhatsApp"
               class="grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-[0_10px_30px_-8px_rgba(37,211,102,0.6)] ring-1 ring-white/20 transition hover:-translate-y-0.5 active:scale-95">
                <img src="{{ asset('icons/ic_whatsapp.png') }}" class="h-7 w-7 object-contain" alt="WhatsApp">
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
