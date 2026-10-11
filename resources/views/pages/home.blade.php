@php use Illuminate\Support\Facades\Storage; $isId = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.home')">

    {{-- ===================== HERO CAROUSEL ===================== --}}
    @php
        // Angka statistik dibaca lewat satu sumber bersama dengan halaman
        // Tentang Kami. Pemisahan angka dan satuannya ("500" dan "+") dikerjakan
        // di sana, bukan diulang di tiap tampilan yang memakainya.
        $companyStats = \App\Support\Company::stats();

        $slides = [];
        if (isset($heroBanners) && $heroBanners->isNotEmpty()) {
            foreach ($heroBanners as $banner) {
                $slides[] = [
                    'img_url' => str_starts_with($banner->image, 'http') ? $banner->image : Storage::url($banner->image),
                    'cat' => optional($banner->category)->name,
                    'title' => $banner->title,
                    'desc' => $banner->subtitle,
                    'link' => $banner->link_url ?: route('services.index'),
                    'btn_text' => $banner->button_text ?: ($isId ? 'Jelajahi' : 'Explore'),
                ];
            }
        } else {
            $hardcodedSlides = $isId ? [
                ['img' => 'photo-1524178232363-1fb2b075b655', 'cat' => 'Pelatihan', 'title' => 'Pelatihan SDM yang Berdampak', 'desc' => 'Program pelatihan karyawan yang relevan dan inovatif untuk meningkatkan kompetensi serta produktivitas tim Anda.'],
                ['img' => 'photo-1542744173-8e7e53415bb0', 'cat' => 'Sertifikasi', 'title' => 'Sertifikasi Profesi Terpercaya', 'desc' => 'Sertifikasi kompetensi yang diakui secara profesional untuk meningkatkan kredibilitas dan daya saing SDM Anda.'],
                ['img' => 'photo-1521737604893-d14cc237f11d', 'cat' => 'Headhunter', 'title' => 'Headhunter & Penempatan Talenta', 'desc' => 'Penyeleksian dan penempatan talenta terbaik yang sesuai dengan kebutuhan strategis perusahaan Anda.'],
            ] : [
                ['img' => 'photo-1524178232363-1fb2b075b655', 'cat' => 'Training', 'title' => 'Impactful HR Training', 'desc' => "Relevant, innovative employee training programs to boost your team's competency and productivity."],
                ['img' => 'photo-1542744173-8e7e53415bb0', 'cat' => 'Certification', 'title' => 'Trusted Professional Certification', 'desc' => "Professionally recognized competency certification to strengthen your people's credibility and edge."],
                ['img' => 'photo-1521737604893-d14cc237f11d', 'cat' => 'Headhunting', 'title' => 'Headhunting & Talent Placement', 'desc' => 'Selection and placement of top talent aligned with your strategic business needs.'],
            ];
            foreach ($hardcodedSlides as $s) {
                $slides[] = [
                    'img_url' => "https://images.unsplash.com/" . $s['img'] . "?auto=format&fit=crop&w=1920&q=80",
                    'cat' => $s['cat'],
                    'title' => $s['title'],
                    'desc' => $s['desc'],
                    'link' => route('services.index'),
                    'btn_text' => __('site.cta.explore'),
                ];
            }
        }
    @endphp
    <section class="relative">
        <div class="swiper hero-carousel" data-hero-carousel>
            <div class="swiper-wrapper">
                @foreach ($slides as $slide)
                    <div class="swiper-slide relative min-h-[100svh] overflow-hidden bg-navy-950">
                        <img src="{{ $slide['img_url'] }}"
                             alt="{{ $slide['title'] }}" {{ $loop->first ? 'loading=eager fetchpriority=high' : 'loading=lazy' }}
                             class="hero-slide-img absolute inset-0 h-full w-full object-cover">
                        {{-- Blue gradient wash: dark navy on the left, fading out before the right so the photo stays clear on that side --}}
                        <div class="absolute inset-0" style="background: linear-gradient(100deg, #123a6b 0%, rgba(22,74,125,0.92) 22%, rgba(43,131,223,0.42) 44%, rgba(43,131,223,0) 66%);"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-navy-950/30"></div>

                        <div class="container relative flex min-h-[100svh] items-center">
                            {{-- Asymmetric padding instead of py-28: the box keeps roughly the same
                                 height so the flex centring is undisturbed, but the heading, copy and
                                 CTA all sit lower — clear of the fixed header and better balanced
                                 against the stats ledger pinned to the bottom of the slide. --}}
                            <div class="hero-content max-w-2xl pb-20 pt-32 text-white md:pb-16 md:pt-40">
                                @if (! empty($slide['cat']))
                                    <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 font-mono text-[11px] font-medium uppercase tracking-normal text-white backdrop-blur">
                                        <svg class="h-3.5 w-3.5 text-gold" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.2 7.8L22 12l-7.8 2.2L12 22l-2.2-7.8L2 12l7.8-2.2z"/></svg>
                                        {{ $slide['cat'] }}
                                    </span>
                                @endif
                                @php
                                    $hw = preg_split('/\s+/', trim($slide['title']));
                                    $mid = max(1, (int) floor(count($hw) / 2));
                                    $hl = implode(' ', array_slice($hw, 0, $mid));   // baris 1 — tebal
                                    $ha = implode(' ', array_slice($hw, $mid));       // baris 2 — italic
                                @endphp
                                {{-- Hanya slide pertama yang menjadi <h1>. Seluruh slide ada di
                                     DOM sekaligus, jadi menandai ketiganya sebagai h1 membuat
                                     mesin pencari melihat tiga judul halaman yang bersaing.
                                     Sisanya h2, tampilannya tetap sama persis.

                                     Ketiga nilai clamp diturunkan ~10% bersamaan agar judul
                                     mengecil merata, bukan hanya di satu ukuran layar. --}}
                                @php $heroTag = $loop->first ? 'h1' : 'h2'; @endphp
                                <{{ $heroTag }} class="font-display font-bold leading-[1.12] text-balance [font-size:clamp(1.95rem,4.6vw,3.5rem)] {{ empty($slide['cat']) ? '' : 'mt-6' }}">
                                    <span class="block">{{ $hl }}</span>
                                    @if ($ha !== '')<span class="italic-accent block font-normal leading-[1.28] pb-[0.16em] text-gradient-white-navy">{{ $ha }}</span>@endif
                                </{{ $heroTag }}>
                                <p class="mt-6 max-w-xl text-[15px] font-normal leading-relaxed text-white/90 text-pretty md:text-lg">{{ $slide['desc'] }}</p>
                                <div class="mt-10">
                                    <a href="{{ $slide['link'] }}" class="btn border border-gold text-gold hover:border-gold hover:bg-gold hover:text-navy-950 hover:shadow-gold">{{ $slide['btn_text'] }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- arrows --}}
            <button data-hero-prev class="group absolute left-4 top-1/2 z-10 hidden h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-navy md:grid" aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                <svg class="h-5 w-5" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button data-hero-next class="group absolute right-4 top-1/2 z-10 hidden h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/25 bg-white/10 text-white backdrop-blur transition hover:bg-white hover:text-navy md:grid" aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                <svg class="h-5 w-5" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>

            {{-- Paginasi turun ke dasar. Posisi bottom-24 sebelumnya dipakai untuk
                 memberi ruang bagi papan statistik yang kini pindah ke seksi
                 Kategori Layanan; tanpa papan itu, ketinggian tersebut menyisakan
                 celah kosong di bawah titik-titiknya. --}}
            <div class="absolute inset-x-0 bottom-8 z-20 flex justify-center gap-2" data-hero-carousel-pagination></div>
        </div>
    </section>

    {{-- ===================== SERVICE CATEGORIES =====================
         Kartu kategori tidak lagi berfoto, hanya ikon. Foto kategori dulu
         diambil dari kolam gambar layanan dan banner, jadi gambar yang sama
         muncul di beberapa kategori berbeda dan tidak satu pun benar-benar
         menggambarkan isinya. Ikon tidak berpura-pura menjadi foto.
         (Kartu LAYANAN di seksi berikutnya tetap berfoto — itu memang karya
         nyata, bukan lambang kategori.)

         Coverflow 3D-nya diganti rel geser yang sama dengan rail blog di bawah:
         satu perilaku untuk dua tempat, bisa diseret, dan tidak perlu JS khusus
         yang hanya melayani satu seksi. --}}
    {{-- Sapuan cahaya sangat lembut di atas putih, alat yang sama dengan halaman
         Sertifikat dan detail Layanan. Bukan blok warna: ia hanya memberi bidang
         ini kedalaman sehingga kartu-kartunya punya tempat untuk melayang. Tanpa
         itu kartu putih di atas putih hanya bisa dibedakan lewat garis tepi, dan
         garis tepi membuatnya terbaca sebagai kotak, bukan objek. --}}
    {{-- md:pb-44 menambah ruang untuk separuh atas pita ajakan yang menumpang di
         kaki seksi ini. Utilitas pb-* mengalahkan py-* dari .section-sm karena
         lapis utilities datang sesudah lapis components. --}}
    <section class="section-sm relative overflow-hidden bg-white md:pb-44 lg:pb-48">
        <div class="pointer-events-none absolute inset-0 aurora-light opacity-70"></div>

        <div class="container relative">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-center lg:gap-12">
                {{-- Kiri: judul, kalimat pengantar, jalan ke seluruh kategori --}}
                <div class="lg:col-span-4" data-aos="fade-up">
                    {{-- Tegak tebal + satu kata miring berwarna. Di latar terang situs
                         ini biasanya menekankan lewat miring saja; di sini warnanya
                         ikut dipakai atas permintaan, dan sky-700 dipilih karena
                         mencapai 6,4:1 di atas putih — jauh di atas ambang teks. --}}
                    {{-- "Layanan Kami" dimiringkan berwarna sebagai satu kesatuan,
                         bukan dua penggal terpisah: keduanya satu frasa, dan memutus
                         warnanya di tengah frasa akan membuat jedanya terbaca di
                         tempat yang bukan jeda. --}}
                    {{-- text-display-lg, sama dengan seluruh judul seksi lain di
                         beranda. Pasangan text-4xl/md:text-5xl sebelumnya melompat
                         sekali di satu titik putus lalu diam, dan menjadikan judul
                         ini satu-satunya yang berskala sendiri. font-display dan
                         text-balance tidak ditulis: app.css sudah memberikannya ke
                         setiap h1–h4; font-bold ditulis karena memang menimpa
                         font-semibold bawaan itu. --}}
                    {{-- Kata "Kami" di judul ini diminta pemilik, jadi ia satu-satunya
                         pengecualian dari aturan judul seksi di situs ini. Sisa judul
                         beranda tetap tanpa kata itu. --}}
                    <x-heading
                        :lead="$isId ? 'Kategori' : 'Our'"
                        :accent="$isId ? 'Layanan Kami' : 'Service Categories'"
                        class="text-display-lg font-bold text-navy" />
                    <p class="mt-5 max-w-sm leading-relaxed text-slate-600 text-pretty">
                        {{ $isId
                            ? 'Dari penataan organisasi sampai uji kompetensi bersertifikat BNSP.'
                            : 'From organisational restructuring through to BNSP-certified assessment.' }}
                    </p>
                    <a href="{{ route('services.index') }}" class="btn-blue mt-8">
                        {{ $isId ? 'Lihat Semua Kategori' : 'View All Categories' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                {{-- Kanan: rel geser --}}
                @if ($categories->isNotEmpty())
                    {{-- data-hscroll-auto: barisnya bergeser sendiri tiap beberapa
                         detik. initHscroll() hanya menyalakannya bila isinya memang
                         melebihi lebar rel, jadi saat kategorinya sedikit dan sudah
                         muat seluruhnya ia diam — bergerak tanpa ada yang tersembunyi
                         hanya mengganggu. Berhenti saat kursor berada di atasnya. --}}
                    <div class="min-w-0 lg:col-span-8" data-hscroll data-hscroll-auto>
                        {{-- Panah bergaris, bukan bulatan biru terisi. Dengan tombol
                             ajakan biru di kolom kiri, dua bulatan biru di sini membuat
                             tiga benda paling mencolok berebut perhatian di satu seksi,
                             padahal hanya satu yang benar-benar tujuan pengunjung.
                             Sebagai garis tipis, panahnya tetap jelas tanpa bersaing. --}}
                        <div class="mb-6 flex items-center justify-end gap-2">
                            <button type="button" data-hscroll-prev class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 text-navy transition duration-300 hover:border-navy hover:bg-navy hover:text-white active:scale-95" aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" data-hscroll-next class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 text-navy transition duration-300 hover:border-navy hover:bg-navy hover:text-white active:scale-95" aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>

                        {{-- Kartunya sempit dengan sengaja: empat muat sekaligus di layar
                             lebar dan yang kelima mengintip di tepi, sehingga terlihat
                             bahwa barisnya masih bisa digeser tanpa perlu diberi tahu.

                             Deskripsi kategori dilepas dari kartu. Di lebar 12rem ia
                             memanjang lima sampai enam baris dan membuat kartunya tinggi
                             sekali; nama kategori sudah menjelaskan isinya, dan uraian
                             lengkapnya memang menunggu di halaman Layanan. --}}
                        {{-- rail-fade: hanya sisi yang MASIH menyembunyikan kartu yang
                             meluruh, mengikuti posisi geser yang disetel initHscroll().
                             Peluruhan permanen membuat kartu terakhir tetap terlihat pudar
                             dan terpotong padahal relnya sudah digeser sampai habis.

                             snap-proximity, bukan snap-mandatory. Dengan mandatory, browser
                             wajib berhenti tepat di awal sebuah kartu; kartu terakhir yang
                             titik awalnya berada di luar jangkauan geser membuatnya mundur
                             ke kartu sebelumnya, sehingga yang terakhir tidak pernah bisa
                             tampil utuh. Proximity tetap membantu merapikan posisi tanpa
                             menjebak ujungnya.

                             px-1/pt-2/pb-4 memberi ruang bagi bayangan kartu dan angkatnya
                             saat disentuh kursor; tanpa itu keduanya terpangkas oleh
                             overflow rel ini. -mx-1 mengembalikan ruang itu supaya kartu
                             pertama tetap sejajar dengan tepi kolomnya. --}}
                        <div data-hscroll-track class="rail-fade -mx-1 flex cursor-grab snap-x snap-proximity select-none items-stretch gap-4 overflow-x-auto scroll-smooth px-1 pb-4 pt-2 active:cursor-grabbing [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            @foreach ($categories as $cat)
                                {{-- Seluruh kartu adalah tautannya. Tombol terisi di tiap
                                     kartu akan menjadi lima tombol biru berjajar di ruang
                                     sesempit ini; teks berpanah sudah cukup menandai bahwa
                                     kartunya bisa diklik. --}}
                                {{-- Garis rambut ditambah bayangan rapat 8px. Sebelumnya
                                     bayangan saja: di atas bidang terang, bayangan lebar
                                     yang menyebar praktis lenyap dan kartunya tidak
                                     kelihatan. Yang dihindari adalah garis 1px berpasangan
                                     bayangan LEBAR — itu pola kartu hantu; bayangan rapat
                                     justru mempertegas tepinya, bukan mengaburkannya.
                                     Saat disentuh kursor barulah bayangannya melebar, di
                                     situ ia menjadi penanda keadaan. --}}
                                <a href="{{ route('services.index') }}#{{ $cat->slug }}"
                                   class="group flex w-44 shrink-0 snap-start flex-col rounded-2xl border border-navy-100 bg-white p-5 shadow-[0_2px_8px_-2px_rgba(10,42,94,0.10)] transition duration-300 ease-out-soft hover:-translate-y-1.5 hover:border-sky-200 hover:shadow-lift sm:w-48">
                                    {{-- Bulat, bukan kotak membulat: ikonnya sendiri sudah
                                         bersudut, dan lingkaran memberi bentuk yang berbeda
                                         sehingga keduanya tidak saling menyalin. --}}
                                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-navy-50 text-navy transition-colors duration-300 group-hover:bg-sky-600 group-hover:text-white">
                                        <x-category-icon :name="$cat->icon" class="h-6 w-6" />
                                    </span>

                                    <h3 class="mt-4 font-display text-[15px] font-semibold leading-snug text-navy text-balance transition-colors duration-300 group-hover:text-sky-700">{{ $cat->name }}</h3>

                                    {{-- Jumlah layanan per kategori. Angkanya sudah ikut
                                         terambil di HomeController lewat withCount('services')
                                         tetapi tidak pernah ditampilkan satu pun halaman.
                                         Ia mengisi ruang yang tadinya menganga pada kartu
                                         berjudul pendek, dan yang mengisinya keterangan
                                         betulan — bukan hiasan yang dipasang agar tidak
                                         kosong. Disembunyikan bila kategorinya masih nol. --}}
                                    @if ($cat->services_count > 0)
                                        <p class="mt-2 font-mono text-[11px] tabular-nums text-slate-500">
                                            {{ $cat->services_count }} {{ $isId ? 'layanan' : ($cat->services_count > 1 ? 'services' : 'service') }}
                                        </p>
                                    @endif

                                    {{-- Dua pembungkus: yang luar memikul mt-auto plus jarak
                                         minimum, yang dalam memikul garisnya. Kalau garis dan
                                         mt-auto ditaruh pada elemen yang sama, pada kartu
                                         tertinggi garisnya menempel persis di bawah tulisan
                                         karena margin otomatisnya tinggal nol. --}}
                                    <span class="mt-auto block pt-5">
                                        <span class="block border-t border-navy-100 pt-4">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-700 transition-colors duration-300 group-hover:text-navy">
                                                {{ $isId ? 'Selengkapnya' : 'Learn more' }}
                                                <svg class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                        </span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="lg:col-span-8 text-slate-500">{{ $isId ? 'Belum ada kategori layanan.' : 'No service categories yet.' }}</p>
                @endif
            </div>

            {{-- Papan statistik, pindahan dari dasar hero. Di sana ia menumpang di
                 atas foto di balik tirai gelap, terbaca setinggi 8px, dan menahan
                 titik paginasi naik menjauh dari tepinya. Di sini ia punya ruang
                 sendiri, angkanya bisa sebesar yang pantas, dan letaknya masuk akal:
                 tepat setelah pengunjung melihat apa saja yang ditawarkan.

                 Angkanya dibaca lewat Company::stats(), sumber yang sama dengan
                 papan angka di halaman Tentang Kami. Sebelumnya beranda membaca
                 tiga angka sendiri dan Tentang membaca empat angka lain — dua set
                 tentang perusahaan yang sama, yang akan saling bertentangan begitu
                 salah satunya diperbarui sendirian. --}}
            @if (! empty($companyStats))
                {{-- Dua kolom di ponsel, empat di layar lebar. Empat angka berjajar
                     di layar 375px menyisakan 80px per kolom; "Profesional terlatih"
                     di lebar itu pecah satu kata per baris. Dua kolom memberinya
                     ~160px, cukup untuk angka sebesar text-4xl dan label dua baris. --}}
                <div class="mt-16 grid grid-cols-2 gap-x-5 gap-y-10 border-t border-navy-100 pt-12 sm:gap-x-8 md:mt-20 lg:grid-cols-4" data-aos="fade-up">
                    @foreach ($companyStats as $i => $stat)
                        {{-- Garis pemisah mengikuti jumlah kolomnya. Di dua kolom,
                             angka ketiga memulai baris baru sehingga tidak boleh
                             bergaris kiri; di empat kolom ia berada di tengah baris
                             sehingga harus. Dihitung per indeks, bukan dengan varian
                             nth-child, supaya aturannya terbaca di satu tempat. --}}
                        <div @class([
                                'border-l border-navy-100 pl-5 sm:pl-8' => $i % 2 === 1,
                                'lg:border-l lg:border-navy-100 lg:pl-8' => $i % 2 === 0 && $i > 0,
                            ])>
                            {{-- Satuannya ("+", "%") dipisah ke span sendiri agar bisa
                                 diberi warna: angkanya terbaca sebagai besaran, satuannya
                                 sebagai keterangan. Penghitungnya dipasang pada span angka
                                 saja — JS-nya menimpa seluruh isi elemen yang dipasangi,
                                 jadi kalau satu span memuat keduanya, warnanya ikut hilang
                                 begitu animasinya mulai. --}}
                            <p class="flex items-baseline font-display text-4xl font-bold leading-none tracking-tight text-navy sm:text-5xl md:text-6xl">
                                <span class="tabular-nums" data-counter="{{ $stat['number'] }}">{{ $stat['number'] }}</span><span class="text-sky-600">{{ $stat['suffix'] }}</span>
                            </p>
                            <p class="mt-3 text-sm leading-snug text-slate-600 sm:mt-4">{{ $stat['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== PITA AJAKAN =====================
         Menumpang di garis sambungan dua seksi: separuh atas di Kategori
         Layanan, separuh bawah di Pelatihan Terbaru.

         Caranya jarak negatif setinggi separuh pitanya di atas dan di bawah,
         sehingga sumbangan pita ini pada alur halaman nol dan kedua seksi
         bersentuhan tepat di tengahnya. Ruang untuk separuh yang menumpang
         itu ditambahkan di kedua seksinya (md:pb-44 di atas, kelas
         .pt-section--cta-above di bawah), bukan dengan menggeser pitanya
         memakai transform — transform tidak menyisihkan ruang, jadi isinya
         akan tertutup.

         Hanya menumpang dari md ke atas. Di ponsel pitanya menumpuk tegak dan
         tingginya mengikuti isi, jadi jarak negatif setinggi tetap akan
         menutupi judul seksi di bawahnya. --}}
    <div class="relative z-20 my-12 md:-my-[90px]">
        <div class="container">
            <div class="cta-band flex flex-col items-start gap-7 px-6 py-8 md:min-h-[180px] md:flex-row md:items-center md:justify-between md:gap-10 md:px-14 md:py-0"
                 data-aos="fade-up">
                <div class="cta-band-stamp" aria-hidden="true"></div>

                <div class="relative">
                    {{-- Satu kata dimiringkan dan diturunkan bobotnya. Miring saja di
                         tengah huruf setebal ini nyaris tidak terbaca; perbedaan
                         bobotnya yang membuatnya menonjol. --}}
                    <h2 class="font-display text-2xl font-bold leading-[1.15] tracking-[-0.03em] text-white md:text-[clamp(1.75rem,2.4vw,2.125rem)] lg:whitespace-nowrap">
                        {{ $isId ? 'Keahlian Anda layak ' : 'Your skills deserve ' }}<em class="font-light italic">{{ $isId ? 'diakui' : 'formal' }}</em>{{ $isId ? ' secara resmi.' : ' recognition.' }}
                    </h2>
                    <p class="mt-2.5 text-pretty text-[15px] leading-relaxed text-navy-100 md:text-base lg:whitespace-nowrap">
                        {{ $isId
                            ? 'Ceritakan target Anda, lalu kami susun jalur pelatihan hingga uji kompetensinya.'
                            : 'Tell us your target, and we will map the path from training through to assessment.' }}
                    </p>
                </div>

                {{-- Pil putih berlingkaran navy, bukan .btn-blue: di atas bidang biru
                     tombol biru akan hilang ke dalam latarnya. --}}
                <a href="{{ route('contact.index') }}"
                   class="group/cta relative inline-flex shrink-0 items-center gap-3.5 rounded-full bg-white py-[7px] pl-7 pr-[7px] text-[15px] font-semibold text-navy-600 shadow-[0_10px_24px_-10px_rgba(0,0,0,0.3)] transition duration-[250ms] ease-out-soft hover:-translate-y-0.5 hover:shadow-[0_16px_30px_-10px_rgba(0,0,0,0.38)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-navy-700">
                    {{ $isId ? 'Hubungi Kami' : 'Contact Us' }}
                    <span class="grid h-[42px] w-[42px] shrink-0 place-items-center rounded-full bg-navy-600 text-white transition-transform duration-300 ease-out-soft group-hover/cta:translate-x-0.5" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </a>
            </div>
        </div>
    </div>

    {{-- ===================== PELATIHAN TERBARU =====================
         Rancangan dari Claude Design, disesuaikan dengan palet, jenis huruf,
         lebar container, dan radius situs ini. Seksi lama beserta ilustrasi
         papan penilaiannya dihapus seluruhnya, termasuk carousel Swiper-nya.

         $programs dipetakan HomeController dari model Service, jadi isinya
         layanan sungguhan dan ikut berubah saat admin menyuntingnya di CMS.

         cta-above menambah ruang di kepala seksi ini untuk separuh bawah pita
         ajakan di atasnya. --}}
    @if ($programs->isNotEmpty())
        <x-pelatihan-terbaru :programs="$programs" :lihat-semua-url="route('services.index')" cta-above />
    @endif

    {{-- ===================== WHY CHOOSE US (image band + numbered row) ===================== --}}
    @php
        // Lima nilai DELTA, dari config/company.php — sumber yang sama dengan
        // seksi Nilai Perusahaan di halaman Tentang. Ditulis ulang di sini,
        // keduanya akan menyimpang begitu salah satu disunting.
        $reasons = \App\Support\Company::values();
    @endphp
    {{-- Pita berfoto gelap, dikembalikan atas permintaan setelah sempat
         dinetralkan. Setelah seksi Kategori Layanan dan Portofolio kini terang,
         beranda ini tidak lagi bertumpuk bidang gelap seperti dulu, jadi satu
         pita gelap di sini justru memberi jeda — bukan lagi keempat kalinya. --}}
    <section class="relative overflow-hidden bg-navy-950 text-white">
        <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1920&q=80" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-navy-950/82"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/75 via-navy-950/45 to-navy-950/88"></div>
        {{-- Butir dan cahaya samar, alat yang sama dengan kepala halaman dan pita
             Mitra. Tanpa keduanya foto gelap ini rata betul dan terbaca sebagai
             bidang datar, bukan ruang. --}}
        <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
        {{-- /10, bukan /12: 12 di luar skala opasitas Tailwind sehingga kelasnya
             tidak pernah dibuat dan lingkarannya tampil tanpa warna sama sekali. --}}
        <div class="pointer-events-none absolute -left-24 top-10 h-72 w-72 rounded-full bg-sky-500/10 blur-3xl" data-parallax="0.12"></div>
        <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-cyan/10 blur-3xl" data-parallax="0.08"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
        {{-- Judul di kiri, alasannya di kanan. Sebelumnya judul di tengah lalu
             satu baris tipis di dasar pita setinggi 34rem, dengan justify-between
             menyisakan lubang kosong besar di antaranya. Dua kolom mengisi pita
             itu sebagaimana mestinya dan tidak perlu lagi dipaksa setinggi itu. --}}
        <div class="container relative grid items-start gap-12 py-20 lg:grid-cols-12 lg:gap-16 lg:py-28">
            <div class="lg:col-span-5" data-aos="fade-up">
                {{-- text-display-lg, sama dengan judul seksi lain di situs ini,
                     dan ukurannya ikut lebar layar lewat clamp. Pasangan
                     text-3xl/md:text-5xl sebelumnya melompat sekali di satu
                     titik henti lalu diam di 48px berapa pun lebar layarnya. --}}
                {{-- Aksen miring bergradasi biru-cyan, dibuat khusus untuk latar
                     gelap. Ia sempat diturunkan ke arang pekat saat pita ini
                     sempat terang; sekarang fotonya kembali, jadi gradasinya pun
                     kembali — di atas navy pekat justru warna inilah yang
                     kontrasnya paling kuat. --}}
                <x-heading
                    :lead="$isId ? 'Alasan Memilih' : 'Why Choose'"
                    :accent="$isId ? 'Kami?' : 'Us?'"
                    accent-class="text-gradient-white-navy"
                    class="text-display-lg font-semibold text-white text-balance" />
                {{-- Kalimat pengantarnya menyebut akronimnya. Tanpa itu, lima huruf
                     emas di sebelah kanan hanya terbaca sebagai hiasan; dengan itu,
                     ia jadi keterangan. --}}
                <p class="mt-6 max-w-md text-lg leading-relaxed text-navy-100 text-pretty">{{ $isId ? 'Lima nilai yang kami pegang di setiap program. Huruf depannya mengeja nama kami sendiri: DELTA.' : 'Five values we hold to in every programme. Their initials spell our own name: DELTA.' }}</p>
            </div>

            {{-- Satu daftar, bukan dua salinan. Dulu ada pita berjalan untuk layar
                 kecil dan baris tujuh kolom untuk layar lebar: isinya tertulis dua
                 kali di halaman, dan yang tersembunyi tetap ikut terkirim.

                 Tujuh kolom juga terlalu sempit. Di lebar penuh tiap kolom hanya
                 menyisakan sekitar 130 piksel, sehingga "Sertifikasi Diakui
                 Industri" pecah menjadi tiga baris.

                 Angka 01..07 dilepas: ketujuhnya setara, tidak ada yang pertama
                 atau terakhir, jadi penomorannya menjanjikan urutan yang tidak
                 pernah ada. Penggantinya centang, penanda yang tidak menjanjikan
                 urutan apa pun dan memakai bahasa yang sudah dipakai daftar
                 manfaat paket di halaman Kemitraan. --}}
            {{-- Satu kolom, bukan dua. Begitu tiap butir punya keterangan, dua
                 kolom di dalam kolom 7/12 menyisakan ~290px per butir, dan
                 kalimatnya pecah empat kata per baris. Satu kolom memberi
                 keterangannya lebar penuh dan jatuh di satu sampai dua baris.

                 Garis rambut dan centang memakai nilai latar gelap: navy-100
                 hampir tak terlihat di atas navy pekat, dan sky-600 terlalu
                 gelap di sana. --}}
            <ul class="lg:col-span-7">
                @foreach ($reasons as $i => $r)
                    <li class="group flex items-start gap-5 border-t border-white/10 py-5 md:py-6" data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 50 }}">
                        {{-- Huruf depan nilainya, bukan centang. Kelimanya berderet
                             ke bawah mengeja DELTA, jadi penanda barisnya sekaligus
                             membawa nama perusahaannya — centang hanya menyatakan
                             "ada", dan itu sudah jelas dari daftarnya sendiri.

                             Lebarnya dipatok w-10 supaya kelima huruf berdiri di
                             satu garis tegak lurus; tanpa itu, "L" yang sempit dan
                             "A" yang lebar membuat judul di sebelahnya bergeser
                             tidak rata. --}}
                        <span class="mt-0.5 w-10 shrink-0 text-center font-display text-3xl font-bold leading-none text-gold-soft md:text-4xl" aria-hidden="true">{{ mb_substr($r['title'], 0, 1) }}</span>
                        <div class="min-w-0">
                            <p class="font-display text-lg font-semibold leading-snug text-white md:text-xl">{{ $r['title'] }}</p>
                            <p class="mt-1.5 max-w-prose text-pretty text-sm leading-relaxed text-navy-100">{{ $r['desc'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- ===================== PORTFOLIO =====================
         Indeks berdaftar, bukan kisi tiga kartu. Kisi menampilkan tiga proyek
         sekaligus dan tidak satu pun sempat diceritakan; daftar yang butirnya
         membuka bergiliran memberi satu proyek seluruh panel foto di kanannya
         beserta kalimat penjelasnya.

         Keadaan aktifnya berjalan sendiri tiap lima detik, berhenti saat
         kursor masuk ke seksinya, dan berpindah saat butirnya ditunjuk,
         disorot papan tik, atau titiknya diklik.

         Seluruh isi keempat butir tetap ada di DOM dalam keadaan apa pun; yang
         berubah hanya tinggi dan kepekatannya. Jadi mesin pencari dan pembaca
         layar membaca keempat proyek itu seluruhnya, dan halaman ini tetap
         lengkap sekalipun JavaScript gagal dimuat. --}}
    @if ($portfolios->isNotEmpty())
        @php
            $portoTotal = $portfolios->count();
            $portoPad = fn (int $n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        @endphp
        <section class="section bg-white"
                 x-data="{
                     active: 0,
                     total: {{ $portoTotal }},
                     paused: false,
                     timer: null,
                     reduce: false,
                     start() {
                         clearInterval(this.timer);
                         if (this.reduce || this.total < 2) return;
                         this.timer = setInterval(() => {
                             if (! this.paused) this.active = (this.active + 1) % this.total;
                         }, 5000);
                     },
                     go(i) { this.active = i; this.start(); },
                     init() {
                         this.reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                         this.start();
                     },
                     destroy() { clearInterval(this.timer); },
                 }"
                 x-on:mouseenter="paused = true"
                 x-on:mouseleave="paused = false">
            <div class="container">
                {{-- Judul di kiri, tiga angka cakupan di kanan dan dirata-bawah
                     dengannya. Angkanya dihitung dari datanya sendiri, jadi ikut
                     bergerak saat admin menambah proyek. --}}
                <div class="grid gap-10 lg:grid-cols-[1fr_auto] lg:items-end lg:gap-16">
                    <div data-aos="fade-up">
                        {{-- text-display-lg, token judul seksi situs ini. Ukuran
                             sendiri sampai 4rem membuat judul ini satu-satunya yang
                             lebih besar dari judul seksi lain di halaman yang sama.
                             Tanpa miring dan tanpa warna kedua: pemisah antara kedua
                             barisnya cukup dikerjakan pergantian barisnya sendiri. --}}
                        <h2 class="text-display-lg text-navy">
                            {{ $isId ? 'Bukti kerja,' : 'Proof of work,' }}<br>
                            {{ $isId ? 'bukan sekadar janji.' : 'not just promises.' }}
                        </h2>
                    </div>

                    @if ($portfolioScope?->total)
                        @php
                            $scope = $isId
                                ? [[$portfolioScope->total, 'Proyek selesai'], [$portfolioScope->clients, 'Klien korporat'], [$portfolioScope->categories, 'Bidang layanan']]
                                : [[$portfolioScope->total, 'Projects delivered'], [$portfolioScope->clients, 'Corporate clients'], [$portfolioScope->categories, 'Service areas']];
                        @endphp
                        {{-- Garis pemisah dipasang di sisi kiri butir kedua dan ketiga
                             saja, bukan di kedua sisi tiap butir: dua garis
                             bersebelahan menghasilkan tebal dua kali di tengah dan
                             garis menggantung di kedua ujungnya. --}}
                        <dl class="flex shrink-0" data-aos="fade-up" data-aos-delay="50">
                            @foreach ($scope as $i => [$value, $label])
                                <div @class([
                                        'px-5 sm:px-8 first:pl-0 last:pr-0',
                                        'border-l border-navy-100' => $i > 0,
                                    ])>
                                    <dd class="font-display text-3xl font-extrabold tracking-[-0.045em] text-navy sm:text-4xl">
                                        <span class="tabular-nums" data-counter="{{ $value }}">{{ $value }}</span><span class="text-navy-500">+</span>
                                    </dd>
                                    <dt class="mt-1.5 text-[13px] font-medium text-slate-600 sm:whitespace-nowrap">{{ $label }}</dt>
                                </div>
                            @endforeach
                        </dl>
                    @endif
                </div>

                <div class="mt-14 grid items-stretch gap-10 md:mt-16 lg:grid-cols-12 lg:gap-16">
                    {{-- Panel foto. Di layar sempit ia naik ke atas daftarnya: daftar
                         empat butir yang mendahului foto membuat fotonya baru terlihat
                         setelah menggulir jauh. --}}
                    <div class="order-first lg:order-last lg:col-span-7" data-aos="fade-up" data-aos-delay="50">
                        <div class="relative aspect-[4/5] overflow-hidden rounded-[28px] bg-navy shadow-[0_40px_80px_-40px_rgba(32,95,159,0.45)] sm:aspect-[16/11] lg:aspect-auto lg:h-full lg:min-h-[620px]">
                            @foreach ($portfolios as $i => $p)
                                <div class="porto-photo absolute inset-0" :data-on="active === {{ $i }} ? '1' : '0'" aria-hidden="true">
                                    @if ($p->cover_image)
                                        <img src="{{ str_starts_with($p->cover_image, 'http') ? $p->cover_image : Storage::url($p->cover_image) }}"
                                             alt="" loading="lazy" decoding="async" class="h-full w-full object-cover">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-navy-700 to-navy-950"></div>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Dua gradasi: yang biru memberi nada seksi ini, yang gelap
                                 menjamin keterbacaan tulisan di kakinya. Dipisah supaya
                                 pekatnya bisa diatur sendiri-sendiri. --}}
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-600/60 via-navy-500/20 to-transparent"></div>
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/70 via-transparent to-navy-950/20"></div>

                            <div class="absolute inset-x-5 top-5 flex items-start justify-between gap-3 sm:inset-x-7 sm:top-7">
                                @foreach ($portfolios as $i => $p)
                                    @if ($p->category)
                                        <span class="font-display text-[11px] font-semibold uppercase tracking-[0.16em] text-white"
                                              x-show="active === {{ $i }}" x-cloak>{{ $p->category->name }}</span>
                                    @endif
                                @endforeach
                                <span class="ml-auto shrink-0 rounded-full bg-white/[0.16] px-3 py-1.5 font-mono text-[11px] text-white backdrop-blur-[10px]"
                                      x-text="String(active + 1).padStart(2, '0') + ' / {{ $portoPad($portoTotal) }}'" aria-hidden="true">{{ $portoPad(1) }} / {{ $portoPad($portoTotal) }}</span>
                            </div>

                            <div class="absolute inset-x-5 bottom-6 flex flex-col gap-4 sm:inset-x-9 sm:bottom-8 sm:flex-row sm:items-end sm:justify-between">
                                <div class="min-w-0">
                                    @foreach ($portfolios as $i => $p)
                                        <div x-show="active === {{ $i }}" x-cloak>
                                            <p class="flex flex-wrap items-center gap-x-2 text-[13px] font-medium text-navy-100">
                                                @if ($p->client_name)<span>{{ $p->client_name }}</span>@endif
                                                @if ($p->client_name && $p->project_date)<span aria-hidden="true">·</span>@endif
                                                @if ($p->project_date)<span class="tabular-nums">{{ $p->project_date->translatedFormat('Y') }}</span>@endif
                                            </p>
                                            <p class="mt-1.5 max-w-[460px] font-display text-[22px] font-bold leading-snug text-white text-balance">{{ $p->title }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    @foreach ($portfolios as $i => $p)
                                        <button type="button" x-on:click="go({{ $i }})"
                                                class="h-1 rounded-sm transition-all duration-[450ms] ease-out-soft"
                                                :class="active === {{ $i }} ? 'w-7 bg-white' : 'w-2 bg-white/45'"
                                                aria-label="{{ $isId ? 'Proyek' : 'Project' }} {{ $i + 1 }}"></button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar proyek --}}
                    <div class="flex flex-col lg:col-span-5">
                        <ol>
                            @foreach ($portfolios as $i => $p)
                                <li class="porto-item relative border-t border-navy-100" :data-on="active === {{ $i }} ? '1' : '0'">
                                    <span class="porto-bar" :class="active === {{ $i }} && ! paused ? 'porto-bar-run' : ''" aria-hidden="true"></span>

                                    <a href="{{ route('portfolio.show', $p->slug) }}"
                                       class="grid grid-cols-[2.75rem_1fr_auto] items-start gap-3 py-7 outline-none sm:grid-cols-[4rem_1fr_auto]"
                                       x-on:mouseenter="go({{ $i }})" x-on:focus="go({{ $i }})">

                                        {{-- porto-fade menyeragamkan lama dan lengkung
                                             peralihan warna di seluruh butir. Tanpa itu
                                             tiap bagian memakai lengkung bawaan Tailwind
                                             sementara geseran dan bukaannya memakai
                                             lengkung lain, dan keduanya yang bergerak
                                             bersamaan dengan lengkung berbeda itulah yang
                                             terbaca kaku. --}}
                                        <span class="porto-fade font-display text-[28px] font-light italic leading-none"
                                              :class="active === {{ $i }} ? 'text-navy-600' : 'text-navy-200'" aria-hidden="true">{{ $portoPad($i + 1) }}</span>

                                        <span class="porto-shift block min-w-0">
                                            @if ($p->category)
                                                <span class="porto-fade block font-display text-[11px] font-semibold uppercase tracking-[0.16em]"
                                                      :class="active === {{ $i }} ? 'text-navy-600' : 'text-slate-500'">{{ $p->category->name }}</span>
                                            @endif
                                            <span class="porto-fade mt-1.5 block font-display text-[22px] font-bold leading-[1.25] tracking-[-0.025em] text-balance"
                                                  :class="active === {{ $i }} ? 'text-navy' : 'text-slate-500'">{{ $p->title }}</span>

                                            <span class="porto-body block">
                                                <span class="block">
                                                    <span class="mt-2.5 flex flex-wrap items-center gap-x-2 text-sm font-medium text-slate-700">
                                                        @if ($p->client_name)<span>{{ $p->client_name }}</span>@endif
                                                        @if ($p->client_name && $p->project_date)<span class="text-navy-200" aria-hidden="true">·</span>@endif
                                                        @if ($p->project_date)<span class="tabular-nums">{{ $p->project_date->translatedFormat('Y') }}</span>@endif
                                                    </span>
                                                    @if ($p->short_description)
                                                        <span class="mt-1.5 block max-w-[400px] text-sm leading-[1.65] text-slate-600">{{ $p->short_description }}</span>
                                                    @endif
                                                </span>
                                            </span>
                                        </span>

                                        {{-- Putarannya dikerjakan .porto-arrow lewat atribut
                                             data, bukan kelas -rotate-45 yang ditukar Alpine.
                                             Tailwind memutar lewat peubah --tw-rotate, dan
                                             peubah khusus tidak diinterpolasi peramban: nilai
                                             barunya langsung berlaku, jadi panahnya meloncat
                                             sementara warnanya meluncur. Itu yang paling
                                             terasa kaku di butir ini. --}}
                                        <span class="porto-arrow grid h-10 w-10 shrink-0 place-items-center rounded-full border"
                                              :data-on="active === {{ $i }} ? '1' : '0'"
                                              :class="active === {{ $i }} ? 'border-navy-500 bg-navy-500 text-white' : 'border-navy-100 bg-white text-slate-400'"
                                              aria-hidden="true">
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ol>

                        <div class="mt-auto flex flex-wrap items-center justify-between gap-4 border-t border-navy-100 pt-9">
                            {{-- .btn-blue, tombol andalan situs ini: warnanya, hovernya,
                                 dan cincin fokusnya sudah ditentukan satu kali di sana. --}}
                            <a href="{{ route('portfolio.index') }}" class="btn-blue group">
                                {{ $isId ? 'Lihat Semua Portofolio' : 'View All Portfolio' }}
                                <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <span class="font-mono text-[13px] font-medium text-slate-500"
                                  x-text="String(active + 1).padStart(2, '0') + ' / {{ $portoPad($portoTotal) }}'" aria-hidden="true">{{ $portoPad(1) }} / {{ $portoPad($portoTotal) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== TESTIMONIALS ===================== --}}
    <x-testimonial-columns :testimonials="$testimonials" />

    {{-- ===================== BERITA (panel + rel kartu) =====================
         Susunannya mengikuti rujukan yang diberikan: judul terpusat, satu panel
         berwarna di kiri, deretan kartu yang bisa digeser di kanan, dan satu
         tombol di bawah tengah. Warnanya dipindah ke navy–biru situs ini, bukan
         jingga–merah rujukannya, dan hurufnya tetap Plus Jakarta Sans + Figtree.

         Relnya tetap trek scroll-snap bawaan (data-hscroll), bukan Swiper:
         kolom ini sel grid, dan Swiper runtuh jadi kotak kosong di dalamnya.
         Kartunya <x-blog-card> yang sama dengan halaman blog. --}}
    @if ($posts->isNotEmpty())
        <section class="section relative overflow-hidden border-t border-navy-50 bg-white">
            <div class="pointer-events-none absolute inset-0 aurora-light opacity-60"></div>

            <div class="container relative">
                {{-- Kepala terpusat --}}
                <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                    {{-- Judul polos. Aksen miring berwarna disimpan untuk judul
                         utama halaman dan satu seksi berlatar gelap; dipasang di
                         tiap seksi, ia berhenti menjadi penekanan dan berubah
                         jadi pola yang mengganggu bacaan. --}}
                    <h2 class="text-display-lg text-navy">{{ $isId ? 'Berita & Informasi Terbaru' : 'Latest News & Updates' }}</h2>
                    <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-slate-600 text-pretty">
                        {{ $isId
                            ? 'Kabar pelatihan, uji kompetensi BNSP, dan kegiatan terbaru, diperbarui setiap ada yang baru.'
                            : 'News on training, BNSP competence assessments, and recent activity, updated as it happens.' }}
                    </p>
                </div>

                <div class="mt-12 grid gap-6 lg:grid-cols-12">
                    {{-- Panel kiri. bg-navy-anim dipakai supaya panel ini satu-satunya
                         bidang berwarna di seksi putih ini, sekaligus memberi kontras
                         yang di rujukannya dibawa warna jingga.

                         Mulai lg panelnya melebar ke kanan melewati kolomnya (-mr) dan
                         masuk ke bawah deretan kartu, persis seperti rujukannya. Sisi
                         kanannya dibuat lurus di ukuran itu: tepi lurus yang sesekali
                         mengintip di sela dua kartu saat rel bergeser terbaca sebagai
                         panel yang berlanjut di belakang, sementara sudut membulat
                         akan terbaca sebagai bentuk asing yang muncul-hilang.

                         pr-nya dilebihkan sebesar bagian yang tertimpa supaya
                         tulisannya berhenti sebelum kartu, bukan tersembunyi di
                         baliknya.

                         Lima kolom di lg, empat di xl. Lebar isi panel ini tidak
                         pernah bisa melebihi lebar kolomnya dikurangi padding kiri,
                         berapa pun -mr dan pr-nya disetel — keduanya saling
                         meniadakan. Di lg dengan empat kolom isinya tinggal 272px
                         dan judul 24px-nya pecah jadi lima baris; dengan lima kolom
                         menjadi 354px. Di xl empat kolom sudah memberi 352px. --}}
                    <div class="lg:col-span-5 xl:col-span-4" data-aos="fade-up">
                        <div class="relative h-full overflow-hidden rounded-3xl bg-navy-anim p-7 text-white shadow-lift sm:p-8 lg:-mr-20 lg:rounded-r-none lg:pr-20 xl:-mr-24 xl:pr-24">
                            <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
                            <div class="pointer-events-none absolute -right-14 -top-14 h-44 w-44 rounded-full bg-sky-400/25 blur-3xl"></div>
                            {{-- Tirai gelap tipis. bg-navy-anim menyapukan pita terang
                                 #2b83df melintasi panel tiap 22 detik, dan di bawah pita
                                 itu teks putih transparan turun sampai 2,4:1. Tirai ini
                                 menahan dasarnya tetap gelap sehingga teksnya lolos 4,5:1
                                 di sepanjang sapuan, tanpa mematikan gerak gradasinya. --}}
                            <div class="pointer-events-none absolute inset-0 bg-navy-950/35"></div>

                            <div class="relative flex h-full flex-col">
                                {{-- Lencana ikon dengan cincin putus-putus yang berputar
                                     pelan. Satu-satunya gerak di panel ini, dan memakai
                                     animasi yang memang sudah ada di situs. --}}
                                <span class="relative grid h-14 w-14 shrink-0 place-items-center">
                                    <span class="absolute inset-0 animate-spin-slow rounded-full border border-dashed border-white/30"></span>
                                    <span class="grid h-11 w-11 place-items-center rounded-full bg-white text-navy shadow-lift">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3.25" y="5.25" width="13" height="13.5" rx="1.75"/>
                                            <path d="M16.25 9.5h3.25a1.25 1.25 0 0 1 1.25 1.25V17a1.75 1.75 0 0 1-1.75 1.75h-2.75"/>
                                            <path d="M6.5 9h6.5M6.5 12h6.5M6.5 15h4"/>
                                        </svg>
                                    </span>
                                </span>

                                <h3 class="mt-6 text-xl leading-snug sm:text-2xl">
                                    {{ $isId
                                        ? 'Informasi terkini seputar pelatihan dan sertifikasi'
                                        : 'The latest on our training and certification' }}
                                </h3>
                                <p class="mt-3 text-sm leading-relaxed text-white/85 text-pretty">
                                    {{ $isId
                                        ? 'Jadwal kelas, kabar kegiatan, dan hal yang perlu Anda tahu sebelum ikut uji kompetensi.'
                                        : 'Class schedules, event news, and what is worth knowing before you sit an assessment.' }}
                                </p>

                                {{-- Tanggal terbit tulisan terbaru — tanggal sungguhan dari
                                     CMS, jadi panel ini ikut menua kalau blognya berhenti
                                     diisi, alih-alih menjanjikan hal yang tidak terjadi. --}}
                                @if ($posts->first()->published_at)
                                    <div class="mt-auto flex items-center gap-2 pt-8 font-mono text-[11px] uppercase tracking-wider text-white/85">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
                                        {{ $isId ? 'Terbit terakhir' : 'Last published' }}
                                        {{ $posts->first()->published_at->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Rel kanan. data-hscroll-auto: bergeser sendiri tiap beberapa
                         detik, berhenti saat kursor di atasnya, dan hanya menyala bila
                         memang ada kartu yang tersembunyi. Penandanya dipasang di kolom
                         ini saja — kalau dipasang di grid, menunjuk panel kiri pun ikut
                         menghentikan gesernya. --}}
                    {{-- lg:self-center — tanpa ini kolomnya ikut meregang setinggi
                         panel sementara deretan kartunya tetap setinggi isinya dan
                         menempel di atas, jadi kartunya duduk lebih tinggi dari titik
                         tengah panel dan panahnya pun ikut meleset. --}}
                    <div class="relative z-10 min-w-0 lg:col-span-7 lg:self-center xl:col-span-8" data-hscroll data-hscroll-auto data-aos="fade-up" data-aos-delay="100">
                        {{-- Panah di sisi kiri dan kanan deretan kartu, sama persis
                             dengan seksi Agenda Mendatang dan Pelatihan Terbaru.

                             Yang kiri dapat satu tambahan: cincin putih tipis. Ia
                             berdiri di atas panel navy, dan wujud sentuhnya di seksi
                             lain adalah bulatan navy — tanpa cincin, panah ini lenyap
                             ke dalam panel tepat saat disentuh. Di keadaan diam
                             cincinnya tidak terlihat karena isinya juga putih. --}}
                        <button type="button" data-hscroll-prev aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}"
                            class="absolute left-0 top-1/2 z-20 hidden h-12 w-12 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift ring-1 ring-white/80 transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 lg:grid">
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" data-hscroll-next aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}"
                            class="absolute right-0 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 lg:grid">
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>

                        {{-- snap-proximity, bukan snap-mandatory: yang terakhir pernah
                             tertahan di luar jangkauan karena titik jepretnya menarik
                             balik. -mx-1/px-1 memberi ruang bayangan kartu saat diangkat.

                             .rail-fade dilepas: tepi kiri rel ini sekarang berada di
                             atas panel navy, dan kartu yang meluruh di sana menampilkan
                             navy menembus badan kartu — bukan isyarat "masih ada lagi",
                             hanya noda. Timpaan kartunya sendiri sudah mengatakan itu. --}}
                        {{-- py-3, bukan pt-1/pb-4: ruang bayangan kartu harus sama di
                             atas dan di bawah, kalau tidak kartunya terbaca turun
                             sedikit dari tengah panel walaupun kolomnya sudah pas. --}}
                        <div data-hscroll-track class="-mx-1 flex cursor-grab snap-x snap-proximity select-none items-stretch gap-6 overflow-x-auto scroll-smooth px-1 py-3 active:cursor-grabbing [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            @foreach ($posts as $post)
                                {{-- Lebarnya pecahan dari lebar rel, bukan angka tetap:
                                     pecahan selalu habis dibagi tiga, jadi tepat tiga
                                     kartu yang tampak dan tidak pernah ada potongan
                                     kartu keempat mengintip di tepi kanan. Lebar tetap
                                     280px sempat dicoba supaya persis seukuran kartu
                                     Agenda, tapi 280px tidak pernah membagi habis lebar
                                     rel mana pun, dan sisanya muncul sebagai potongan
                                     itu.

                                     Jumlah per layar: 80% di layar kecil, 2 di sm,
                                     3 di md, lalu kembali 2 di lg dan 3 lagi di xl.
                                     Turun di lg karena di titik itu tata letaknya
                                     berpindah ke grid 12 kolom dan rel ini menyempit
                                     jadi 7/12 — tiga kartu di situ tinggal ~172px,
                                     terlalu sempit untuk gambar sampul, tanggal,
                                     judul, dan tautannya sekaligus. Di xl relnya
                                     melebar lagi dan tiga kartu mendarat di ~245px. --}}
                                <div class="min-w-0 shrink-0 basis-[80%] snap-start sm:basis-[calc((100%_-_1.5rem)/2)] md:basis-[calc((100%_-_3rem)/3)] lg:basis-[calc((100%_-_1.5rem)/2)] xl:basis-[calc((100%_-_3rem)/3)]">
                                    {{-- Tanpa paragraf ringkasan: bentuknya jadi sama
                                         persis dengan kartu Agenda — gambar, tanggal dan
                                         tempat, judul, lalu tautan. --}}
                                    <x-blog-card :post="$post" :animate="false" :excerpt="false" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Tombol tunggal di bawah tengah, menggantikan tombol yang dulu
                     menempel di kolom kiri. --}}
                <div class="mt-10 text-center" data-aos="fade-up">
                    <a href="{{ route('blog.index') }}" class="btn-blue">
                        {{ $isId ? 'Lihat Semua Berita' : 'View All News' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== INSTAGRAM UPDATES ===================== --}}
    <section class="section bg-white border-t border-navy-50">
        <div class="container">
            {{-- Judulnya dulu memakai clamp() yang ditulis sendiri dan tracking
                 sendiri, tanpa kata beraksen — satu-satunya judul seksi di beranda
                 yang tidak mengikuti token text-display-lg. Keterangannya pun
                 memakai slate-500 sementara seksi lain slate-600. --}}
            <div class="mx-auto max-w-2xl text-center mb-12" data-aos="fade-up">
                <h2 class="text-display-lg text-navy">{{ $isId ? 'Kegiatan Terbaru di Instagram' : 'Recent Activity on Instagram' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-pretty text-sm leading-relaxed text-slate-600">
                    {{ $isId
                        ? 'Dokumentasi kelas pelatihan dan uji kompetensi yang baru berjalan, di @deltatigaenam.'
                        : 'Photos from the training classes and competence assessments we have just run, at @deltatigaenam.' }}
                </p>
            </div>

            {{-- Satu bentuk kartu, satu perulangan. Sebelumnya markah yang sama
                 persis ditulis dua kali — sekali untuk $instagramUpdates dari CMS
                 dan sekali untuk contoh cadangan — sehingga tiap perbaikan warna
                 atau ukuran harus dikerjakan dua kali dan bisa lupa sebelah.
                 Datanya yang diseragamkan dulu, bukan markahnya yang digandakan. --}}
            @php
                $igCards = (isset($instagramUpdates) && $instagramUpdates->isNotEmpty())
                    ? $instagramUpdates->map(fn ($ig) => [
                        'img' => $ig->image ? (str_starts_with($ig->image, 'http') ? $ig->image : \Illuminate\Support\Facades\Storage::url($ig->image)) : null,
                        'label' => $ig->batch_label,
                        'title' => $ig->title,
                        'company' => $ig->company,
                        'date' => $ig->date_range,
                        'link' => $ig->instagram_url ?: 'https://www.instagram.com/deltatigaenam/',
                    ])
                    : collect([
                        ['img' => asset('images/certified-risk-management.png'), 'label' => 'BATCH IV', 'title' => 'Certified Risk Management', 'company' => 'PT Delta Tiga Enam', 'date' => '19 - 22 Agustus 2025', 'link' => 'https://www.instagram.com/deltatigaenam/'],
                        ['img' => asset('images/training-seminar.png'), 'label' => 'SEMINAR', 'title' => 'Pengembangan Kompetensi SDM', 'company' => 'PT Delta Tiga Enam', 'date' => '05 - 06 Agustus 2025', 'link' => 'https://www.instagram.com/deltatigaenam/'],
                    ]);
            @endphp

            <div class="mx-auto grid max-w-4xl gap-8 md:grid-cols-2">
                @foreach ($igCards as $ig)
                    {{-- .card card-hover, bukan salinan tulisan tangannya. Salinan itu
                         menulis ulang border-navy-100 dan shadow-card yang sudah jadi
                         isi .card, dan transition-all pada lapis utilitas menimpa
                         ease-out-soft milik .card — jadi dua kartu ini melandai
                         berbeda dari seluruh kartu lain di situs. --}}
                    <div class="card card-hover overflow-hidden" data-aos="{{ $loop->first ? 'fade-right' : 'fade-left' }}">
                        <div class="flex items-center justify-between border-b border-navy-50 bg-white p-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ asset('images/logodelta36.png') }}" alt="Delta Tiga Enam" class="h-6 w-6 shrink-0 rounded-lg object-contain">
                                <div class="leading-tight">
                                    <p class="text-xs font-semibold text-navy">deltatigaenam</p>
                                    {{-- slate-500 di 10px, bukan slate-400 di 9px: yang lama
                                         2,6:1, di bawah ambang 4,5:1, dan 9px terlalu kecil
                                         untuk ukuran teks terkecil di halaman ini. --}}
                                    <p class="text-[10px] text-slate-500">Original audio</p>
                                </div>
                            </div>
                            {{-- py-2 -my-1: kotak sentuhnya naik ke 36px tanpa mengubah
                                 tinggi tampilnya, karena pil 23px jauh di bawah sasaran
                                 sentuh yang nyaman di ponsel. --}}
                            <a href="{{ $ig['link'] }}" target="_blank" rel="noopener" class="-my-1 rounded bg-navy-600 px-3 py-2 text-[10px] font-semibold text-white transition-colors hover:bg-navy-700">View profile</a>
                        </div>

                        <div class="group relative aspect-[4/5] overflow-hidden bg-navy-950">
                            @if ($ig['img'])
                                <img src="{{ $ig['img'] }}" alt="{{ $ig['title'] }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 aurora opacity-60"></div>
                            @endif
                            <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-navy-950 via-navy-950/40 to-transparent p-5 pt-12 text-white">
                                @if ($ig['label'])
                                    <span class="inline-block self-start rounded bg-navy-600 px-2 py-0.5 font-mono text-[9px] uppercase tracking-wider text-white">{{ $ig['label'] }}</span>
                                @endif
                                <h3 class="mt-2 text-lg leading-tight">{{ $ig['title'] }}</h3>
                                @if ($ig['company'])<p class="mt-0.5 text-[11px] text-navy-200">{{ $ig['company'] }}</p>@endif
                                @if ($ig['date'])
                                    <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-3 font-mono text-[10px] text-white/60"><span>{{ $ig['date'] }}</span></div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-navy-50 bg-white p-3.5 text-slate-500">
                            {{-- p-2.5 -m-2.5 pada kedua ikon: bidang sentuhnya jadi 40px
                                 sementara ikonnya tetap 20px, karena dua tautan 20px yang
                                 hanya berjarak 14px mudah salah tekan di ponsel. --}}
                            <div class="flex items-center gap-1.5">
                                <a href="{{ $ig['link'] }}" target="_blank" rel="noopener" aria-label="{{ $isId ? 'Suka di Instagram' : 'Like on Instagram' }}" class="p-2.5 transition-colors hover:text-sky-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg></a>
                                <a href="{{ $ig['link'] }}" target="_blank" rel="noopener" aria-label="{{ $isId ? 'Komentar di Instagram' : 'Comment on Instagram' }}" class="p-2.5 transition-colors hover:text-sky-700"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-1.923 2.202 4.968 4.968 0 0 0 2.803-.832c.43-.284.974-.354 1.5-.178 1.12.375 2.316.577 3.561.577z"/></svg></a>
                            </div>
                            <a href="{{ $ig['link'] }}" target="_blank" rel="noopener" class="group/ig -my-2 inline-flex items-center gap-1.5 py-2 text-xs font-semibold text-sky-700 transition-colors hover:text-navy"><span>{{ $isId ? 'Lihat di Instagram' : 'View on Instagram' }}</span><svg class="h-3.5 w-3.5 transition-transform duration-300 group-hover/ig:translate-x-0.5" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== FAQ SECTION =====================
         bg-paper, bukan putih. Empat seksi putih berturut-turut membuat bagian
         bawah beranda terbaca sebagai satu bidang panjang tanpa jeda; latar
         bertinta biru ini memberi irama sekaligus mengangkat panel akordeon
         yang memang putih. --}}
    <section class="section relative overflow-hidden border-t border-navy-50 bg-paper">
        <div class="pointer-events-none absolute inset-0 aurora-light opacity-50"></div>

        <div class="container relative grid gap-12 lg:grid-cols-12 lg:gap-16">

            {{-- Left Side: Heading & WhatsApp CTA --}}
            <div class="lg:col-span-5 flex flex-col space-y-8 lg:sticky lg:top-28 lg:self-start" data-aos="fade-right">
                <div class="space-y-4">
                    {{-- .kicker, not .eyebrow: the latter is display:none site-wide, so this
                         label never actually rendered. Satu-satunya kicker di beranda,
                         jadi ia masih terbaca sebagai label yang disengaja, bukan
                         perancah yang diulang di tiap seksi. --}}
                    <p class="kicker">FAQ</p>
                    <h2 class="text-display-lg text-navy">{{ $isId ? 'Pertanyaan yang Sering Diajukan' : 'Frequently Asked Questions' }}</h2>
                    <p class="max-w-md text-[15px] leading-relaxed text-slate-600 text-pretty">
                        {{ $isId
                            ? 'Temukan jawaban cepat untuk pertanyaan umum mengenai sertifikasi, pelatihan, rekrutmen, dan kemitraan di PT Delta Tiga Enam.'
                            : 'Find quick answers to common questions about certification, training, recruitment, and partnerships at PT Delta Tiga Enam.' }}
                    </p>
                </div>

                {{-- WhatsApp CTA Card --}}
                <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-navy-900 to-navy-950 p-7 text-white shadow-lift ring-1 ring-white/5 space-y-4 md:p-8">
                    <div class="pointer-events-none absolute -right-14 -top-14 h-40 w-40 rounded-full bg-sky-400/15 blur-3xl"></div>
                    <div class="pointer-events-none absolute -bottom-16 -left-10 h-40 w-40 rounded-full bg-gold/10 blur-3xl"></div>
                    <div class="relative flex items-center gap-3">
                        {{-- Ubin putih berikon navy, bukan sky-400 berikon putih:
                             putih di atas #34d3f4 hanya 1,9:1, jadi ikonnya nyaris
                             tidak terbaca. Bentuknya pun kini sama dengan lencana
                             ikon di panel Berita. --}}
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-white text-navy ring-1 ring-white/20">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.2 4.79 1.2h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.95 1.36-.5.05-.96.24-3.23-.67-2.73-1.08-4.47-3.86-4.6-4.04-.14-.18-1.11-1.48-1.11-2.82 0-1.34.7-2 .95-2.28.24-.27.53-.34.7-.34.18 0 .35 0 .5.01.16.01.38-.06.59.45.22.52.73 1.8.8 1.93.06.13.11.28.02.46-.09.18-.13.29-.26.45-.13.16-.28.36-.4.48-.13.13-.27.28-.12.54.15.26.66 1.09 1.42 1.76.97.87 1.79 1.14 2.05 1.27.26.13.41.11.56-.07.15-.18.65-.76.82-1.02.17-.26.35-.22.59-.13.24.09 1.52.72 1.78.85.26.13.43.2.5.31.06.11.06.64-.18 1.32z"/></svg>
                        </span>
                        <h3 class="font-display font-semibold text-white text-base">{{ $isId ? 'Punya pertanyaan lain?' : 'Have more questions?' }}</h3>
                    </div>
                    <p class="relative text-xs text-navy-200 leading-relaxed">
                        {{ $isId
                            ? 'Jika Anda tidak menemukan jawaban yang dicari, hubungi admin kami melalui WhatsApp untuk konsultasi cepat.'
                            : 'If you cannot find the answer you are looking for, contact our admin via WhatsApp for a quick consultation.' }}
                    </p>
                    <div class="relative pt-2">
                        <a href="https://wa.me/62818834766?text=Halo%20Delta%20Tiga%20Enam,%20saya%20ingin%20bertanya%20mengenai..." target="_blank" rel="noopener" class="btn-blue w-full">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.2 4.79 1.2h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.16c-.24.68-1.42 1.31-1.95 1.36-.5.05-.96.24-3.23-.67-2.73-1.08-4.47-3.86-4.6-4.04-.14-.18-1.11-1.48-1.11-2.82 0-1.34.7-2 .95-2.28.24-.27.53-.34.7-.34.18 0 .35 0 .5.01.16.01.38-.06.59.45.22.52.73 1.8.8 1.93.06.13.11.28.02.46-.09.18-.13.29-.26.45-.13.16-.28.36-.4.48-.13.13-.27.28-.12.54.15.26.66 1.09 1.42 1.76.97.87 1.79 1.14 2.05 1.27.26.13.41.11.56-.07.15-.18.65-.76.82-1.02.17-.26.35-.22.59-.13.24.09 1.52.72 1.78.85.26.13.43.2.5.31.06.11.06.64-.18 1.32z"/></svg>
                            <span>Hubungi WA Admin</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right Side: Accordion driven by AlpineJS --}}
            <div class="lg:col-span-7" data-aos="fade-left" x-data="{ activeFaq: 0 }">
                @php
                    $faqs = $isId ? [
                        [
                            'q' => 'Apa saja layanan utama yang ditawarkan oleh PT Delta Tiga Enam?',
                            'a' => 'Kami menawarkan solusi human capital terintegrasi yang mencakup Pelatihan SDM, Sertifikasi Kompetensi Profesi (BNSP), Asesmen & Seleksi Karyawan, serta Headhunter & Penempatan Tenaga Kerja.',
                        ],
                        [
                            'q' => 'Apa manfaat bekerja sama dengan konsultan human capital seperti Delta Tiga Enam?',
                            'a' => 'Sebagai konsultan human capital, kami membantu perusahaan mengelola SDM secara strategis, mulai dari pemetaan kompetensi, peningkatan produktivitas, hingga efisiensi rekrutmen. Anda mendapatkan pendampingan ahli sehingga setiap keputusan terkait talenta menjadi lebih terukur, hemat biaya, dan berdampak langsung pada kinerja organisasi.',
                        ],
                        [
                            'q' => 'Apakah sertifikasi kompetensi dari Delta Tiga Enam resmi?',
                            'a' => 'Ya, seluruh program sertifikasi kompetensi kami diselenggarakan secara resmi bekerja sama dengan LSP berlisensi BNSP (Badan Nasional Sertifikasi Profesi) sehingga diakui di tingkat nasional oleh berbagai sektor industri.',
                        ],
                        [
                            'q' => 'Berapa lama durasi pelatihan dan apakah peserta memperoleh sertifikat?',
                            'a' => 'Durasi pelatihan bervariasi mulai dari 1–5 hari tergantung program dan tingkat kompetensinya. Setiap peserta yang menyelesaikan pelatihan akan memperoleh sertifikat; khusus program sertifikasi kompetensi, sertifikat diterbitkan resmi oleh LSP berlisensi BNSP setelah peserta dinyatakan kompeten dalam uji sertifikasi.',
                        ],
                        [
                            'q' => 'Bagaimana cara mendaftar program pelatihan atau sertifikasi?',
                            'a' => 'Anda dapat mendaftar langsung dengan memilih layanan di website kami, menghubungi admin kami melalui WhatsApp, atau mengirimkan detail kebutuhan Anda melalui formulir kontak.',
                        ],
                        [
                            'q' => 'Apakah program pelatihan bisa diadakan khusus untuk internal perusahaan (In-House)?',
                            'a' => 'Tentu saja. Kami menyediakan layanan In-House Training dengan materi, jadwal, dan metode penyampaian yang dirancang khusus menyesuaikan kebutuhan peningkatan kompetensi staf di perusahaan Anda.',
                        ],
                    ] : [
                        [
                            'q' => 'What are the main services offered by PT Delta Tiga Enam?',
                            'a' => 'We offer integrated human capital solutions covering HR Training, Professional Competence Certification (BNSP), Employee Assessment & Selection, and Headhunting & Talent Placement.',
                        ],
                        [
                            'q' => 'What are the benefits of working with a human capital consultant like Delta Tiga Enam?',
                            'a' => 'As a human capital consultant, we help companies manage their workforce strategically, from competency mapping and productivity improvement to more efficient recruitment. You gain expert guidance so every talent-related decision becomes measurable, cost-effective, and directly impacts organizational performance.',
                        ],
                        [
                            'q' => 'Are the competence certifications official?',
                            'a' => 'Yes, all our competency certification programs are officially conducted in partnership with BNSP-licensed LSPs, ensuring national recognition across industries.',
                        ],
                        [
                            'q' => 'How long is the training and do participants receive a certificate?',
                            'a' => 'Training duration varies from 1–5 days depending on the program and competency level. Every participant who completes the training receives a certificate; for competency certification programs, certificates are officially issued by BNSP-licensed LSPs once the participant is declared competent in the assessment.',
                        ],
                        [
                            'q' => 'How can I register for a training or certification program?',
                            'a' => 'You can register directly by selecting the service on our website, contacting our admin via WhatsApp, or sending your details through the contact form.',
                        ],
                        [
                            'q' => 'Can training programs be customized for internal company needs (In-House)?',
                            'a' => 'Absolutely. We provide customized In-House Training with curriculum, schedule, and methods tailored to address specific competence needs in your organization.',
                        ],
                    ];
                @endphp

                {{-- One surface with hairline dividers instead of a stack of boxed cards:
                     seven bordered rectangles read as a card grid, which the brand brief
                     explicitly lists as an anti-reference. --}}
                <div class="overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card">
                    <div class="divide-y divide-navy-100">
                        @foreach ($faqs as $i => $faq)
                            <div class="group/faq relative transition-colors duration-300"
                                 :class="activeFaq === {{ $i }} ? 'bg-navy-50/60' : 'bg-white hover:bg-navy-50/30'">

                                {{-- Active marker: a hairline on the row's leading edge, drawn with a
                                     pseudo-free span so it scales rather than shifting the text. --}}
                                {{-- Gradasinya memakai pasangan biru yang sama dengan
                                     .btn-blue (navy-500 → sky-400). Sebelumnya biru ke
                                     emas — satu-satunya tempat di situs ini yang
                                     mencampur aksen dengan emas dalam satu gradasi. --}}
                                <span aria-hidden="true"
                                      class="pointer-events-none absolute inset-y-0 left-0 w-[3px] origin-center bg-gradient-to-b from-navy-500 to-sky-400 transition-transform duration-300 ease-out-soft"
                                      :class="activeFaq === {{ $i }} ? 'scale-y-100' : 'scale-y-0'"></span>

                                <h3>
                                    <button type="button" id="faq-q-{{ $i }}"
                                            x-on:click="activeFaq = activeFaq === {{ $i }} ? null : {{ $i }}"
                                            :aria-expanded="activeFaq === {{ $i }} ? 'true' : 'false'"
                                            aria-controls="faq-panel-{{ $i }}"
                                            class="flex w-full items-start gap-4 px-5 py-5 text-left transition-colors md:gap-5 md:px-7">

                                        {{-- transition-colors: yang berubah hanya dua warna.
                                             slate-600 di atas navy-50, bukan slate-500 —
                                             pasangan yang lama 4,33:1, di bawah ambang. --}}
                                        <span class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-lg font-mono text-[11px] font-bold tabular-nums transition-colors duration-300" aria-hidden="true"
                                              :class="activeFaq === {{ $i }} ? 'bg-navy text-white' : 'bg-navy-50 text-slate-600 group-hover/faq:bg-navy-100'">{{ sprintf('%02d', $i + 1) }}</span>

                                        {{-- text-slate-600, bukan text-navy/85: warna teks
                                             sekunder yang sudah dipakai seluruh situs,
                                             bukan satu-satunya tempat yang memakai navy
                                             beropasitas. --}}
                                        <span class="flex-1 font-display text-[15px] font-semibold leading-snug transition-colors duration-300 md:text-[17px]"
                                              :class="activeFaq === {{ $i }} ? 'text-navy' : 'text-slate-600'">{{ $faq['q'] }}</span>

                                        {{-- Plus that rotates into a minus. --}}
                                        <span class="relative mt-0.5 grid h-7 w-7 shrink-0 place-items-center transition-transform duration-300 ease-out-soft"
                                              {{-- slate-500, bukan slate-400: tanda + ini
                                                   satu-satunya isyarat bahwa barisnya bisa
                                                   dibuka, jadi ia komponen antarmuka yang
                                                   harus mencapai 3:1. slate-400 hanya 2,6:1. --}}
                                              :class="activeFaq === {{ $i }} ? 'rotate-180 text-sky-700' : 'text-slate-500'"
                                              aria-hidden="true">
                                            <span class="absolute h-0.5 w-3.5 rounded-full bg-current"></span>
                                            <span class="absolute h-3.5 w-0.5 rounded-full bg-current transition-transform duration-300 ease-out-soft"
                                                  :class="activeFaq === {{ $i }} ? 'scale-y-0' : 'scale-y-100'"></span>
                                        </span>
                                    </button>
                                </h3>

                                {{-- grid-rows 0fr -> 1fr: animates to the answer's natural height, so a
                                     long answer is never clipped the way a fixed max-height clips it. --}}
                                <div id="faq-panel-{{ $i }}" role="region" aria-labelledby="faq-q-{{ $i }}"
                                     class="faq-panel grid transition-[grid-template-rows] duration-300 ease-out-soft"
                                     :class="activeFaq === {{ $i }} ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'">
                                    <div class="overflow-hidden">
                                        <p class="pb-6 pl-[3.75rem] pr-5 text-sm leading-relaxed text-slate-600 text-pretty md:pl-[4.25rem] md:pr-8">
                                            {{ $faq['a'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ===================== UPCOMING AGENDAS =====================
         Ikut disembunyikan selagi halaman Agenda dimatikan lewat
         config/company.php: seluruh tombol di seksi ini menuju halaman itu,
         jadi membiarkannya tampil berarti mengirim pengunjung ke 404. --}}
    @if (config('company.pages.agenda', true))
    <section class="section relative overflow-hidden border-t border-navy-50 bg-paper">
        {{-- Animated background --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute left-1/2 top-1/2 h-[560px] w-[560px] -translate-x-1/2 -translate-y-1/2 rounded-full" style="background: radial-gradient(circle, rgba(28,125,224,0.10), transparent 70%);"></div>
            <div class="absolute -left-24 top-16 h-72 w-72 animate-float-slow rounded-full bg-sky-300/25 blur-3xl"></div>
            <div class="absolute -right-24 top-1/3 h-80 w-80 animate-float rounded-full bg-gold-soft/20 blur-3xl"></div>
            <div class="absolute -bottom-16 left-1/3 h-72 w-72 animate-float-slow rounded-full bg-sky-200/30 blur-3xl"></div>
            <div class="absolute -left-20 -top-20 h-64 w-64 animate-spin-slow rounded-full border border-dashed border-sky-300/40"></div>
        </div>

        <div class="container relative">
            {{-- Centered header --}}
            <div class="mx-auto max-w-2xl text-center" data-aos="fade-up">
                <h2 class="text-display-lg text-navy">{{ $isId ? 'Jadwal Pelatihan & Sertifikasi' : 'Training & Certification Schedule' }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-slate-600">
                    {{ $isId
                        ? 'Ikuti kelas pelatihan, sertifikasi, dan kegiatan terbaru dari kami. Geser untuk melihat jadwal lengkapnya.'
                        : 'Join our latest training classes, certifications, and events. Swipe to explore the full schedule.' }}
                </p>
            </div>

            @if ($upcomingAgendas->isNotEmpty())
                <div class="relative mt-12" data-hscroll data-hscroll-auto data-aos="fade-up">
                    {{-- Arrows (desktop) --}}
                    <button type="button" data-hscroll-prev aria-label="{{ $isId ? 'Sebelumnya' : 'Previous' }}"
                        class="absolute left-0 top-1/2 z-20 hidden h-12 w-12 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 lg:grid">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <button type="button" data-hscroll-next aria-label="{{ $isId ? 'Berikutnya' : 'Next' }}"
                        class="absolute right-0 top-1/2 z-20 hidden h-12 w-12 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full border border-navy-100 bg-white text-navy shadow-lift transition hover:border-navy hover:bg-navy hover:text-white active:scale-95 lg:grid">
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    <div data-hscroll-track class="flex cursor-grab snap-x snap-mandatory select-none gap-6 overflow-x-auto scroll-smooth pb-4 active:cursor-grabbing [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($upcomingAgendas as $agenda)
                            @php
                                $isPast = $agenda->starts_at->isPast();
                                $isNearest = $loop->first && ! $isPast; // first upcoming = soonest
                            @endphp
                            <a href="{{ route('agenda.index') }}" data-spotlight
                               class="group relative flex min-w-0 shrink-0 basis-[80%] snap-start flex-col overflow-hidden rounded-3xl border bg-white shadow-card transition-all duration-500 ease-out-soft hover:-translate-y-1.5 hover:shadow-lift sm:basis-[calc((100%_-_1.5rem)/2)] md:basis-[calc((100%_-_3rem)/3)] lg:basis-[calc((100%_-_4.5rem)/4)] {{ $isNearest ? 'border-gold/60 ring-1 ring-gold/40' : 'border-navy-100 hover:border-navy-200' }}">

                            {{-- Media (compact) --}}
                            <div class="relative aspect-[16/10] overflow-hidden bg-navy-900">
                                @if ($agenda->image)
                                    <img src="{{ Storage::url($agenda->image) }}" alt="{{ $agenda->title }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 ease-out-soft group-hover:scale-[1.05]">
                                @else
                                    <div class="absolute inset-0" style="background: linear-gradient(150deg,#123a6b 0%,#071d38 100%);"></div>
                                    <div class="absolute inset-0 aurora opacity-40"></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-navy-950/55 via-navy-950/5 to-transparent"></div>

                                {{-- Compact date badge --}}
                                <div class="absolute left-3 top-3 flex w-11 flex-col items-center rounded-xl py-1.5 text-center shadow-lift ring-1 backdrop-blur {{ $isNearest ? 'bg-gradient-to-br from-gold to-gold-soft ring-gold-soft/70' : 'bg-white/95 ring-white/60' }}">
                                    <span class="font-display text-lg font-semibold leading-none {{ $isNearest ? 'text-navy-950' : 'text-navy' }}">{{ $agenda->starts_at->format('d') }}</span>
                                    {{-- Bulannya bagian dari tanggal, bukan hiasan, jadi
                                         ambangnya 4,5:1. slate-400 hanya 2,6:1 dan
                                         gold-deep 3,3:1 di atas lencana yang hampir
                                         putih ini. --}}
                                    <span class="font-mono text-[9px] font-bold uppercase tracking-normal {{ $isNearest ? 'text-navy-950/70' : ($isPast ? 'text-slate-600' : 'text-navy') }}">{{ $agenda->starts_at->translatedFormat('M') }}</span>
                                </div>

                                {{-- Status pill — Segera Hadir (terdekat) / Akan Datang / Selesai (lewat) --}}
                                @php
                                    if ($isPast)      { $pillBg = 'bg-slate-500/90 text-white'; $pillDot = 'bg-white/80'; $pillText = $isId ? 'Selesai' : 'Ended'; }
                                    elseif ($isNearest) { $pillBg = 'bg-gold text-navy-950';    $pillDot = 'bg-navy-950/70'; $pillText = $isId ? 'Segera Hadir' : 'Coming Soon'; }
                                    // sky-700, bukan sky-600: putih di atas sky-600 hanya
                                    // 3,45:1, sedangkan pil 9px tebal ini tetap teks biasa
                                    // menurut WCAG dan butuh 4,5:1.
                                    else              { $pillBg = 'bg-sky-700/90 text-white';  $pillDot = 'bg-white/90';   $pillText = $isId ? 'Akan Datang' : 'Upcoming'; }
                                @endphp
                                <span class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-normal shadow-lift backdrop-blur {{ $pillBg }}">
                                    <span class="h-1 w-1 rounded-full {{ $pillDot }}"></span>
                                    {{ $pillText }}
                                </span>
                            </div>

                            {{-- Body (compact) --}}
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center gap-2 font-mono text-[11px] text-slate-500">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="shrink-0">{{ $agenda->starts_at->translatedFormat('H:i') }} WIB</span>
                                    @if ($agenda->location)
                                        <span class="text-navy-200">&middot;</span>
                                        <span class="truncate">{{ $agenda->location }}</span>
                                    @endif
                                </div>

                                <h3 class="mt-2.5 line-clamp-2 font-display text-base font-semibold leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">
                                    {{ $agenda->title }}
                                </h3>

                                <span class="mt-auto inline-flex items-center gap-1.5 pt-4 text-xs font-bold uppercase tracking-normal text-sky-700 transition-colors group-hover:text-navy">
                                    {{ $isId ? 'Selengkapnya' : 'Read More' }}
                                    <svg class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10 text-center" data-aos="fade-up">
                    {{-- .btn-ghost, bukan salinan tulisan tangannya. Kelas itu sudah
                         berisi bentuk, jarak, dan wujud sentuh yang sama persis, dan
                         salinan ini tertinggal dari perilaku tekan .btn yang lain. --}}
                    <a href="{{ route('agenda.index') }}" class="btn-ghost">
                        {{ $isId ? 'Lihat Semua Agenda' : 'View All Agendas' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            @else
                {{-- Premium Empty State / WhatsApp Lead Generation --}}
                {{-- Tanpa .card: kelas itu membawa bg-white, border-navy-100, lebar
                     garis, dan rounded-2xl yang keempatnya ditimpa di sini juga,
                     jadi yang benar-benar terpakai hanya shadow-card. --}}
                <div class="mt-10 rounded-3xl border-2 border-dashed border-navy-200 bg-navy-50/20 p-8 text-center shadow-card md:p-12" data-aos="fade-up">
                    <div class="max-w-md mx-auto space-y-4">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy-50 text-slate-500 mx-auto">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                        </span>
                        <h3 class="font-display text-lg font-semibold text-navy">
                            {{ $isId ? 'Belum Ada Agenda Terdekat' : 'No Upcoming Agendas' }}
                        </h3>
                        <p class="text-xs leading-relaxed text-slate-600 text-balance">
                            {{ $isId
                                ? 'Kami sedang merancang berbagai kelas pelatihan dan jadwal sertifikasi kompetensi terbaru. Ingin tahu jadwal terdekat atau berdiskusi?'
                                : 'We are planning various training classes and new competence certification schedules. Want to know the nearest schedule or discuss?' }}
                        </p>
                        <div class="pt-2">
                            {{-- .btn-blue, sama dengan tombol WhatsApp di seksi FAQ.
                                 Hijau emerald sebelumnya adalah satu-satunya hijau di
                                 situs ini, dan dua tombol WhatsApp berwarna berbeda di
                                 satu halaman terbaca sebagai dua tindakan berbeda. --}}
                            <a href="https://wa.me/62818834766?text=Halo%20Delta%20Tiga%20Enam,%20saya%20ingin%20tahu%20jadwal%20pelatihan%20dan%20sertifikasi%20terdekat..." target="_blank" rel="noopener" class="btn-blue">
                                <img src="{{ asset('icons/ic_whatsapp.png') }}" class="h-4.5 w-4.5 object-contain" alt="WhatsApp">
                                <span>WhatsApp Admin</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ===================== PARTNERS & CLIENTS ===================== --}}
    <x-partners-clients :partners="$partners" :clients="$clients" />

</x-layout>
