@props(['partner'])

{{-- Satu pelat logo mitra. Dipakai dua kali oleh x-partners-clients: pada
     susunan diam saat mitranya muat dua baris, dan di dalam pita berjalan
     saat jumlahnya lebih banyak. --}}
<div {{ $attributes->merge(['class' => 'group']) }}>
    <div class="flex aspect-[3/2] w-full items-center justify-center overflow-hidden rounded-xl bg-white p-4 shadow-[0_2px_8px_rgba(2,12,27,0.25)] transition-transform duration-500 ease-out-soft group-hover:-translate-y-1.5">
        @if ($partner->logo)
            <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-full max-w-full object-contain">
        @else
            <span class="px-2 text-center font-display text-xs font-semibold leading-tight text-navy">{{ $partner->name }}</span>
        @endif
    </div>

    {{-- Nama mitra tidak dimiringkan: italic di sini mengenai data, bukan penekanan. --}}
    <p class="mt-3.5 font-display text-sm font-semibold leading-snug text-white text-balance">{{ $partner->name }}</p>
    @if ($partner->registration_number)
        <p class="mt-1 font-mono text-[11px] tracking-tight text-navy-200">{{ $partner->registration_number }}</p>
    @endif
</div>
