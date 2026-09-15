@props(['partners' => null, 'clients' => null])

@php
    $partners = $partners ?? collect();
    $clients = $clients ?? collect();
    $isId = app()->getLocale() === 'id';

    // Repeat clients so a single marquee "half" fills the viewport, then render
    // two identical halves for a seamless, never-ending -50% loop.
    $repeat = function ($col, $min = 16) {
        if (! $col || $col->isEmpty()) {
            return collect();
        }
        $times = max(2, (int) ceil($min / max(1, $col->count())));
        $out = collect();
        for ($i = 0; $i < $times; $i++) {
            $out = $out->concat($col);
        }
        return $out;
    };

    $klien = $repeat($clients, 16);

    // Mitra ditata tiga logo per baris dan tetap dua baris saat jumlahnya
    // bertambah: kolomnya yang tumbuh, sehingga pelatnya mengecil sendiri tanpa
    // perlu diatur ulang. Minimal 3 supaya empat mitra sekarang tidak melar
    // menjadi dua pelat raksasa, dan dibatasi 6 karena di bawah itu logonya
    // sudah tidak terbaca — lewat dua belas mitra, barisnya bertambah.
    $partnerCols = min(6, max(3, (int) ceil($partners->count() / 2)));
@endphp

@if ($partners->isNotEmpty() || $clients->isNotEmpty())
    {{-- ONE unified section (Mitra + Klien) on a single background image, using the same
         gradient treatment as the "Alasan Memilih Kami / Keunggulan" band. --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <img src="https://images.unsplash.com/photo-1600880292089-90a7e086ee0c?auto=format&fit=crop&w=1920&q=80"
             alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-navy-950/82"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/75 via-navy-950/45 to-navy-950/88"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-20"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        {{-- ===================== MITRA — centered; logo box on top, name + reg. number below ===================== --}}
        @if ($partners->isNotEmpty())
            <div class="relative container pt-14 md:pt-20 {{ $clients->isEmpty() ? 'pb-14 md:pb-20' : 'pb-10 md:pb-12' }}">
                {{-- Rata kiri, mengikuti pola judul seksi di
                     halaman lain. Judul di tengah membuat seksi ini terasa
                     berdiri sendiri, padahal ia bagian dari satu situs. --}}
                <div class="max-w-2xl" data-aos="fade-up">
                    <h2 class="text-display-lg font-semibold text-white text-balance">{{ $isId ? 'Mitra Kami' : 'Our Partners' }}</h2>
                    <p class="mt-5 text-lg leading-relaxed text-navy-100 text-pretty">
                        {{ $isId
                            ? 'Saatnya naik level bersama sertifikasi kompetensi resmi BNSP.'
                            : 'Time to level up with official BNSP competency certification.' }}
                    </p>
                </div>

                {{-- Tanpa kartu kaca. Pelat putih tempat logonya sudah menjadi
                     wadahnya sendiri; membungkusnya lagi dengan panel buram
                     hanya menumpuk bingkai.

                     Tiga logo per baris, dan tetap dua baris ketika mitranya
                     bertambah: yang tumbuh jumlah kolomnya, sehingga pelatnya
                     mengecil sendiri. max-w-4xl menahan lebarnya supaya pelat
                     tidak melar selebar halaman saat mitranya masih sedikit. --}}
                <div class="mitra-grid mt-12 max-w-4xl gap-x-5 gap-y-8 md:mt-14 md:gap-x-6" style="--mitra-cols: {{ $partnerCols }};">
                    @foreach ($partners as $partner)
                        <x-partner-plate :partner="$partner" data-aos="fade-up" data-aos-delay="{{ ($loop->index % $partnerCols) * 70 }}" />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ===================== KLIEN — clean white logo band: the logos' transparent
             backgrounds blend in (no boxes), shown in their own colours ===================== --}}
        @if ($clients->isNotEmpty())
            <div class="relative bg-white pb-11 pt-10 md:pb-12 md:pt-12" data-aos="fade-up">
                <div class="container flex flex-col gap-6 md:flex-row md:items-center md:gap-10">
                    {{-- Left: heading + description --}}
                    <div class="shrink-0 md:w-60 lg:w-72">
                        {{-- h2, bukan h3: klien dan mitra adalah dua kelompok yang setara.
                             Sebagai h3 ia terbaca seolah bagian dari "Mitra Kami". --}}
                        <h2 class="font-display text-2xl font-bold text-navy text-balance md:text-3xl">{{ $isId ? 'Klien Kami' : 'Our Clients' }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            {{ $isId ? 'Klien yang telah menggunakan layanan kami.' : 'Clients who have used our services.' }}
                        </p>
                    </div>
                    {{-- Right: logos run left in their own colours. Fade both edges (clean on
                         mobile where it stacks full-width; near the heading on desktop). --}}
                    <div class="mask-fade-x relative w-full min-w-0 flex-1 overflow-hidden">
                        <div class="flex w-max items-center gap-10 animate-marquee [will-change:transform] md:gap-14">
                            @for ($h = 0; $h < 2; $h++)
                                @foreach ($klien as $client)
                                    @if ($client->logo)
                                        {{-- Logonya transparan, jadi menyatu dengan pita putih tanpa
                                             perlu kotak. Ditampilkan dengan warna aslinya: merek
                                             seperti ini justru dikenali lewat warnanya. --}}
                                        <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy"
                                             class="h-10 w-auto max-w-[150px] shrink-0 object-contain transition duration-300 hover:scale-105 md:h-12"
                                             aria-hidden="{{ $h ? 'true' : 'false' }}">
                                    @else
                                        <span class="shrink-0 text-base font-semibold text-slate-500 transition hover:text-navy" aria-hidden="{{ $h ? 'true' : 'false' }}">{{ $client->name }}</span>
                                    @endif
                                @endforeach
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endif
