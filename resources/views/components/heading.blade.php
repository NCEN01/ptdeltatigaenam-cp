@props([
    'lead' => '',       // upright words before the accent
    'accent' => '',     // the italic, gradient keyword
    'tail' => '',       // optional upright words after the accent
    // Dua nilai saja, dipilih menurut latarnya — bukan satu per pemanggil:
    //   latar terang → text-sky-700            (bawaan)
    //   latar gelap  → text-gradient-white-navy
    // Sebelumnya ada empat nilai berbeda di situs ini, termasuk text-gradient
    // yang berwarna arang dan membuat aksennya tidak terlihat sama sekali.
    'accentClass' => 'text-sky-700',
])

{{--
    Reusable bold-upright + italic-gradient heading.
    Usage: <x-heading lead="Wawasan &" accent="artikel" tail="terbaru kami"
                      class="text-display-lg font-semibold text-navy" />
--}}
{{-- Span aksennya hanya ditulis bila memang ada isinya. Judul tanpa kata yang
     dimiringkan sebelumnya tetap meninggalkan span kosong di dalam h2. --}}
<h2 {{ $attributes }}>{{ $lead }}@if ($accent !== '')@if ($lead !== '') @endif<span class="italic-accent {{ $accentClass }}">{{ $accent }}</span>@endif@if ($tail !== '') {{ $tail }}@endif</h2>
