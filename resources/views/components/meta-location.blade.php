@props([
    'value' => null,
    'tone' => 'text-gold-deep',
    'size' => 'h-3 w-3',
    'separator' => true,
])

@if (filled($value))
    @if ($separator)
        <span class="text-navy-200" aria-hidden="true">·</span>
    @endif
    <span {{ $attributes->class(['inline-flex min-w-0 items-center gap-1', $tone]) }}>
        <svg class="{{ $size }} shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
        </svg>
        <span class="truncate normal-case tracking-normal">{{ $value }}</span>
    </span>
@endif
