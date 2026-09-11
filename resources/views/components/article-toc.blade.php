@props([
    'items' => [],
])

@php
    $isId = app()->getLocale() === 'id';
    $ids = array_column($items, 'id');
    $topLevelSeen = 0;
@endphp

@if (count($items))
    <nav x-data="articleToc(@js($ids))"
         aria-label="{{ $isId ? 'Daftar isi artikel' : 'Article table of contents' }}"
         class="rounded-2xl bg-navy-50/60 p-5 sm:p-6">

        {{-- The whole row is the tap target on mobile (a lone chevron is too small for a
             thumb); inert on desktop, where the list is always open anyway. --}}
        <button type="button" @click="expanded = !expanded"
                class="-my-1 flex w-full items-center justify-between gap-3 py-1 text-left lg:pointer-events-none"
                :aria-expanded="expanded ? 'true' : 'false'"
                aria-controls="toc-list">
            <span class="text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">
                {{ $isId ? 'Daftar Isi' : 'On this page' }}
            </span>

            <span class="flex items-center gap-2.5">
                <span class="font-mono text-[11px] tabular-nums text-slate-400" x-text="Math.round(progress) + '%'">0%</span>
                <span class="grid h-9 w-9 place-items-center rounded-lg text-slate-500 lg:hidden">
                    <svg class="h-4 w-4 transition-transform duration-300 ease-out-soft" :class="expanded && 'rotate-180'"
                         viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </span>
        </button>

        {{-- Headings on a progress rail --}}
        <ol id="toc-list" x-show="expanded" x-cloak class="relative mt-5 lg:!block">
            {{-- Rail track --}}
            <span class="pointer-events-none absolute bottom-2 left-[3px] top-2 w-px bg-navy-200/70" aria-hidden="true"></span>
            {{-- Reading progress fills the rail (scaleY keeps it on the GPU) --}}
            <span class="pointer-events-none absolute bottom-2 left-[3px] top-2 w-px origin-top bg-gradient-to-b from-sky-500 to-gold"
                  :style="`transform: scaleY(${progress / 100})`" aria-hidden="true"></span>

            @foreach ($items as $item)
                @php
                    $isSub = ($item['level'] ?? 2) >= 3;
                    // Only top-level entries carry a number, so they need their own counter.
                    $number = $isSub ? null : ++$topLevelSeen;
                @endphp
                <li class="relative" x-data="{ id: @js($item['id']) }">
                    {{-- data-no-smooth: the global anchor handler must not double-scroll this link. --}}
                    <a href="#{{ $item['id'] }}" data-no-smooth
                       @click="active = id; if (window.innerWidth < 1024) expanded = false"
                       class="group flex items-start gap-2.5 py-2 pr-1 text-sm leading-snug transition-colors duration-200 {{ $isSub ? 'pl-11' : 'pl-6' }}"
                       :class="active === id ? 'text-navy font-semibold' : 'text-slate-500 hover:text-navy'"
                       :aria-current="active === id ? 'true' : null">

                        {{-- Marker sitting on the rail --}}
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
@endif
