{{--
    Papan penilaian berikut wisudawannya. Satu gambar untuk satu gagasan:
    kompetensi diuji butir demi butir, lalu peserta lulus.

    Digambar geometris dengan goresan tebal seragam, bukan sketsa. Sketsa
    bertangan di latar halaman perusahaan terbaca amatir, bukan hangat.

    Yang bergerak hanya centangnya: ketiganya menggambar diri berurutan lalu
    terhapus dan mengulang. Papan dan orangnya diam — kalau semuanya ikut
    bergerak, tidak ada yang menjadi titik perhatian.
--}}
<svg {{ $attributes->merge(['class' => 'h-56 w-56']) }} viewBox="0 0 96 88" fill="none" aria-hidden="true">
    {{-- ── Papan jepit ── --}}
    <g stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
        <rect x="6" y="12" width="52" height="68" rx="5"/>
        <rect x="23" y="5" width="18" height="12" rx="3.5"/>
    </g>

    {{-- Baris butir penilaian: garis isian di kiri, centang di kanan. --}}
    <g stroke="currentColor" stroke-width="2.2" stroke-linecap="round" opacity="0.45">
        <path d="M15 32h16"/>
        <path d="M15 46h16"/>
        <path d="M15 60h16"/>
    </g>

    {{-- Centang. Jeda berbeda supaya tercentang satu per satu, bukan serentak. --}}
    <g stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" fill="none">
        <path class="edu-tick" style="animation-delay: 0s" d="m38 32 4 4 8-9"/>
        <path class="edu-tick" style="animation-delay: .45s" d="m38 46 4 4 8-9"/>
        <path class="edu-tick" style="animation-delay: .9s" d="m38 60 4 4 8-9"/>
    </g>

    {{-- ── Wisudawan ── --}}
    <g stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
        {{-- Toga: belah ketupat dengan jumbai pendek di tepi kanan --}}
        <path d="m74 22-16 7 16 7 16-7z"/>
        <path d="M90 29v7"/>
        {{-- Kepala --}}
        <circle cx="74" cy="48" r="8"/>
        {{-- Bahu --}}
        <path d="M60 80a14 14 0 0 1 28 0"/>
    </g>
</svg>
