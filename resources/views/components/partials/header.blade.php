@php
    use App\Support\Locale;
    $current = Locale::current();
    $nav = [
        ['route' => 'about', 'label' => __('site.nav.about')],
        ['route' => 'services.index', 'label' => __('site.nav.services')],
        ['route' => 'portfolio.index', 'label' => __('site.nav.portfolio')],
        ['route' => 'blog.index', 'label' => __('site.nav.blog')],
        ['route' => 'partnership.index', 'label' => __('site.nav.partnership')],
        ['route' => 'contact.index', 'label' => __('site.nav.contact')],
    ];
@endphp

<header
    x-data="{ scrolled: false, open: false }"
    x-init="scrolled = window.scrollY > 24; window.addEventListener('scroll', () => scrolled = window.scrollY > 24)"
    class="fixed inset-x-0 top-0 z-[100] transition-all duration-300 ease-out-soft"
    :class="scrolled ? 'py-2.5' : 'py-4'"
>
    <div class="container">
        <div
            class="flex items-center justify-between rounded-full border px-4 py-2.5 transition-all duration-300 ease-out-soft md:px-5"
            :class="scrolled
                ? 'border-navy-100 bg-white/85 shadow-card backdrop-blur-xl'
                : 'border-transparent bg-transparent'"
        >
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-navy text-white shadow-card">
                    <span class="font-display text-lg font-semibold leading-none">D</span>
                </span>
                <span class="flex flex-col leading-none">
                    <span class="font-display text-[15px] font-semibold tracking-tight text-navy">Delta Tiga Enam</span>
                    <span class="font-mono text-[9px] uppercase tracking-[0.2em] text-navy-300">Human Capital</span>
                </span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary">
                @foreach ($nav as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200 {{ $active ? 'text-navy' : 'text-navy-500 hover:text-navy' }}">
                        {{ $item['label'] }}
                        @if ($active)<span class="mx-auto mt-0.5 block h-px w-4 bg-gold"></span>@endif
                    </a>
                @endforeach
            </nav>

            {{-- Right cluster --}}
            <div class="flex items-center gap-2">
                {{-- Language switcher --}}
                <div x-data="{ langOpen: false }" class="relative hidden sm:block">
                    <button @click="langOpen = !langOpen" @click.outside="langOpen = false"
                            class="flex items-center gap-1.5 rounded-full px-3 py-2 font-mono text-xs uppercase tracking-wider text-navy-500 transition-colors hover:text-navy"
                            aria-label="{{ __('site.common.language') }}">
                        {{ strtoupper($current) }}
                        <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div x-show="langOpen" x-transition.opacity.duration.150ms x-cloak
                         class="absolute right-0 mt-2 w-36 overflow-hidden rounded-2xl border border-navy-100 bg-white p-1 shadow-lift">
                        @foreach (Locale::supported() as $loc)
                            <a href="{{ Locale::alternate($loc) }}"
                               class="flex items-center justify-between rounded-xl px-3 py-2 text-sm transition-colors {{ $loc === $current ? 'bg-mist text-navy' : 'text-navy-500 hover:bg-mist' }}">
                                {{ Locale::label($loc) }}
                                <span class="font-mono text-[10px] text-navy-300">{{ strtoupper($loc) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @auth('customer')
                    <a href="{{ route('account.profile') }}" class="hidden btn-primary !px-5 !py-2.5 text-sm lg:inline-flex">
                        {{ __('site.nav.account') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden btn-primary !px-5 !py-2.5 text-sm lg:inline-flex">
                        {{ __('site.nav.login') }}
                    </a>
                @endauth

                {{-- Mobile toggle --}}
                <button @click="open = true" class="grid h-10 w-10 place-items-center rounded-full text-navy lg:hidden" aria-label="{{ __('site.common.menu') }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile overlay --}}
    <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[110] lg:hidden">
        <div class="absolute inset-0 bg-navy-950/60 backdrop-blur-sm" @click="open = false"></div>
        <div x-show="open" x-transition:enter="transition ease-out-soft duration-300"
             x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             class="absolute right-0 top-0 flex h-full w-[82%] max-w-sm flex-col bg-white p-6 shadow-lift">
            <div class="mb-8 flex items-center justify-between">
                <span class="font-display text-lg font-semibold text-navy">Menu</span>
                <button @click="open = false" class="grid h-10 w-10 place-items-center rounded-full text-navy" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </button>
            </div>
            <nav class="flex flex-col gap-1" aria-label="Mobile">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="rounded-2xl px-4 py-3.5 font-display text-xl text-navy transition-colors hover:bg-mist {{ request()->routeIs($item['route']) ? 'bg-mist' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <div class="mt-auto space-y-4 pt-6">
                <div class="flex gap-2">
                    @foreach (Locale::supported() as $loc)
                        <a href="{{ Locale::alternate($loc) }}"
                           class="flex-1 rounded-full border px-4 py-2.5 text-center font-mono text-xs uppercase tracking-wider {{ $loc === $current ? 'border-navy bg-navy text-white' : 'border-navy-200 text-navy-500' }}">
                            {{ Locale::label($loc) }}
                        </a>
                    @endforeach
                </div>
                @auth('customer')
                    <a href="{{ route('account.profile') }}" class="btn-primary w-full">{{ __('site.nav.account') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary w-full">{{ __('site.nav.login') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>
