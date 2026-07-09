@php
    use App\Support\Locale;
    $current = Locale::current();
    // Data-driven: the Services dropdown follows the admin's active categories automatically.
    $serviceCategories = \App\Models\ServiceCategory::where('is_active', true)
        ->orderBy('sort_order')->orderBy('name')->get(['name', 'slug']);
    $nav = [
        ['route' => 'about', 'label' => __('site.nav.about')],
        ['route' => 'services.index', 'label' => __('site.nav.services')],
        ['route' => 'certificates.index', 'label' => __('site.nav.certificates')],
        ['route' => 'portfolio.index', 'label' => __('site.nav.portfolio')],
        ['route' => 'blog.index', 'label' => __('site.nav.blog')],
        ['route' => 'agenda.index', 'label' => __('site.nav.agenda')],
        ['route' => 'partnership.index', 'label' => __('site.nav.partnership')],
        ['route' => 'contact.index', 'label' => __('site.nav.contact')],
    ];
@endphp

<header
    x-data="{ scrolled: false, open: false, navHidden: false, lastScrollY: 0 }"
    x-init="
        scrolled = window.scrollY > 24;
        lastScrollY = window.scrollY;
        window.addEventListener('scroll', () => {
            const currentY = window.scrollY;
            scrolled = currentY > 24;
            // Only hide after scrolling past 300px to avoid flickering near the top
            if (currentY > 300 && currentY > lastScrollY + 8) {
                navHidden = true;   // scrolling down → hide
            } else if (currentY < lastScrollY - 8) {
                navHidden = false;  // scrolling up → show
            }
            lastScrollY = currentY;
        }, { passive: true })
    "
    class="fixed inset-x-0 top-0 z-[100] will-change-transform transition-all duration-500 ease-[cubic-bezier(0.25,0.1,0.25,1)]"
    :class="{
        'py-2': scrolled,
        'py-4': !scrolled,
        '-translate-y-full opacity-0': navHidden,
        'translate-y-0 opacity-100': !navHidden
    }"
>
    <div class="container">
        <div
            class="flex items-center justify-between rounded-full border px-4 py-2.5 transition-all duration-500 ease-out-soft md:px-5"
            :class="scrolled
                ? 'border-navy-100 bg-white/80 backdrop-blur-2xl shadow-lift'
                : 'border-transparent bg-transparent'"
        >
            {{-- Brand (logo only) --}}
            <a href="{{ route('home') }}" class="group flex shrink-0 items-center" aria-label="PT. Delta Tiga Enam">
                <img src="{{ asset('images/logodelta36.png') }}" alt="PT. Delta Tiga Enam" width="36" height="36" class="h-8 w-8 shrink-0 transition-transform duration-300 group-hover:scale-105 md:h-9 md:w-9">
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden items-center gap-0.5 lg:flex" aria-label="Primary">
                @foreach ($nav as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    @if ($item['route'] === 'services.index' && $serviceCategories->isNotEmpty())
                        {{-- Services with category dropdown (hover) --}}
                        <div class="group relative">
                            <a href="{{ route($item['route']) }}"
                               class="relative flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium transition-colors duration-300"
                               :class="scrolled ? '{{ $active ? 'text-navy' : 'text-navy-500 group-hover:text-navy' }}' : '{{ $active ? 'text-white' : 'text-white/75 group-hover:text-white' }}'">
                                {{ $item['label'] }}
                                <svg class="h-3 w-3 transition-transform duration-300 group-hover:rotate-180" viewBox="0 0 12 12" fill="none"><path d="M3 4.5 6 7.5 9 4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span class="pointer-events-none absolute -bottom-0.5 left-1/2 h-0.5 w-5 -translate-x-1/2 origin-center rounded-full bg-sky-500 transition-transform duration-300 ease-out-soft {{ $active ? 'scale-100' : 'scale-0 group-hover:scale-100' }}"></span>
                            </a>
                            <div class="invisible absolute left-1/2 top-full z-50 w-72 -translate-x-1/2 pt-3 opacity-0 transition-all duration-200 ease-out-soft group-hover:visible group-hover:opacity-100">
                                <div class="overflow-hidden rounded-2xl border border-navy-100 bg-white p-2 shadow-lift">
                                    <p class="px-3.5 pb-1.5 pt-2 font-mono text-[10px] uppercase tracking-[0.18em] text-navy-300">{{ $current === 'id' ? 'Kategori Layanan' : 'Service Categories' }}</p>
                                    @foreach ($serviceCategories as $cat)
                                        <a href="{{ route('services.index') }}#{{ $cat->slug }}" class="group/it flex items-center justify-between gap-3 rounded-xl px-3.5 py-2.5 text-sm text-navy-600 transition-colors hover:bg-mist hover:text-navy">
                                            <span>{{ $cat->name }}</span>
                                            <svg class="h-3.5 w-3.5 shrink-0 text-navy-200 transition-all duration-200 group-hover/it:translate-x-0.5 group-hover/it:text-sky-600" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                    @endforeach
                                    <div class="my-1.5 h-px bg-navy-50"></div>
                                    <a href="{{ route('services.index') }}" class="flex items-center gap-2 rounded-xl px-3.5 py-2.5 text-sm font-medium text-sky-600 transition-colors hover:bg-mist">
                                        {{ $current === 'id' ? 'Lihat Semua Layanan' : 'View All Services' }}
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="group relative rounded-full px-4 py-2 text-sm font-medium transition-colors duration-300"
                           :class="scrolled ? '{{ $active ? 'text-navy' : 'text-navy-500 hover:text-navy' }}' : '{{ $active ? 'text-white' : 'text-white/75 hover:text-white' }}'">
                            {{ $item['label'] }}
                            <span class="pointer-events-none absolute -bottom-0.5 left-1/2 h-0.5 w-5 -translate-x-1/2 origin-center rounded-full bg-sky-500 transition-transform duration-300 ease-out-soft {{ $active ? 'scale-100' : 'scale-0 group-hover:scale-100' }}"></span>
                        </a>
                    @endif
                @endforeach
            </nav>

            {{-- Right cluster --}}
            <div class="flex items-center gap-2">
                {{-- Language switcher --}}
                <div x-data="{ langOpen: false }" class="relative hidden sm:block">
                    <button @click="langOpen = !langOpen" @click.outside="langOpen = false"
                            class="flex items-center gap-1.5 rounded-full px-3 py-2 font-mono text-xs uppercase tracking-wider transition-colors"
                            :class="scrolled ? 'text-navy-500 hover:text-navy' : 'text-white/80 hover:text-white'"
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
                    <a href="{{ route('account.profile') }}" class="hidden btn-blue !px-5 !py-2.5 text-sm lg:inline-flex">
                        {{ __('site.nav.account') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden btn-blue !px-5 !py-2.5 text-sm lg:inline-flex">
                        {{ __('site.nav.login') }}
                    </a>
                @endauth

                {{-- Mobile toggle --}}
                <button @click="open = true" class="grid h-10 w-10 place-items-center rounded-full transition-colors lg:hidden" :class="scrolled ? 'text-navy' : 'text-white'" aria-label="{{ __('site.common.menu') }}">
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
            <nav class="flex flex-col gap-1 overflow-y-auto" aria-label="Mobile">
                @foreach ($nav as $item)
                    <a href="{{ route($item['route']) }}"
                       class="rounded-2xl px-4 py-3.5 font-display text-xl text-navy transition-colors hover:bg-mist {{ request()->routeIs($item['route']) ? 'bg-mist' : '' }}">
                        {{ $item['label'] }}
                    </a>
                    @if ($item['route'] === 'services.index' && $serviceCategories->isNotEmpty())
                        <div class="mb-1 ml-4 flex flex-col gap-0.5 border-l border-navy-100 pl-3">
                            @foreach ($serviceCategories as $cat)
                                <a href="{{ route('services.index') }}#{{ $cat->slug }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm text-navy-500 transition-colors hover:bg-mist hover:text-navy">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                    @endif
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
                    <a href="{{ route('account.profile') }}" class="btn-blue w-full">{{ __('site.nav.account') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn-blue w-full">{{ __('site.nav.login') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>
