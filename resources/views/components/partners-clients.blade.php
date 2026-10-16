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

    /* Mitra bergeser hanya kalau jumlahnya melewati satu baris kisi.
       Empat adalah lebar baris di layar besar (lihat .mitra-board di app.css);
       tepat empat atau kurang, semuanya terlihat sekaligus dan deretan yang
       bergerak tidak menambah apa pun selain waktu tunggu.

       $mitraSeparuh adalah satu separuh rel; markahnya yang menggandakannya. */
    $mitraBergeser = $partners->count() > 4;

    /* $repeat tidak dipakai di sini: ia memaksa minimal dua salinan, yang
       benar untuk klien (logonya sedikit dan kecil) tetapi tidak di sini.
       Pada 25 mitra, dua salinan per separuh berarti 100 kartu dirender untuk
       rel yang hanya memperlihatkan sepuluh sekaligus. Yang dibutuhkan cuma
       cukup kartu untuk melewati layar terlebar — kartu 248px beserta jaraknya
       butuh sekitar sepuluh — dan daftar yang sudah sepanjang itu tidak perlu
       diulang sama sekali. */
    $mitraSeparuh = collect();
    if ($mitraBergeser) {
        while ($mitraSeparuh->count() < 10) {
            $mitraSeparuh = $mitraSeparuh->concat($partners);
        }
    }
    $mitraSeparuh = $mitraSeparuh->values();
@endphp

@if ($partners->isNotEmpty() || $clients->isNotEmpty())
    {{-- Satu seksi gelap untuk Mitra dan Klien sekaligus. Keduanya menjawab
         pertanyaan yang sama, "siapa yang berdiri di belakang sertifikat ini",
         jadi memisahkannya jadi dua seksi membuat jawabannya terbaca dua kali.

         Latarnya foto kota, menggantikan siluet yang sebelumnya digambar dari
         kode lewat <x-skyline />. Siluet itu dibuat saat belum ada fotonya;
         begitu fotonya ada, kota sungguhan membawa kedalaman yang tidak bisa
         ditiru beberapa persegi panjang bertumpuk. --}}
    <section class="relative overflow-hidden text-white">
        <img src="{{ asset('images/bg-mitra.webp') }}" alt="" aria-hidden="true"
             width="1916" height="821" loading="lazy" decoding="async"
             class="absolute inset-0 h-full w-full object-cover">
        {{-- Dua tabir bertumpuk. Yang mendatar menjaga judul di kiri tetap
             terbaca; yang menurun menggelapkan dasar seksi supaya pita klien
             putih di bawahnya tidak bertabrakan langsung dengan langit senja. --}}
        <div class="absolute inset-0 bg-gradient-to-r from-navy-950/90 via-navy-950/70 to-navy-900/60"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/60 via-transparent to-navy-950/75"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-20"></div>

        <div class="relative container pt-20 md:pt-28 lg:pt-32">
            {{-- Judul di kiri, kalimat penjelas di kanan dan dirata-bawah
                 dengannya: keduanya satu pernyataan yang dipecah, bukan judul
                 lalu paragraf di bawahnya. --}}
            <div class="grid gap-8 lg:grid-cols-[1fr_420px] lg:items-end lg:gap-20">
                {{-- Label, judul, dan kalimat pendukungnya memakai takaran yang sama
                     dengan seksi FAQ: .kicker-on-dark (pasangan .kicker untuk latar
                     gelap), token judul text-display-lg, dan kalimat 15px. Ukuran
                     sendiri sampai 3,5rem membuat judul ini satu-satunya yang lebih
                     besar dari judul seksi lain. --}}
                <div data-aos="fade-up">
                    <p class="kicker-on-dark">{{ $isId ? 'Mitra Kerja Sama' : 'Collaborating Partners' }}</p>
                    {{-- Baris kedua dimiringkan dan bergradasi, bobotnya tetap
                         setebal baris pertama. Gradasinya menggantikan navy-200
                         polos: perlakuan yang sama dengan baris miring di hero,
                         hanya birunya — jadi kedua judul bergradasi di situs ini
                         terbaca sebagai satu sistem, bukan dua kebetulan. --}}
                    <h2 class="mt-4 text-display-lg text-white">
                        {{ $isId ? 'Sertifikasi resmi,' : 'Official certification,' }}<br>
                        <em class="italic-accent font-extrabold text-gradient-white-gold">{{ $isId ? 'dijamin lembaga terpercaya.' : 'backed by trusted bodies.' }}</em>
                    </h2>
                </div>

                @if ($partners->isNotEmpty())
                    {{-- Jumlahnya tidak lagi disebut di kalimat ini. "Empat
                         Lembaga Sertifikasi Profesi…" benar hanya selama
                         mitranya tetap empat, dan kalimat ini tidak punya cara
                         mengetahui kapan admin menambah yang kelima — yang
                         tertinggal kemudian adalah angka yang keliru di seksi
                         yang justru sedang meyakinkan pembaca soal keabsahan. --}}
                    <p class="max-w-md text-[15px] leading-relaxed text-navy-100 text-pretty" data-aos="fade-up" data-aos-delay="50">
                        {{ $isId
                            ? 'Lembaga Sertifikasi Profesi berlisensi BNSP mendampingi setiap uji kompetensi kami, sehingga sertifikat Anda sah dan diakui.'
                            : 'BNSP-licensed certification bodies oversee every assessment we run, so your certificate is valid and recognised.' }}
                    </p>
                @endif
            </div>

            @if ($partners->isNotEmpty())
                @if ($mitraBergeser)
                    {{-- Lebih dari empat mitra: relnya bergeser sendiri ke kiri,
                         perlakuan yang sama dengan pita klien di bawah seksi ini.

                         Sampai empat, kisinya diam — dan itu memang yang
                         diinginkan: keempat kartunya muat utuh dalam satu baris,
                         jadi tidak ada yang tersembunyi, dan menggeser deretan
                         yang sudah terlihat seluruhnya hanya membuat pembaca
                         menunggu logo yang sudah dibacanya kembali lewat.

                         Dua separuh identik dirender berurutan supaya putaran
                         -50% tidak pernah menyisakan celah kosong, sama seperti
                         pita klien. --}}
                    <div class="glass-surface mitra-shelf mt-14 md:mt-16" data-aos="fade-up">
                        <div class="mask-fade-x overflow-hidden">
                            {{-- Lama putarannya ditulis sendiri, bukan 40s bawaan
                                 animate-marquee: separuh relnya sekitar 2700px, dan
                                 pada 40 detik ia melaju 67 piksel per detik — terlalu
                                 cepat untuk nama lembaga beserta nomor registrasinya
                                 yang memang dimaksudkan untuk dibaca. --}}
                            <div class="mitra-rail animate-marquee [animation-duration:72s] [will-change:transform] hover:[animation-play-state:paused]">
                                {{-- Digandakan di markah, pola yang sama dengan pita
                                     klien di bawah: animate-marquee menggeser -50%,
                                     jadi isi relnya harus dua separuh identik supaya
                                     putarannya tidak pernah memperlihatkan celah.
                                     aria-hidden pada separuh kedua — pembaca layar
                                     tidak perlu mendengar daftar mitra dua kali. --}}
                                @for ($h = 0; $h < 2; $h++)
                                    @foreach ($mitraSeparuh as $partner)
                                        <x-mitra-card :partner="$partner"
                                                      class="mitra-rail-card"
                                                      aria-hidden="{{ $h ? 'true' : 'false' }}" />
                                    @endforeach
                                @endfor
                            </div>
                        </div>
                    </div>
                @else
                    <div class="glass-surface mitra-board mt-14 md:mt-16" data-aos="fade-up">
                        @foreach ($partners as $partner)
                            <x-mitra-card :partner="$partner" />
                        @endforeach
                    </div>
                @endif
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
                             Sebagai h3 ia terbaca seolah bagian dari Mitra.

                             Satu untaian teks, bukan <em> di dalam h2. Kata
                             keduanya dulu dimiringkan, diturunkan bobotnya, dan
                             diberi warna sendiri; sekarang tebal dan sewarna
                             dengan kata pertama. Begitu tampilannya sama persis,
                             <em> tidak lagi menandai apa pun — ia hanya menyuruh
                             pembaca layar menekankan satu kata tanpa alasan. --}}
                        <h2 class="font-display text-[26px] font-bold leading-tight text-navy">
                            {{ $isId ? 'Klien Kami' : 'Our Clients' }}
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
