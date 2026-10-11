{{--
    Seksi "Pelatihan Terbaru Kami". Rancangan dari Claude Design, disesuaikan
    dengan identitas situs ini.

    Pemakaian:
        <x-pelatihan-terbaru :programs="$programs" :lihat-semua-url="route('services.index')" />

    $programs = koleksi/array berisi: kategori, judul, deskripsi, gambar, url,
    harga, harga_awal (boleh kosong). HomeController memetakannya dari model
    Service, jadi angka dan gambarnya data sungguhan, bukan contoh.

    Aset dimuat lewat @vite, bukan asset(): proyek ini memakai Vite, dan
    berkas di public/ tidak ikut di-hash sehingga peramban menyimpan versi
    lamanya setelah CSS diperbarui.
--}}
@props(['programs' => [], 'lihatSemuaUrl' => '#', 'ctaAbove' => false])

@once
    @push('styles')
        @vite(['resources/css/pelatihan-terbaru.css'])
    @endpush
    @push('scripts')
        @vite(['resources/js/pelatihan-terbaru.js'])
    @endpush
@endonce

@php $isId = app()->getLocale() === 'id'; @endphp

{{-- ctaAbove: separuh bawah pita ajakan di beranda menumpang di kepala
     seksi ini, jadi ruangnya ditambah supaya judulnya tidak tertutup.
     Opsional, bukan tetap: seksi ini bisa dipakai di halaman yang tidak
     punya pita itu. --}}
<section class="pt-section @if ($ctaAbove) pt-section--cta-above @endif" aria-labelledby="pt-title">
    <div class="pt-grid-bg" aria-hidden="true"></div>
    <div class="pt-glow" aria-hidden="true"></div>

    <div class="pt-container">
        <div class="pt-head">
            <div class="pt-copy" data-aos="fade-up">
                {{-- Label kecil "Program Baru Dibuka" dihapus: judulnya sudah
                     menyebut hal yang sama, dan label huruf kapital kecil di atas
                     judul memang sudah lama ditiadakan di seluruh situs ini. --}}
                {{-- Tanpa kata miring. Aksen itu kini disimpan untuk judul utama
                     halaman dan satu seksi berlatar gelap saja; dipasang di tiap
                     seksi, ia berhenti menekankan apa pun. Kata "Kami" juga
                     dilepas: ia dulu muncul di hampir semua judul seksi tanpa
                     menambah apa-apa bagi pembaca maupun mesin pencari. --}}
                <h2 id="pt-title" class="pt-title">
                    {{ $isId ? 'Program Pelatihan Terbaru' : 'Latest Training Programmes' }}
                </h2>
                <p class="pt-lead">
                    {{ $isId
                        ? 'Program yang paling baru kami buka, lengkap dengan biayanya.'
                        : 'The programmes we opened most recently, priced up front.' }}
                </p>
            </div>

            {{-- Ikon beranimasi: papan tulis + topi toga. Seluruh animasinya
                 dipertahankan; hanya warna goresannya yang disesuaikan —
                 #0f2238 jadi navy situs #0e2237, #1f6fd6 jadi biru logo #2b83df. --}}
            <div class="pt-illus" aria-hidden="true" data-aos="fade-left" data-aos-delay="100">
                <div class="pt-orbit"><i class="pt-dot-a"></i><i class="pt-dot-b"></i></div>
                <div class="pt-pulse"></div>
                <div class="pt-disc"></div>
                <svg class="pt-icon" viewBox="0 0 640 640" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="22">
                    <ellipse class="pt-shadow" cx="320" cy="624" rx="220" ry="12" fill="#0e2237" stroke="none"/>
                    <path d="M58 197 H582 V580 H352 Q342 552 320 552 Q298 552 288 580 H58 Z" stroke="#0e2237" fill="#fff"/>
                    <path d="M320 300 V540" stroke="#0e2237"/>
                    <path d="M58 520 H248 Q298 520 320 546 Q342 520 392 520 H582" stroke="#0e2237"/>
                    <path class="pt-line" style="--d:0s"    d="M128 275 H250" stroke="#0e2237" pathLength="1"/>
                    <path class="pt-line" style="--d:.25s"  d="M128 352 H250" stroke="#0e2237" pathLength="1"/>
                    <path class="pt-line" style="--d:.5s"   d="M146 432 H250" stroke="#2b83df" pathLength="1"/>
                    <path class="pt-line" style="--d:.75s"  d="M390 275 H510" stroke="#0e2237" pathLength="1"/>
                    <path class="pt-line" style="--d:1s"    d="M390 352 H510" stroke="#2b83df" pathLength="1"/>
                    <path class="pt-line" style="--d:1.25s" d="M390 432 H492" stroke="#0e2237" pathLength="1"/>
                    <g class="pt-cap">
                        <path d="M210 160 V270 L320 318 L432 270 V160" stroke="#2b83df" fill="#fff"/>
                        <path d="M320 56 L520 128 L320 200 L120 128 Z" stroke="#2b83df" fill="#fff"/>
                        <g class="pt-tassel">
                            <path d="M470 146 V222" stroke="#0e2237" stroke-width="12"/>
                            <circle cx="470" cy="232" r="12" fill="#0e2237" stroke="none"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <div class="pt-list">
            {{-- "Lihat Semua" naik ke kanan atas, tepat di atas deretan kartu, dan
                 memakai .btn-blue milik situs — bukan tombol gelap bawaan
                 rancangannya. Dengan begitu bentuk, warna, dan perilaku sentuhnya
                 sama persis dengan tombol utama di seksi lain, alih-alih menjadi
                 satu-satunya tombol di halaman yang berperilaku sendiri. --}}
            <div class="pt-toolbar" data-aos="fade-up">
                <a href="{{ $lihatSemuaUrl }}" class="btn-blue">
                    {{ $isId ? 'Lihat Semua' : 'View All' }}
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <div class="pt-count">
                    <strong>{{ str_pad(count($programs), 2, '0', STR_PAD_LEFT) }}</strong>
                    {{ $isId ? 'program tersedia' : 'programmes available' }}
                </div>
            </div>

            {{-- Panah pindah ke sisi kiri dan kanan deretan kartu, memakai tampilan
                 yang sama persis dengan seksi Agenda Mendatang. Di atas deretan,
                 keduanya berdiri jauh dari benda yang digerakkannya; di sampingnya,
                 letaknya menjelaskan sendiri apa yang akan bergeser. --}}
            <div class="pt-rail" data-aos="fade-up" data-aos-delay="100">
                <button type="button" data-pt-prev aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}"
                        class="absolute left-0 top-1/2 z-20 hidden h-12 w-12 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 disabled:pointer-events-none disabled:opacity-40 lg:grid">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button type="button" data-pt-next aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}"
                        class="absolute right-0 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 disabled:pointer-events-none disabled:opacity-40 lg:grid">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>

            <div class="pt-track" data-pt-track>
                @foreach ($programs as $i => $p)
                    @php
                        $p = (object) $p;
                        $diskon = ! empty($p->harga_awal) && $p->harga_awal > $p->harga
                            ? round((1 - $p->harga / $p->harga_awal) * 100) : null;
                    @endphp
                    {{-- style="--i:.." dilepas: nilainya dulu hanya memberi jeda
                         berurutan pada animasi muat kartu, dan animasi itu sudah
                         tidak ada. --}}
                    <a href="{{ $p->url ?? '#' }}" class="pt-card">
                        <div class="pt-media">
                            @if (! empty($p->gambar))
                                <img src="{{ $p->gambar }}" alt="{{ $p->judul }}" loading="lazy">
                            @endif
                            @if (! empty($p->kategori))
                                <span class="pt-chip">{{ $p->kategori }}</span>
                            @endif
                            @if ($diskon)
                                <span class="pt-off">-{{ $diskon }}%</span>
                            @endif
                        </div>
                        <div class="pt-body">
                            <h3 class="pt-card-title">{{ $p->judul }}</h3>
                            <p class="pt-desc">{{ $p->deskripsi }}</p>
                            <div class="pt-foot">
                                <div>
                                    @if ($diskon)
                                        <s class="pt-old">Rp {{ number_format($p->harga_awal, 0, ',', '.') }}</s>
                                    @else
                                        <span class="pt-old">{{ $isId ? 'Mulai' : 'From' }}</span>
                                    @endif
                                    {{-- Harga 0 berarti "hubungi kami", bukan gratis. --}}
                                    <div class="pt-price">
                                        @if ($p->harga > 0)
                                            Rp {{ number_format($p->harga, 0, ',', '.') }}
                                        @else
                                            {{ $isId ? 'Hubungi kami' : 'Contact us' }}
                                        @endif
                                    </div>
                                </div>
                                {{-- Panah SVG yang sama dengan kartu portofolio dan
                                     layanan, bukan entitas &rarr;. Entitas mengikuti
                                     bentuk panah bawaan jenis huruf, jadi tebal dan
                                     sudutnya tidak pernah cocok dengan panah di
                                     kartu lain pada halaman yang sama. --}}
                                <span class="pt-go">
                                    <svg viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            </div>
        </div>
    </div>
</section>
