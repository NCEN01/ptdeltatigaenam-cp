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
{{-- Span aksennya hanya ditulis bila memang ada isinya. Judul tanpa kata yang
     dimiringkan sebelumnya tetap meninggalkan span kosong di dalam h2. --}}
<h2 {{ $attributes }}>{{ $lead }}@if ($accent !== '')@if ($lead !== '') @endif<span class="italic-accent {{ $accentClass }}">{{ $accent }}</span>@endif@if ($tail !== '') {{ $tail }}@endif</h2>
