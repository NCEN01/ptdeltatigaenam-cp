@props([
    'action',
    'value' => '',
    'placeholder' => '',
    'label' => '',
    'id' => 'search-q',
])

@php
    // Fragmen hasil ikut dibawa: pengiriman GET hanya mengganti komponen query
    // pada URL action, fragmennya dipertahankan. Tanpa ini pembaca terlempar ke
    // puncak halaman setiap kali mencari.
    $anchor = \App\Http\Controllers\Controller::RESULTS_ANCHOR;
@endphp

<form action="{{ $action }}#{{ $anchor }}" method="GET" role="search"
      {{ $attributes->merge(['class' => 'relative w-full sm:w-72']) }}>
    <label for="{{ $id }}" class="sr-only">{{ $label }}</label>

    <input id="{{ $id }}" type="search" name="q" value="{{ $value }}" autocomplete="off"
           placeholder="{{ $placeholder }}"
           class="w-full rounded-full border border-navy-200 bg-white py-2.5 pl-5 pr-12 text-sm text-navy placeholder:text-slate-500 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/25">

    <button type="submit" aria-label="{{ $label }}"
            class="absolute right-1.5 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-full bg-sky-600 text-white transition hover:bg-sky-700 active:scale-95">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
        </svg>
    </button>
</form>
