@props([
    'items' => [],
])

@php
    $isId = app()->getLocale() === 'id';
    // The Alpine component needs both the anchor id and the label (the mobile bar
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

        {{-- ════════ Mobile: reading bar pinned under the header ════════
             Fixed rather than sticky: on phones this component sits in a short
             row of its own, so a sticky box would have nothing to travel inside. --}}
        <div class="pointer-events-none fixed inset-x-0 z-[90] px-4 transition-all duration-300 ease-out-soft lg:hidden"
             style="top: calc(var(--header-h) + 0.25rem)"
             :class="barVisible ? 'translate-y-0 opacity-100' : '-translate-y-3 opacity-0'">

            <div class="pointer-events-auto mx-auto max-w-lg overflow-hidden rounded-2xl border border-navy-100 bg-white/90 shadow-lift backdrop-blur-xl"
                 :class="!barVisible && 'pointer-events-none'"
                 @click.outside="expanded = false">

                <button type="button" @click="expanded = !expanded"
                        class="flex w-full items-center gap-3 px-3.5 py-2.5 text-left"
                        :aria-expanded="expanded ? 'true' : 'false'"
                        aria-controls="toc-list-mobile">

                    {{-- Progress ring --}}
                    <span class="relative grid h-9 w-9 shrink-0 place-items-center">
                        <svg viewBox="0 0 36 36" class="h-9 w-9 -rotate-90" aria-hidden="true">
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e6ecf5" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#2b83df" stroke-width="3" stroke-linecap="round"
                                    stroke-dasharray="{{ $ring }}"
                                    :stroke-dashoffset="{{ $ring }} - (progress / 100) * {{ $ring }}"
                                    style="transition: stroke-dashoffset .15s linear"/>
                        </svg>
                        <span class="absolute font-mono text-[8px] font-bold tabular-nums text-navy" x-text="Math.round(progress)">0</span>
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="block text-[9px] font-bold uppercase tracking-[0.18em] text-slate-400">
                            {{ $isId ? 'Sedang dibaca' : 'Now reading' }}
                        </span>
                        <span class="block truncate text-sm font-semibold text-navy" x-text="activeLabel"></span>
                    </span>

                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg text-slate-400">
                        <svg class="h-4 w-4 transition-transform duration-300 ease-out-soft" :class="expanded && 'rotate-180'"
                             viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </button>

                <ol id="toc-list-mobile" x-show="expanded" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="max-h-[55dvh] overflow-y-auto border-t border-navy-100 px-2 py-2">
                    @php $topLevelSeen = 0; @endphp
                    @foreach ($items as $item)
                        @php
                            $isSub = ($item['level'] ?? 2) >= 3;
                            $number = $isSub ? null : ++$topLevelSeen;
                        @endphp
                        <li x-data="{ id: @js($item['id']) }">
                            <a href="#{{ $item['id'] }}" data-no-smooth
                               @click="active = id; expanded = false"
                               class="flex items-start gap-2.5 rounded-xl px-2.5 py-2 text-sm leading-snug transition-colors duration-200 {{ $isSub ? 'pl-8' : '' }}"
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
    </div>
@endif
