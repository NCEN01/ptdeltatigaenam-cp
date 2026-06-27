@props(['title' => '', 'heading' => '', 'subheading' => null])

@php use App\Support\Locale; @endphp
<!DOCTYPE html>
<html lang="{{ Locale::current() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A2A5E">
    <title>{{ $title }} — PT Delta Tiga Enam</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-white text-ink antialiased">
    <div class="grid min-h-dvh lg:grid-cols-2">
        {{-- Brand panel --}}
        <div class="relative hidden overflow-hidden bg-navy-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute inset-0 aurora opacity-70"></div>
            <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
            <a href="{{ route('home') }}" class="relative flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white text-navy font-display text-xl font-semibold">D</span>
                <span class="font-display text-lg font-semibold">PT Delta Tiga Enam</span>
            </a>
            <div class="relative">
                <p class="eyebrow mb-5">Human Capital · Training · Certification</p>
                <p class="max-w-md font-display text-3xl font-semibold leading-tight text-balance">
                    {{ app()->getLocale() === 'id' ? 'Transformasi human capital yang berkelanjutan.' : 'Sustainable human capital transformation.' }}
                </p>
            </div>
            <p class="relative font-mono text-xs text-navy-300">© {{ now()->year }} PT Delta Tiga Enam</p>
        </div>

        {{-- Form panel --}}
        <div class="flex items-center justify-center px-6 py-12 sm:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-10 inline-flex items-center gap-2 text-sm text-navy-400 hover:text-navy lg:hidden">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-navy text-white font-display">D</span>
                    Delta Tiga Enam
                </a>

                <h1 class="font-display text-3xl font-semibold text-navy">{{ $heading }}</h1>
                @if ($subheading)<p class="mt-2 text-navy-500">{{ $subheading }}</p>@endif

                @if (session('status'))
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</div>
                @endif

                <div class="mt-8">{{ $slot }}</div>
            </div>
        </div>
    </div>
</body>
</html>
