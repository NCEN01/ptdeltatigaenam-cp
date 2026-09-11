@props([
    'items' => [],
])

@php
    $isId = app()->getLocale() === 'id';
    // The Alpine component needs both the anchor id and the label (the mobile handle
    // shows the section you are currently reading).
    $tocData = array_map(fn ($item) => ['id' => $item['id'], 'text' => $item['text']], $items);
    // Progress ring geometry: r=15.5 -> circumference 2*pi*r.
    $ring = 97.39;
    $topLevelSeen = 0;
@endphp

@if (count($items))
    <div x-data="articleToc(@js($tocData))">

        {{-- ════════ Desktop: sticky panel in the sidebar ════════ --}}
        <nav aria-label="{{ $isId ? 'Daftar isi artikel' : 'Article table of contents' }}"
             class="hidden rounded-2xl bg-navy-50/60 p-6 lg:block">

            <div class="flex items-baseline justify-between gap-3">
                <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">
                    {{ $isId ? 'Daftar Isi' : 'On this page' }}
                </span>
                <span class="font-mono text-[11px] tabular-nums text-slate-400" x-text="Math.round(progress) + '%'">0%</span>
            </div>

            <div class="mt-3 h-0.5 w-full overflow-hidden rounded-full bg-navy-200/70">
                <div class="h-full origin-left rounded-full bg-gradient-to-r from-sky-500 to-gold"
                     :style="`transform: scaleX(${progress / 100})`"></div>
            </div>

            <ol class="relative mt-5">
                <span class="pointer-events-none absolute bottom-2 left-[3px] top-2 w-px bg-navy-200/70" aria-hidden="true"></span>
                <span class="pointer-events-none absolute bottom-2 left-[3px] top-2 w-px origin-top bg-gradient-to-b from-sky-500 to-gold"
                      :style="`transform: scaleY(${progress / 100})`" aria-hidden="true"></span>

                @foreach ($items as $item)
                    @php
                        $isSub = ($item['level'] ?? 2) >= 3;
                        $number = $isSub ? null : ++$topLevelSeen;
                    @endphp
                    <li class="relative" x-data="{ id: @js($item['id']) }">
                        <a href="#{{ $item['id'] }}" data-no-smooth @click="active = id"
                           class="group relative flex items-start gap-2.5 py-2 pr-1 text-sm leading-snug transition-colors duration-200 {{ $isSub ? 'pl-11' : 'pl-6' }}"
                           :class="active === id ? 'text-navy font-semibold' : 'text-slate-500 hover:text-navy'"
                           :aria-current="active === id ? 'true' : null">
                            <span aria-hidden="true"
                                  class="absolute left-0 top-[1.05rem] h-[7px] w-[7px] -translate-y-1/2 rounded-full ring-2 ring-navy-50 transition-all duration-300 ease-out-soft"
                                  :class="active === id ? 'bg-sky-500 scale-[1.35]' : 'bg-navy-200 group-hover:bg-navy-300'"></span>
                            @if ($number !== null)
                                <span class="mt-px shrink-0 font-mono text-[10px] tabular-nums transition-colors duration-200"
                                      :class="active === id ? 'text-sky-600' : 'text-slate-300 group-hover:text-slate-400'">
                                    {{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                            <span class="min-w-0 flex-1 text-pretty">{{ $item['text'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>

        {{-- ════════ Mobile: handle on the right edge + slide-in drawer ════════
             Fixed rather than sticky: on phones this component sits in a short row of
             its own, so a sticky box would have nothing to travel inside. Parked at the
             vertical middle to stay clear of the bottom-right floating buttons. --}}
        <button type="button" x-cloak @click="expanded = true"
                class="fixed right-3 top-1/2 z-[95] grid h-14 w-14 -translate-y-1/2 place-items-center rounded-full border border-navy-100 bg-white/90 shadow-lift backdrop-blur-xl transition-all duration-300 ease-out-soft active:scale-95 lg:hidden"
                :class="barVisible ? 'translate-x-0 opacity-100' : 'pointer-events-none translate-x-6 opacity-0'"
                :aria-expanded="expanded ? 'true' : 'false'"
                aria-controls="toc-drawer"
                aria-label="{{ $isId ? 'Buka daftar isi' : 'Open table of contents' }}">

            {{-- Progress ring hugging the handle --}}
            <svg viewBox="0 0 36 36" class="absolute inset-0 h-full w-full -rotate-90" aria-hidden="true">
                <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e6ecf5" stroke-width="2.5"/>
                <circle cx="18" cy="18" r="15.5" fill="none" stroke="#2b83df" stroke-width="2.5" stroke-linecap="round"
                        stroke-dasharray="{{ $ring }}"
                        :stroke-dashoffset="{{ $ring }} - (progress / 100) * {{ $ring }}"
                        style="transition: stroke-dashoffset .15s linear"/>
            </svg>

            <span class="relative flex flex-col items-center leading-none">
                <svg class="h-4 w-4 text-navy" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M2.5 4h11M2.5 8h11M2.5 12h7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
                <span class="mt-1 font-mono text-[9px] font-bold tabular-nums text-slate-400" x-text="Math.round(progress)">0</span>
            </span>
        </button>

        {{-- Backdrop --}}
        <div x-show="expanded" x-cloak @click="expanded = false"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[140] bg-navy-950/45 backdrop-blur-[2px] lg:hidden"></div>

        {{-- Drawer --}}
        <div id="toc-drawer" x-show="expanded" x-cloak
             x-transition:enter="transition ease-out-soft duration-300"
             x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 z-[141] flex w-[84%] max-w-xs flex-col bg-white shadow-lift lg:hidden"
             role="dialog" aria-modal="true"
             aria-label="{{ $isId ? 'Daftar isi artikel' : 'Article table of contents' }}">

            <div class="flex items-start justify-between gap-3 border-b border-navy-100 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">
                        {{ $isId ? 'Daftar Isi' : 'On this page' }}
                    </p>
                    <p class="mt-1 truncate text-sm font-semibold text-navy" x-text="activeLabel"></p>
                </div>
                <button type="button" @click="expanded = false"
                        class="-mr-1 grid h-9 w-9 shrink-0 place-items-center rounded-lg text-slate-400 transition-colors hover:bg-navy-50 hover:text-navy"
                        aria-label="{{ $isId ? 'Tutup daftar isi' : 'Close table of contents' }}">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <div class="h-0.5 w-full shrink-0 overflow-hidden bg-navy-100">
                <div class="h-full origin-left bg-gradient-to-r from-sky-500 to-gold"
                     :style="`transform: scaleX(${progress / 100})`"></div>
            </div>

            <ol class="flex-1 overflow-y-auto px-3 py-3">
                @php $topLevelSeen = 0; @endphp
                @foreach ($items as $item)
                    @php
                        $isSub = ($item['level'] ?? 2) >= 3;
                        $number = $isSub ? null : ++$topLevelSeen;
                    @endphp
                    <li x-data="{ id: @js($item['id']) }">
                        <a href="#{{ $item['id'] }}" data-no-smooth
                           @click="active = id; expanded = false"
                           class="flex items-start gap-2.5 rounded-xl px-3 py-2.5 text-sm leading-snug transition-colors duration-200 {{ $isSub ? 'pl-9' : '' }}"
                           :class="active === id ? 'bg-sky-50 font-semibold text-navy' : 'text-slate-500'"
                           :aria-current="active === id ? 'true' : null">
                            @if ($number !== null)
                                <span class="mt-px shrink-0 font-mono text-[10px] tabular-nums"
                                      :class="active === id ? 'text-sky-600' : 'text-slate-300'">
                                    {{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                            <span class="min-w-0 flex-1 text-pretty">{{ $item['text'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endif
