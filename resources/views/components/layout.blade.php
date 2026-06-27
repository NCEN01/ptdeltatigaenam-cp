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

    <main id="main">
        {{ $slot }}
    </main>

    <x-partials.footer />
</body>
</html>
