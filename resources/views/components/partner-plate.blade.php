@props(['partner', 'tone' => 'dark'])

@php
    // Dua latar, satu markah. 'dark' dipakai seksi Mitra Kami yang berdiri di
    // atas foto gelap, 'light' di halaman Kemitraan yang berlatar putih. Yang
    // berbeda hanya warna dan tinggi kotaknya, jadi menyalin seluruh markahnya
    // ke halaman kedua berarti tiap perubahan harus dikerjakan dua kali.
    $light = $tone === 'light';

    $box = $light
        ? 'h-28 border border-navy-100 bg-neutral-50 group-hover:border-sky-200 group-hover:bg-white group-hover:shadow-lift md:h-32'
        : 'aspect-[3/2] bg-white shadow-[0_2px_8px_rgba(2,12,27,0.25)]';

    $logoSize = $light ? 'max-h-14 md:max-h-16' : 'max-h-full';
@endphp

{{-- h-full + flex-col: sel grid sama tinggi, jadi nomor registrasi bisa
     didorong ke dasar sel dengan mt-auto. Tanpa itu letaknya ikut naik-turun
     mengikuti nama mitra yang ada yang satu baris dan ada yang dua. --}}
<div {{ $attributes->merge(['class' => 'group flex h-full flex-col']) }}>
    <div class="flex w-full items-center justify-center overflow-hidden rounded-xl p-4 transition duration-500 ease-out-soft group-hover:-translate-y-1.5 {{ $box }}">
        @if ($partner->logo)
            <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-w-full object-contain {{ $logoSize }}">
        @else
            <span class="px-2 text-center font-display text-xs font-semibold leading-tight text-navy">{{ $partner->name }}</span>
        @endif
    </div>

    {{-- Nama mitra tidak dimiringkan: italic di sini mengenai data, bukan penekanan. --}}
    <p class="mt-3.5 font-display text-sm font-semibold leading-snug text-balance {{ $light ? 'text-navy' : 'text-white' }}">{{ $partner->name }}</p>
    @if ($partner->registration_number)
        <p class="mt-auto pt-1 font-mono text-[11px] tracking-tight {{ $light ? 'text-slate-500' : 'text-navy-200' }}">{{ $partner->registration_number }}</p>
    @endif
</div>
