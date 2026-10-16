@props(['name' => null])

{{--
    Ikon kategori layanan. Goresannya kini hidup di App\Support\CategoryIcons,
    bukan di sini: panel admin perlu menawarkan daftar namanya sebagai pilihan,
    dan dua daftar terpisah pasti menyimpang — ikon baru ditambahkan di satu
    tempat lalu lupa di tempat lain, dan CMS menawarkan nama yang tidak ada
    gambarnya lalu tampil sebagai cadangan tanpa ada yang tahu kenapa.

    Digambar sebagai goresan, bukan bidang penuh, dengan tebal 1,5 seperti ikon
    lain di situs ini. Warnanya mewarisi teks induknya lewat currentColor
    sehingga satu berkas melayani latar terang maupun gelap.
--}}

<svg {{ $attributes->merge(['class' => 'h-6 w-6']) }} viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">{!! \App\Support\CategoryIcons::path($name) !!}</svg>