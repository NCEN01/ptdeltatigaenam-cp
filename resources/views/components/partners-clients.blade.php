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

    // Foto pendamping seksi Mitra, diunggah lewat CMS (Banner → penempatan "Mitra").
    // Opsional: tanpa foto, panel logonya melebar penuh dan tidak ada ruang kosong.
    $photo = \App\Models\Banner::activeNow()
        ->where('placement', 'partners')
        ->orderBy('sort_order')
        ->first();

    $photoSrc = $photoSrcset = null;
    if ($photo?->image) {
        if (str_starts_with($photo->image, 'http')) {
            $photoSrc = $photo->image;
        } else {
            $photoSrc = Storage::url($photo->image);
            $photoSrcset = app(\App\Services\MediaService::class)->srcset($photo->image, 'partner_photo');
        }
    }

    // Desktop column count grows with the number of partners, so the cards get
    // smaller automatically as more mitra are added (kept to the wireframe's 3 up to 6).
    // Berdampingan dengan foto, panelnya tinggal 7 dari 12 kolom, jadi jumlah
    // kolomnya dibatasi agar pelat logonya tidak menyempit.
    $pCount = $partners->count();
    $lgCols = match (true) {
        $pCount <= 6 => 3,
        $pCount <= 12 => 4,
        $pCount <= 20 => 5,
        default => 6,
    };
    if ($photoSrc) {
        $lgCols = min($lgCols, 3);
    }
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

        {{-- ===================== MITRA — panel putih di atas latar gelap, berisi judul,
             deskripsi, dan logo mitra; foto pendamping (opsional, dari CMS) di sampingnya.
             Panelnya yang menjadi wadah, jadi pelat logonya cukup bergaris tipis — tanpa
             itu ada dua lapis kotak menumpuk untuk satu logo. ===================== --}}
        @if ($partners->isNotEmpty())
            <div class="relative container pt-14 md:pt-20 {{ $clients->isEmpty() ? 'pb-14 md:pb-20' : 'pb-10 md:pb-12' }}">
                <div class="grid gap-6 lg:grid-cols-12 lg:gap-8">
                    <div class="{{ $photoSrc ? 'lg:col-span-7' : 'lg:col-span-12' }} rounded-3xl bg-white p-7 shadow-[0_30px_70px_-40px_rgba(2,12,27,0.95)] sm:p-9 md:p-11" data-aos="fade-up">
                        {{-- Rata kiri, mengikuti pola judul seksi di halaman lain. --}}
                        <div class="max-w-2xl">
                            <h2 class="text-display-lg font-semibold text-navy text-balance">{{ $isId ? 'Mitra Kami' : 'Our Partners' }}</h2>
                            <p class="mt-5 leading-relaxed text-slate-600">
                                {{ $isId
                                    ? 'Lembaga sertifikasi, asosiasi profesi, dan institusi pendidikan yang bekerja sama dengan kami dalam menyelenggarakan pelatihan dan uji kompetensi.'
                                    : 'Certification bodies, professional associations, and educational institutions that work with us to run training and competency assessment.' }}
                            </p>
                        </div>

                        {{-- Nama mitra tidak dimiringkan: italic di sini mengenai data,
                             bukan penekanan. --}}
                        <div class="mitra-grid mt-10 gap-x-5 gap-y-7 md:mt-12 md:gap-x-6" style="--mitra-cols: {{ $lgCols }};">
                            @foreach ($partners as $partner)
                                <div class="group" data-aos="fade-up" data-aos-delay="{{ ($loop->index % $lgCols) * 70 }}">
                                    <div class="flex aspect-[3/2] w-full items-center justify-center overflow-hidden rounded-xl border border-navy-100 bg-white p-4 transition duration-500 ease-out-soft group-hover:-translate-y-1.5 group-hover:border-sky-200 group-hover:shadow-lift">
                                        @if ($partner->logo)
                                            <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}" loading="lazy" class="max-h-full max-w-full object-contain">
                                        @else
                                            <span class="px-2 text-center font-display text-xs font-semibold leading-tight text-navy">{{ $partner->name }}</span>
                                        @endif
                                    </div>

                                    <p class="mt-3.5 font-display text-sm font-semibold leading-snug text-navy text-balance">{{ $partner->name }}</p>
                                    @if ($partner->registration_number)
                                        <p class="mt-1 font-mono text-[11px] tracking-tight text-slate-500">{{ $partner->registration_number }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if ($photoSrc)
                        {{-- lg:absolute agar tingginya mengikuti panel di sebelahnya, bukan
                             sebaliknya; di layar kecil fotonya kembali mengalir normal. --}}
                        <div class="relative overflow-hidden rounded-3xl bg-navy-900 ring-1 ring-white/10 lg:col-span-5" data-aos="fade-up" data-aos-delay="120">
                            <img src="{{ $photoSrc }}" @if ($photoSrcset) srcset="{{ $photoSrcset }}" sizes="(min-width: 1024px) 40vw, 100vw" @endif
                                 alt="{{ $photo->title }}" loading="lazy"
                                 class="h-72 w-full object-cover sm:h-96 lg:absolute lg:inset-0 lg:h-full">
                            @if (filled($photo->subtitle))
                                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-950/90 via-navy-950/55 to-transparent p-6 pt-16">
                                    <p class="text-sm font-medium leading-snug text-white text-pretty">{{ $photo->subtitle }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ===================== KLIEN — clean white logo band: the logos' white backgrounds blend
             in (no boxes), shown grayscale and revealing their colour on hover ===================== --}}
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
                    {{-- Right: logos run left, grayscale → colour on hover. Fade both edges (clean on
                         mobile where it stacks full-width; near the heading on desktop). --}}
                    <div class="mask-fade-x relative w-full min-w-0 flex-1 overflow-hidden">
                        <div class="flex w-max items-center gap-10 animate-marquee [will-change:transform] md:gap-14">
                            @for ($h = 0; $h < 2; $h++)
                                @foreach ($klien as $client)
                                    @if ($client->logo)
                                        {{-- White logo bg blends into the white band; grayscale by default, colour on hover --}}
                                        <img src="{{ Storage::url($client->logo) }}" alt="{{ $client->name }}" loading="lazy"
                                             class="h-10 w-auto max-w-[150px] shrink-0 object-contain opacity-70 grayscale transition duration-300 hover:scale-105 hover:opacity-100 hover:grayscale-0 md:h-12"
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
