@props([
    'lead' => '',       // upright words before the accent
    'accent' => '',     // the italic, gradient keyword
    'tail' => '',       // optional upright words after the accent
    'accentClass' => 'text-gradient', // use text-gradient-hero on dark backgrounds
])

{{--
    Reusable bold-upright + italic-gradient heading.
    Usage: <x-heading lead="Wawasan &" accent="artikel" tail="terbaru kami"
                      class="text-display-lg font-semibold text-navy" />
--}}
<h2 {{ $attributes }}>{{ $lead }}@if ($lead !== '' && $accent !== '') @endif<span class="italic-accent {{ $accentClass }}">{{ $accent }}</span>@if ($tail !== '') {{ $tail }}@endif</h2>
