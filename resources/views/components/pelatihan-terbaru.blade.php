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
@props(['programs' => [], 'lihatSemuaUrl' => '#'])

@once
    @push('styles')
        @vite(['resources/css/pelatihan-terbaru.css'])
    @endpush
    @push('scripts')
        @vite(['resources/js/pelatihan-terbaru.js'])
    @endpush
@endonce

@php $isId = app()->getLocale() === 'id'; @endphp

<section class="pt-section" aria-labelledby="pt-title">
    <div class="pt-grid-bg" aria-hidden="true"></div>
    <div class="pt-glow" aria-hidden="true"></div>

    <div class="pt-container">
        <div class="pt-head">
            <div class="pt-copy">
                <div class="pt-eyebrow"><span></span>{{ $isId ? 'Program Baru Dibuka' : 'Newly Opened' }}</div>
                <h2 id="pt-title" class="pt-title">
                    {{ $isId ? 'Pelatihan Terbaru' : 'Our Latest' }} <em>{{ $isId ? 'Kami' : 'Training' }}</em>
                </h2>
                <p class="pt-lead">
                    {{ $isId
                        ? 'Program yang paling baru kami buka, lengkap dengan biayanya.'
                        : 'The programmes we opened most recently, priced up front.' }}
                </p>
                <a href="{{ $lihatSemuaUrl }}" class="pt-btn">
                    {{ $isId ? 'Lihat Semua' : 'View All' }} <span class="pt-btn-ic">&rarr;</span>
                </a>
            </div>

            {{-- Ikon beranimasi: papan tulis + topi toga. Seluruh animasinya
                 dipertahankan; hanya warna goresannya yang disesuaikan —
                 #0f2238 jadi navy situs #0e2237, #1f6fd6 jadi biru logo #2b83df. --}}
            <div class="pt-illus" aria-hidden="true">
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
            <div class="pt-toolbar">
                <div class="pt-count">
                    <strong>{{ str_pad(count($programs), 2, '0', STR_PAD_LEFT) }}</strong>
                    {{ $isId ? 'program tersedia' : 'programmes available' }}
                </div>
                <div class="pt-nav">
                    <button type="button" class="pt-arrow" data-pt-prev aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">&larr;</button>
                    <button type="button" class="pt-arrow pt-arrow--primary" data-pt-next aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">&rarr;</button>
                </div>
            </div>

            <div class="pt-track" data-pt-track>
                @foreach ($programs as $i => $p)
                    @php
                        $p = (object) $p;
                        $diskon = ! empty($p->harga_awal) && $p->harga_awal > $p->harga
                            ? round((1 - $p->harga / $p->harga_awal) * 100) : null;
                    @endphp
                    <a href="{{ $p->url ?? '#' }}" class="pt-card" style="--i:{{ $i }}">
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
                                <span class="pt-go">&rarr;</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
