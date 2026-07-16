@if ($paginator->hasPages())
    @php
        $iconL = '<svg class="h-5 w-5" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $iconR = '<svg class="h-5 w-5" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $btn = 'grid h-11 w-11 shrink-0 place-items-center rounded-full border border-navy-200 bg-white text-navy shadow-card transition hover:-translate-y-0.5 hover:border-gold hover:bg-gold hover:text-navy-950 active:scale-95';
        $btnOff = 'grid h-11 w-11 shrink-0 place-items-center rounded-full border border-navy-100 bg-white text-slate-300 cursor-default';
    @endphp
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between gap-3">
        {{-- Mobile: icon buttons only (no "Previous"/"Next" text) --}}
        <div class="flex flex-1 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="{{ $btnOff }}">{!! $iconL !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="{{ $btn }}">{!! $iconL !!}</a>
            @endif

            <span class="font-mono text-xs text-slate-500">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="{{ $btn }}">{!! $iconR !!}</a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="{{ $btnOff }}">{!! $iconR !!}</span>
            @endif
        </div>

        {{-- Tablet & desktop: numbered --}}
        <div class="hidden w-full items-center justify-between sm:flex">
            <p class="text-sm text-slate-500">
                {!! __('Showing') !!}
                <span class="font-medium text-navy">{{ $paginator->firstItem() ?? 0 }}</span>–<span class="font-medium text-navy">{{ $paginator->lastItem() ?? 0 }}</span>
                {!! __('of') !!} <span class="font-medium text-navy">{{ $paginator->total() }}</span>
            </p>
            <span class="inline-flex items-center gap-1.5">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}" class="grid h-9 w-9 place-items-center rounded-full border border-navy-100 text-slate-300">{!! $iconL !!}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}" class="grid h-9 w-9 place-items-center rounded-full border border-navy-200 text-navy transition hover:border-gold hover:bg-gold hover:text-navy-950">{!! $iconL !!}</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true" class="px-1.5 text-sm text-slate-400">{{ $element }}</span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="grid h-9 min-w-[2.25rem] place-items-center rounded-full bg-navy px-3 text-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="grid h-9 min-w-[2.25rem] place-items-center rounded-full px-3 text-sm text-slate-600 transition hover:bg-mist hover:text-navy">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}" class="grid h-9 w-9 place-items-center rounded-full border border-navy-200 text-navy transition hover:border-gold hover:bg-gold hover:text-navy-950">{!! $iconR !!}</a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}" class="grid h-9 w-9 place-items-center rounded-full border border-navy-100 text-slate-300">{!! $iconR !!}</span>
                @endif
            </span>
        </div>
    </nav>
@endif
