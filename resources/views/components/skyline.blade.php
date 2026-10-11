{{--
    Latar kota bergradasi biru, seluruhnya dari kode. Tanpa berkas gambar: yang
    digambar hanya siluet gedung, dan satu berkas foto untuk itu berarti satu
    permintaan jaringan tambahan, satu aset yang harus ikut dipelihara, serta
    satu gambar yang tidak pernah benar-benar tajam di semua lebar layar.

    Pemakaian:
        <section class="relative overflow-hidden text-white">
            <x-skyline />
            ... isi ...
        </section>

    Seluruh lapisnya absolute inset-0 dan pointer-events-none, jadi komponen ini
    tidak pernah menghalangi apa pun yang diletakkan sesudahnya. Pembungkusnya
    harus position: relative dan sebaiknya overflow-hidden.

    Props:
      glow  — lingkaran cahaya di sudut kanan atas (matikan bila seksinya pendek
              atau sudah punya sumber cahaya sendiri)
      city  — siluet gedungnya (matikan bila hanya butuh gradien dasarnya)
--}}
@props(['glow' => true, 'city' => true])

@php
    /*
     * Gedungnya dibangkitkan di sini, bukan di JavaScript seperti pada
     * rancangannya. Acaknya berbenih tetap, jadi hasilnya selalu sama persis;
     * dikerjakan di peladen, siluetnya ikut tercetak di HTML dan muncul utuh
     * sekalipun JavaScript gagal dimuat. Tidak ada pula kedipan antara halaman
     * yang baru dimuat dan saat skripnya selesai menggambar.
     *
     * Rumus acaknya linear congruential sederhana, sama dengan rancangannya,
     * supaya bentuk kotanya persis seperti yang disetujui.
     */
    $seed = 11;
    $rand = function () use (&$seed) {
        $seed = ($seed * 9301 + 49297) % 233280;

        return $seed / 233280;
    };

    $buildings = [];
    $x = -20;
    // 1440 lebar acuan; 1460 memberi satu gedung lebih supaya tepi kanannya
    // tidak pernah terputus di layar lebar.
    while ($x < 1460) {
        $w = 50 + round($rand() * 80);
        $h = 180 + round($rand() * 420);
        $buildings[] = [
            // Persen, bukan piksel: lebar seksinya berubah mengikuti layar,
            // sementara acuannya tetap 1440.
            'left' => $x / 1440 * 100,
            'width' => $w / 1440 * 100,
            'height' => $h,
            'opacity' => round(0.55 + $rand() * 0.4, 3),
        ];
        $x += $w + round($rand() * 10) - 4;
    }
@endphp

<div class="skyline-base pointer-events-none absolute inset-0" aria-hidden="true"></div>

@if ($city)
    <div class="skyline-city pointer-events-none absolute inset-x-0 bottom-0" aria-hidden="true">
        @foreach ($buildings as $b)
            <span class="skyline-building"
                  style="left:{{ round($b['left'], 4) }}%;width:{{ round($b['width'], 4) }}%;height:{{ $b['height'] }}px;opacity:{{ $b['opacity'] }}"></span>
        @endforeach
    </div>
@endif

<div class="skyline-overlay pointer-events-none absolute inset-0" aria-hidden="true"></div>

@if ($glow)
    <div class="skyline-glow pointer-events-none absolute" aria-hidden="true"></div>
@endif

<div class="pointer-events-none absolute inset-0 grain opacity-20" aria-hidden="true"></div>
