@props(['title' => '', 'heading' => '', 'subheading' => null])

@php use App\Support\Locale; @endphp
<!DOCTYPE html>
<html lang="{{ Locale::current() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2b83df">
    <title>{{ $title }} — PT Delta Tiga Enam</title>
    <link rel="icon" href="{{ asset('images/logodelta36.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/logodelta36.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-white text-ink antialiased">
    <div class="grid min-h-dvh lg:grid-cols-2">
        {{-- Brand panel --}}
        <div class="relative hidden overflow-hidden bg-navy-anim p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1400&q=80" alt="" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-40">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/85 to-navy-950/60"></div>
            <div class="pointer-events-none absolute inset-0 aurora opacity-40"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/50 to-transparent"></div>
            <a href="{{ route('home') }}" class="auth-anim relative flex items-center gap-3">
                <img src="{{ asset('images/logodelta36.png') }}" alt="Delta Tiga Enam" class="h-11 w-11 shrink-0">
                <span class="font-display text-lg">PT Delta Tiga Enam</span>
            </a>
            <div class="relative">
                <p class="auth-anim eyebrow mb-5 [animation-delay:120ms]"><span class="rule-gold mr-3"></span>Human Capital · Training · Certification</p>
                <p class="auth-anim max-w-md text-[2.2rem] leading-[1.12] text-balance [animation-delay:200ms]">
                    {{ app()->getLocale() === 'id' ? 'Transformasi human capital yang berkelanjutan.' : 'Sustainable human capital transformation.' }}
                </p>
                <div class="auth-anim mt-8 flex items-center gap-6 [animation-delay:280ms]">
                    <div>
                        <p class="font-display text-2xl text-white md:text-[1.7rem]" data-counter="500" data-counter-suffix="+">0+</p>
                        <p class="mt-1 font-mono text-[10px] uppercase tracking-wider text-navy-200">{{ app()->getLocale() === 'id' ? 'Profesional' : 'Professionals' }}</p>
                    </div>
                    <div class="h-8 w-px bg-white/15"></div>
                    <div>
                        <p class="font-display text-2xl text-white md:text-[1.7rem]" data-counter="10" data-counter-suffix="+">0+</p>
                        <p class="mt-1 font-mono text-[10px] uppercase tracking-wider text-navy-200">{{ app()->getLocale() === 'id' ? 'Tahun' : 'Years' }}</p>
                    </div>
                </div>
            </div>
            <p class="auth-anim relative font-mono text-xs text-slate-400 [animation-delay:360ms]">© {{ now()->year }} PT Delta Tiga Enam</p>
        </div>

        {{-- Form panel --}}
        <div class="flex items-center justify-center px-6 py-10 sm:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="auth-anim mb-8 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-navy lg:hidden">
                    <img src="{{ asset('images/logodelta36.png') }}" alt="Delta Tiga Enam" class="h-9 w-9">
                    Delta Tiga Enam
                </a>

                <h1 class="auth-anim font-display text-3xl font-semibold text-navy">{{ $heading }}</h1>
                @if ($subheading)<p class="auth-anim mt-2 text-slate-600 [animation-delay:80ms]">{{ $subheading }}</p>@endif

                @if (session('status'))
                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">{{ session('status') }}</div>
                @endif

                <div class="mt-6">{{ $slot }}</div>
            </div>
        </div>
    </div>
</body>
</html>
