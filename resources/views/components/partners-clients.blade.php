@props(['partners' => null, 'clients' => null])

@php
    $partners = $partners ?? collect();
    $clients = $clients ?? collect();
    $isId = app()->getLocale() === 'id';

    // Logo klien diulang sampai satu "separuh" rel penuh memenuhi layar, lalu
    // dua separuh yang identik dirender berurutan supaya putaran -50% tidak
    // pernah menyisakan celah kosong.
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

    // Dua jalur berlawanan arah. Jalur kedua dimulai dari tengah daftar supaya
    // kedua baris tidak menampilkan logo yang sama bersebelahan.
    $klienAtas = $klien;
    $klienBawah = $klien->slice((int) floor($klien->count() / 2))->concat($klien->take((int) floor($klien->count() / 2)))->values();
@endphp

@if ($partners->isNotEmpty() || $clients->isNotEmpty())
    {{-- Satu seksi gelap untuk Mitra dan Klien sekaligus, berlatar kota yang
         digambar dari kode (lihat <x-skyline />). Keduanya menjawab pertanyaan
         yang sama, "siapa yang berdiri di belakang sertifikat ini", jadi
         memisahkannya jadi dua seksi membuat jawabannya terbaca dua kali. --}}
    <section class="relative overflow-hidden text-white">
        <x-skyline />

        <div class="relative container pt-20 md:pt-28 lg:pt-32">
            {{-- Judul di kiri, kalimat penjelas di kanan dan dirata-bawah
                 dengannya: keduanya satu pernyataan yang dipecah, bukan judul
                 lalu paragraf di bawahnya. --}}
            <div class="grid gap-8 lg:grid-cols-[1fr_420px] lg:items-end lg:gap-20">
                <div data-aos="fade-up">
                    <p class="font-display text-xs font-semibold uppercase tracking-[0.28em] text-navy-400">
                        {{ $isId ? 'Mitra Kerja Sama' : 'Collaborating Partners' }}
                    </p>
                    {{-- Baris kedua dimiringkan dan diturunkan bobotnya. Miring
                         saja di bawah huruf setebal ini nyaris tidak terbaca;
                         perbedaan bobotnya yang memisahkan kedua baris. --}}
                    <h2 class="mt-5 font-display text-[clamp(2.25rem,4.4vw,3.5rem)] font-extrabold leading-[1.06] tracking-[-0.04em] text-white">
                        {{ $isId ? 'Sertifikasi resmi,' : 'Official certification,' }}<br>
                        <em class="font-light italic text-navy-200">{{ $isId ? 'dijamin lembaga terpercaya.' : 'backed by trusted bodies.' }}</em>
                    </h2>
                </div>

                @if ($partners->isNotEmpty())
                    <p class="text-[17px] leading-[1.7] text-navy-100 text-pretty" data-aos="fade-up" data-aos-delay="50">
                        {{ $isId
                            ? 'Empat Lembaga Sertifikasi Profesi berlisensi BNSP mendampingi setiap uji kompetensi kami, sehingga sertifikat Anda sah dan diakui.'
                            : 'Four BNSP-licensed certification bodies oversee every assessment we run, so your certificate is valid and recognised.' }}
                    </p>
                @endif
            </div>

            @if ($partners->isNotEmpty())
                <div class="mitra-board mt-14 md:mt-16" data-aos="fade-up">
                    @foreach ($partners as $partner)
                        @php
                            $name = trim($partner->name);
                            $href = trim((string) ($partner->website_url ?? ''));
                            // Kartunya jadi tautan hanya bila mitranya punya alamat
                            // situs. Tautan yang tidak menuju ke mana pun tetap
                            // disinggahi papan tik dan tidak melakukan apa-apa.
                            $tag = $href !== '' ? 'a' : 'div';
                        @endphp
                        <{{ $tag }} class="mitra-card flex flex-col gap-5 p-5 outline-none focus-visible:ring-2 focus-visible:ring-navy-200 focus-visible:ring-offset-2 focus-visible:ring-offset-navy-950"
                            @if ($href !== '') href="{{ $href }}" target="_blank" rel="noopener" @endif>

                            {{-- flex + h-full/w-full pada gambarnya, bukan grid + max-h-full.
                                 Persen tinggi pada butir grid yang barisnya auto tidak selalu
                                 punya acuan pasti, dan di situ max-height jatuh ke none: logo
                                 yang tingginya mendekati lebarnya lalu tampil seukuran
                                 aslinya dan meluber menimpa nama di bawahnya. Flex berjarak
                                 pasti menyelesaikannya, dan overflow-hidden menjaganya
                                 sekalipun ada logo dengan perbandingan tak terduga. --}}
                            <div class="mitra-logo-box relative flex h-[150px] items-center justify-center overflow-hidden rounded-[14px] bg-white p-[22px]">
                                @if ($partner->logo)
                                    {{-- alt kosong dengan sengaja: nama mitranya tertulis
                                         tepat di bawah logo ini. Diisi, namanya terbaca dua
                                         kali oleh pembaca layar. --}}
                                    <img src="{{ Storage::url($partner->logo) }}" alt="" loading="lazy" decoding="async"
                                         class="mitra-logo h-full w-full object-contain">
                                @else
                                    <span class="font-display text-4xl font-semibold text-navy-200" aria-hidden="true">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</span>
                                @endif
                            </div>

                            <div class="relative flex flex-1 flex-col">
                                <p class="min-h-10 font-display text-sm font-semibold leading-[1.45] text-white text-balance">{{ $name }}</p>

                                <div class="mt-auto flex items-center justify-between gap-3 pt-3">
                                    @if ($partner->registration_number)
                                        <span class="font-mono text-xs font-medium tracking-[0.1em] text-navy-400">{{ trim($partner->registration_number) }}</span>
                                    @else
                                        <span></span>
                                    @endif

                                    @if ($href !== '')
                                        <span class="mitra-arrow grid h-[30px] w-[30px] shrink-0 place-items-center rounded-full border border-navy-200/50 text-navy-200" aria-hidden="true">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M5 11 11 5M6.5 5H11v4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </{{ $tag }}>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Pita klien putih, menempel di dasar seksi gelapnya. Logo klien
             transparan dan berwarna asli, jadi latar putih memang tempatnya:
             merek seperti ini dikenali justru lewat warnanya. --}}
        @if ($clients->isNotEmpty())
            <div class="relative mt-20 bg-white py-10 md:mt-24 md:py-11" data-aos="fade-up">
                {{-- .container, bukan padding yang ditulis sendiri. Yang lama memakai
                     px-5 sampai xl:pl-12, jadi di layar lebar isinya mulai 48px dari
                     tepi jendela sementara seluruh seksi lain mulai dari tepi
                     container yang maksimal 1280px dan dipusatkan. Di monitor 1920px
                     selisihnya lebih dari 300px, dan pita ini satu-satunya yang
                     keluar dari garis itu. --}}
                <div class="container flex flex-col gap-7 md:flex-row md:items-center md:gap-10">
                    <div class="shrink-0 md:w-56 lg:w-64">
                        {{-- h2, bukan h3: klien dan mitra dua kelompok yang setara.
                             Sebagai h3 ia terbaca seolah bagian dari Mitra. --}}
                        <h2 class="font-display text-[26px] font-bold leading-tight text-navy">
                            {{ $isId ? 'Klien ' : 'Our ' }}<em class="font-normal italic text-navy-600">{{ $isId ? 'Kami' : 'Clients' }}</em>
                        </h2>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600 md:whitespace-nowrap">
                            {{ $isId ? 'Dipercaya BUMN & perusahaan swasta' : 'Trusted by state and private companies' }}
                        </p>
                    </div>

                    {{-- Dua jalur berlawanan arah. Satu jalur membuat logo yang sama
                         lewat dalam urutan yang sama terus-menerus; dua jalur yang
                         saling berpapasan membuat deretnya tidak pernah terbaca
                         sebagai pengulangan.

                         Tanpa garis pemisah tegak di antara judul dan rel ini.
                         Tingginya mengikuti dua jalur logo sementara judulnya jauh
                         lebih pendek, jadi garis itu menjulang sendirian di tengah
                         pita putih tanpa menyentuh apa pun di atas atau di bawahnya.
                         Luruhnya topeng di tepi kiri rel sudah memisahkan keduanya. --}}
                    <div class="mask-fade-x relative w-full min-w-0 flex-1 overflow-hidden">
                        {{-- Lama putarannya ditulis di sini, bukan memakai 40s dan 48s
                             bawaan animate-marquee: relnya memuat 26 logo selebar 228px,
                             jadi separuh jalurnya hampir 6000px dan pada 40 detik ia
                             melaju 148 piksel per detik — terlalu cepat untuk logo yang
                             memang dimaksudkan untuk dibaca. Pada 100 dan 115 detik ia
                             turun ke sekitar 55 piksel per detik.

                             Ditulis sebagai properti sembarang supaya tidak mengubah
                             animate-marquee yang dipakai rel lain di situs ini. --}}
                        @foreach ([['klien' => $klienAtas, 'anim' => 'animate-marquee [animation-duration:100s]'], ['klien' => $klienBawah, 'anim' => 'animate-marquee-reverse [animation-duration:115s]']] as $jalur)
                            <div class="group/rail flex w-max items-center {{ $jalur['anim'] }} [will-change:transform] hover:[animation-play-state:paused] {{ $loop->first ? '' : 'mt-5' }}">
                                @for ($h = 0; $h < 2; $h++)
                                    @foreach ($jalur['klien'] as $client)
                                        <span class="mr-12 flex h-14 w-[180px] shrink-0 items-center justify-center" aria-hidden="{{ $h ? 'true' : 'false' }}">
                                            @if ($client->logo)
                                                <img src="{{ Storage::url($client->logo) }}" alt="{{ $h ? '' : $client->name }}" loading="lazy" decoding="async"
                                                     class="max-h-full max-w-full object-contain opacity-75 grayscale-[.4] transition duration-300 hover:scale-110 hover:opacity-100 hover:grayscale-0">
                                            @else
                                                <span class="text-base font-semibold text-slate-500 transition duration-300 hover:text-navy">{{ $client->name }}</span>
                                            @endif
                                        </span>
                                    @endforeach
                                @endfor
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </section>
@endif
