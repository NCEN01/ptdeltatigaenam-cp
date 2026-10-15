@php
    $isId = app()->getLocale() === 'id';
    $aboutParas = array_filter(array_map('trim', preg_split('/\n\s*\n/', trim((string) $about)))) ?: [$about];


    // Founder (structural content — editable here in the view).
    // Satu baris jabatan, bukan dua. Dulu "Pendiri" berdiri sebagai label di
    // atas nama dan "CEO Delta Tiga Enam" sebagai baris terpisah di bawahnya;
    // keduanya menyebut orang yang sama dan memisahkannya hanya menambah baris.
    $founderRole = $isId ? 'Pendiri & CEO' : 'Founder & CEO';
    // Angka pengalaman dipisah dari kalimat riwayatnya supaya bisa dicetak
    // besar di sampingnya. Kalimatnya sendiri tidak lagi mengulang angkanya.
    $founderYears = '20+';
    $founderYearsLabel = $isId ? 'tahun pengalaman' : 'years of experience';
    $founderName = 'Dani Taupan Ramdani, S.T., M.M., Ph.D (Cand.)';
    $founderQuote = $isId
        ? 'Membangun bangsa dimulai dari membangun manusianya. Di Delta Tiga Enam, kami berkomitmen menjadi jembatan bagi talenta Indonesia menuju standar dunia.'
        : 'Building a nation begins with building its people. At Delta Tiga Enam, we are committed to bridging Indonesian talent toward world-class standards.';
    $founderBio = $isId
        ? 'Di bidang Human Resources dan Manajemen Strategis, membantu perusahaan BUMN maupun swasta melakukan restrukturisasi organisasi.'
        : 'In Human Resources and Strategic Management, helping state-owned and private companies restructure their organisations.';
@endphp

<x-layout :title="$isId ? 'Tentang Kami' : 'About Us'" :description="$tagline">
    <x-page-header
        :title="$isId ? 'Tentang Kami' : 'About Us'"
        placement="about"
        image="photo-1522071820081-009f0129c71c" />

    {{-- ===================== PROFIL PERUSAHAAN =====================
         Judul di atas, lalu teks di kolom kiri yang sempit dengan foto
         memanjang di kanan. Dua angka menumpang di tepi atas foto.

         Angkanya dua, bukan empat. Di tata letak ini angka bukan lagi papan
         statistik tersendiri melainkan keterangan atas fotonya, dan empat
         kartu di sudut yang sama akan menutupi foto yang jadi alasan
         kolom itu ada. Dua yang dipakai adalah dua teratas dari sumber yang
         sama dengan papan angka di beranda, jadi tidak ada angka kedua
         tentang perusahaan yang sama yang bisa menyimpang. --}}
    @php
        $profileStats = array_slice($stats, 0, 2);

        // Nama perusahaan di kalimat pembuka ditebalkan. Teksnya datang dari
        // CMS, jadi di-escape lebih dulu baru disisipi penandanya — bukan
        // sebaliknya, supaya isian admin tidak pernah jadi markup.
        $leadHtml = e($aboutParas[0] ?? '');
        foreach (['PT. Delta Tiga Enam', 'PT Delta Tiga Enam', 'Delta Tiga Enam'] as $brandName) {
            if (str_contains($leadHtml, $brandName)) {
                $leadHtml = \Illuminate\Support\Str::replaceFirst(
                    $brandName,
                    '<strong class="font-semibold italic text-navy">'.$brandName.'</strong>',
                    $leadHtml
                );
                break;
            }
        }
    @endphp
    {{-- Jarak bawahnya dipendekkan dari .section: kartu PDF di seksi berikutnya
         masih tentang Profil Perusahaan juga, jadi keduanya dibaca sebagai satu
         blok, bukan dua seksi yang kebetulan bersebelahan. --}}
    <section class="bg-white pb-14 pt-20 md:pb-16 md:pt-28 lg:pt-36">
        <div class="container">
            <h2 class="text-display-lg font-bold text-navy" data-aos="fade-up">{{ $isId ? 'Profil Perusahaan' : 'Company Profile' }}</h2>

            {{-- Rata regang: foto di kanan mengikuti tinggi kolom teks, jadi kedua
                 kolom berakhir di garis yang sama berapa pun panjang profil yang
                 diisikan admin. Tanpa itu, teks yang panjang meninggalkan ruang
                 kosong menganga di bawah fotonya. --}}
            <div class="mt-8 grid items-stretch gap-8 lg:mt-10 lg:grid-cols-12 lg:gap-12">
                {{-- Rata kanan-kiri mulai dari sm. Di lebar ponsel barisnya hanya
                     memuat sekitar 45 huruf dan perataan penuh di situ membuka
                     celah putih memanjang di tengah paragraf, jadi di sana teksnya
                     tetap rata kiri. [hyphens:auto] memperhalus sisanya. --}}
                <div class="space-y-4 text-[15px] leading-[1.75] text-slate-700 lg:col-span-5" data-aos="fade-up">
                    <p class="text-left [hyphens:auto] sm:text-justify">{!! $leadHtml !!}</p>
                    @foreach (array_slice($aboutParas, 1) as $para)
                        <p class="text-left [hyphens:auto] sm:text-justify">{{ $para }}</p>
                    @endforeach
                </div>

                <div class="relative lg:col-span-7" data-aos="fade-up" data-aos-delay="100">
                    {{-- Di layar lebar tingginya ditentukan kolom teks, bukan
                         perbandingan tetap. Di layar sempit ia kembali ke 16:10 supaya
                         orang di dalam foto tidak jadi sepita tipis. Batas bawah 20rem
                         menjaga fotonya tetap terbaca kalau profilnya dipersingkat. --}}
                    <div class="aspect-[16/10] overflow-hidden rounded-2xl bg-mist shadow-card lg:aspect-auto lg:h-full lg:min-h-[20rem]">
                        <img src="{{ asset('images/profil-perusahaan.webp') }}"
                             alt="{{ $isId ? 'Sesi pelatihan PT Delta Tiga Enam' : 'A PT Delta Tiga Enam training session' }}"
                             width="1600" height="900" loading="lazy" decoding="async"
                             class="h-full w-full object-cover">
                    </div>

                    {{-- Di layar sempit kartunya turun ke bawah foto: menumpuk di sudut
                         kanan atas foto selebar layar ponsel akan menutupi wajah orang
                         di dalamnya. --}}
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:absolute sm:-top-6 sm:right-5 sm:mt-0 sm:flex sm:gap-3">
                        @foreach ($profileStats as $stat)
                            {{-- Angka dulu, keterangannya di bawah: di kartu sekecil ini
                                 angkanyalah yang dicari mata, dan keterangan di atasnya
                                 membuat mata membaca dua kali. --}}
                            <div class="rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 p-4 shadow-lift ring-1 ring-white/10 sm:w-36 sm:p-5">
                                <p class="font-display text-2xl font-bold leading-none text-white sm:text-[1.75rem]">
                                    <span class="tabular-nums" data-counter="{{ $stat['number'] }}">{{ $stat['number'] }}</span><span class="text-sky-400">{{ $stat['suffix'] }}</span>
                                </p>
                                <p class="mt-2 text-[11px] leading-tight text-navy-100">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== COMPANY PROFILE (PDF DOWNLOAD) ===================== --}}
    @php
        $cpUrl = asset('documents/company-profile.pdf');
        $cpPath = public_path('documents/company-profile.pdf');
        $cpSize = is_file($cpPath) ? number_format(filesize($cpPath) / 1048576, 1).' MB · PDF' : 'PDF';
    @endphp
    <section class="bg-white pb-14 md:pb-20 lg:pb-24">
        <div class="container">
            {{-- shadow-card at rest, not shadow-lift: a 1px border paired with a 48px-blur
                 shadow is the "ghost card" look. One or the other, and hover-shadow-2xl was
                 a Tailwind default that sits outside this site's shadow scale.
                 Radius 2xl to match every other card.

                 Selebar container, tanpa max-w-4xl: panel berkas di halaman Layanan
                 selebar itu, dan dua kartu dokumen yang sama bentuknya tapi beda
                 lebarnya terbaca sebagai dua komponen berbeda padahal bukan. --}}
            <div class="group relative overflow-hidden rounded-2xl border border-navy-100 bg-gradient-to-br from-white via-white to-mist p-7 shadow-card transition-shadow duration-300 hover:shadow-lift md:p-9" data-aos="fade-up">
                {{-- soft ambient glows --}}
                <div class="pointer-events-none absolute -right-20 -top-20 h-56 w-56 rounded-full bg-gold/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -left-16 -bottom-16 h-44 w-44 rounded-full bg-sky-400/10 blur-3xl"></div>

                <div class="relative flex flex-col items-start gap-7 md:flex-row md:items-center md:justify-between md:gap-10">
                    <div class="flex items-start gap-6">
                        {{-- Stylised PDF page (tilts + lifts on hover) --}}
                        <div class="relative shrink-0">
                            <div class="grid h-24 w-[4.5rem] place-items-center rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 shadow-lift ring-1 ring-white/10 transition-transform duration-300 ease-out-soft group-hover:-translate-y-1 group-hover:-rotate-3">
                                <svg class="h-9 w-9 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                            </div>
                            <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 rounded-md bg-gold px-2.5 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider text-navy-950 shadow-gold">PDF</span>
                        </div>
                        <div>
                            <p class="mb-2 font-mono text-[11px] uppercase tracking-normal text-gold-deep">{{ $isId ? 'Dokumen Resmi' : 'Official Document' }}</p>
                            <h2 class="font-display text-2xl font-bold leading-tight text-navy md:text-3xl">{{ $isId ? 'Profil Perusahaan' : 'Company Profile' }}</h2>
                            <p class="mt-2.5 max-w-md text-pretty text-sm leading-relaxed text-slate-600">{{ $isId ? 'Pelajari layanan, nilai, dan rekam jejak PT Delta Tiga Enam secara lengkap dalam satu dokumen.' : 'Explore the services, values, and full track record of PT Delta Tiga Enam in one document.' }}</p>
                            <p class="mt-3 inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-wider text-slate-400">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                {{ $cpSize }}
                            </p>
                        </div>
                    </div>

                    <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row md:w-auto">
                        <a href="{{ $cpUrl }}" target="_blank" rel="noopener" class="btn-ghost justify-center">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                            {{ $isId ? 'Lihat' : 'View' }}
                        </a>
                        <a href="{{ $cpUrl }}" download class="btn-blue justify-center">
                            {{ $isId ? 'Unduh PDF' : 'Download PDF' }}
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== VISION & MISSION =====================
         Pita navy berpendar, judul raksasa di kiri, pernyataan visi di kanan,
         lalu kelima misi berderet mendatar sebagai alur langkah di bawah garis.

         Latarnya pendar dari satu titik di kiri atas, bukan foto. Foto di sini
         dulu ditarik langsung dari Unsplash pada tiap kunjungan — satu
         permintaan ke peladen pihak ketiga untuk gambar yang tidak
         menggambarkan apa pun — dan latar bergambar membuat kata-kata di
         atasnya harus dilindungi lapis gelap yang justru memadamkan fotonya.

         Emas dipakai dua kali saja di seluruh pita: ampersan di judul raksasa
         dan satu penggalan di dalam pernyataan visi. Label misi memakai biru
         muda, bukan emas, supaya emasnya tetap jadi penanda yang jarang. --}}
    @php
        /* Visi dipecah jadi dua: kalimat pertama tampil besar sebagai
           pernyataan, sisanya jadi paragraf penjelas. Satu penggalan di antara
           tanda bintang di kalimat pertama ditampilkan emas — konvensi yang
           sama dengan judul seksi Lembaga Mitra di halaman Kemitraan, jadi
           admin tidak perlu mengetik HTML. */
        [$visionLead, $visionRest] = array_pad(preg_split('/(?<=\.)\s+/', trim((string) $vision), 2), 2, '');

        $vLead = $visionLead;
        $vAccent = $vTail = '';
        if (preg_match('/^(.*?)\*(.+?)\*(.*)$/', $visionLead, $m)) {
            [$vLead, $vAccent, $vTail] = [trim($m[1]), trim($m[2]), $m[3]];
        }
    @endphp
    <section class="relative overflow-hidden bg-navy-950 py-20 text-white md:py-28">
        <div class="pointer-events-none absolute inset-0 glow-navy"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>

        {{-- Kata "VISI" raksasa di sudut kanan atas, nyaris tak terlihat.
             aria-hidden: ia tekstur, bukan tulisan yang perlu dibacakan. --}}
        <span class="pointer-events-none absolute -top-6 right-0 select-none font-display text-[22vw] font-bold leading-none tracking-tight text-white/[0.025] md:-top-10 md:text-[14rem]" aria-hidden="true">
            {{ $isId ? 'VISI' : 'VISION' }}
        </span>

        <div class="container relative">
            <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">
                {{-- Judul raksasa. Dua baris, dan "&" emas yang dimiringkan: tanda
                     itu satu-satunya yang menyambung kedua kata, jadi ia yang
                     pantas diberi warna, bukan salah satu katanya. Ampersan miring
                     adalah kebiasaan lama penata huruf — bentuk miringnya memang
                     lebih indah daripada bentuk tegaknya — dan di sini ia memberi
                     satu titik lembut pada judul yang seluruhnya tegak dan tebal.

                     Miringnya hanya dua di seluruh pita ini: ampersan ini dan
                     penggalan emas di dalam visi. Keduanya emas, jadi yang terbaca
                     satu sistem, bukan huruf miring yang bertaburan. --}}
                <div class="lg:col-span-4" data-aos="fade-up">
                    {{-- text-display-lg, ukuran yang sama dengan judul Profil
                         Perusahaan di awal halaman ini. Sebelumnya text-6xl/7xl —
                         hampir dua kali lipat — sehingga satu halaman punya dua
                         jenjang judul seksi yang berbeda dan pembaca kehilangan
                         patokan mana yang setara dengan mana. --}}
                    <h2 class="text-display-lg font-bold text-white">
                        {{ $isId ? 'Visi' : 'Vision' }}<br>
                        <span class="pr-1 italic text-gold-soft">&amp;</span>{{ $isId ? 'Misi' : 'Mission' }}
                    </h2>
                </div>

                <div class="lg:col-span-8 lg:pt-2" data-aos="fade-up" data-aos-delay="100">
                    <h3 class="font-mono text-[10px] uppercase tracking-[0.2em] text-navy-200">{{ $isId ? 'Visi' : 'Vision' }}</h3>

                    {{-- Kalimat pertama sebesar judul seksi, sisanya turun ke
                         ukuran teks isi. Tanpa jenjang itu, visi tiga kalimat
                         terbaca sebagai paragraf biasa dan kalimat intinya
                         hilang di tengahnya.

                         Penggalan emasnya dimiringkan dan bobotnya diturunkan ke
                         normal. Miring saja di atas huruf tebal nyaris tidak
                         terlihat; yang membuatnya terbaca sebagai penggalan
                         tersendiri adalah perbedaan bobotnya, dan miringnya yang
                         melembutkan peralihan itu agar tidak terbaca seperti
                         kalimat yang terputus. --}}
                    <p class="mt-4 max-w-3xl text-pretty font-display text-2xl font-bold leading-[1.3] text-white md:text-[1.75rem]">
                        {{ $vLead }}@if ($vAccent !== '') <span class="font-normal italic text-gold-soft">{{ $vAccent }}</span>@endif{{ $vTail }}
                    </p>

                    @if ($visionRest !== '')
                        <p class="mt-5 max-w-2xl text-pretty leading-relaxed text-navy-200">{{ $visionRest }}</p>
                    @endif
                </div>
            </div>

            @if ($missions->isNotEmpty())
                {{-- Lini masa mendatar yang menanggapi tunjukan.

                     Kelima misi ini satu alur, dari menyusun program sampai
                     menjaga standarnya, dan rel yang terisi sampai titik yang
                     sedang ditunjuk memperlihatkan alur itu bergerak, bukan
                     sekadar berderet.

                     Keadaan aktifnya tidak pulih saat tunjukan ditarik. Rel yang
                     surut kembali ke nol tiap kali kursor lewat terbaca sebagai
                     kedipan, bukan sebagai posisi.

                     Isinya tetap utuh di dalam DOM dalam keadaan apa pun; yang
                     berubah hanya kepekatannya. Jadi pembaca layar dan mesin
                     pencari membaca kelima misi itu seluruhnya, dan halaman ini
                     tetap lengkap sekalipun JavaScript-nya gagal dimuat. --}}
                <div x-data="{ active: 0, labels: @js($missions->pluck('label')->all()) }">
                    <div class="mt-16 flex items-center gap-5 md:mt-20">
                        <h3 class="shrink-0 font-mono text-[10px] uppercase tracking-[0.2em] text-navy-200">{{ $isId ? 'Misi' : 'Mission' }}</h3>
                        <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent" aria-hidden="true"></span>

                        {{-- Penanda butir aktif. Isinya dicetak dari peladen juga,
                             bukan dikosongkan menunggu Alpine: kolom yang kosong
                             sepersekian detik lalu terisi terbaca sebagai halaman
                             yang belum selesai dimuat.

                             aria-hidden: ia mengulang butir yang sudah ada di
                             bawahnya, dan pembaca layar tidak mengikuti kursor. --}}
                        <p class="hidden shrink-0 font-mono text-[10px] uppercase tracking-[0.2em] text-navy-200 sm:block" aria-hidden="true"
                           x-text="String(active + 1).padStart(2, '0') + ' / ' + String(labels.length).padStart(2, '0') + ' — ' + labels[active].toUpperCase()">
                            01 / {{ str_pad($missions->count(), 2, '0', STR_PAD_LEFT) }} &mdash; {{ mb_strtoupper($missions->first()->label) }}
                        </p>
                    </div>

                    <ol class="mt-10 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-5">
                        @foreach ($missions as $i => $mission)
                            {{-- tabindex supaya rel ini bisa dijelajahi papan tik,
                                 dan klik supaya di layar sentuh yang tidak punya
                                 tunjukan tetap ada cara memindahkannya. --}}
                            <li class="relative outline-none"
                                tabindex="0"
                                x-on:mouseenter="active = {{ $i }}"
                                x-on:focus="active = {{ $i }}"
                                x-on:click="active = {{ $i }}"
                                data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 50 }}">

                                {{-- Garis penyambung ke lingkaran berikutnya, memanjang
                                     sampai -1.5rem agar menyeberangi gap-x-6. Hanya dari
                                     lg ke atas: di bawah itu kolomnya dua lalu satu, dan
                                     garis mendatar akan menyambungkan butir yang tidak
                                     bersebelahan. --}}
                                @if (! $loop->last)
                                    <span class="pointer-events-none absolute -right-6 left-11 top-5 hidden h-px overflow-hidden bg-white/10 lg:block" aria-hidden="true">
                                        <span class="misi-line-fill" :data-on="{{ $i }} < active ? '1' : '0'"></span>
                                    </span>
                                @endif

                                {{-- Lingkaran berlatar navy-950 supaya garisnya lewat di
                                     belakang, bukan menembusnya. Warnanya menyatakan
                                     keadaan: putih redup untuk yang belum dilewati, emas
                                     untuk yang sudah, dan yang sedang ditunjuk ditambah
                                     pendar. --}}
                                <span class="misi-node relative grid h-10 w-10 place-items-center rounded-full bg-navy-950 font-mono text-[11px] font-bold tabular-nums ring-1 transition-colors duration-300"
                                      aria-hidden="true"
                                      :data-state="{{ $i }} === active ? 'active' : 'off'"
                                      :class="{{ $i }} <= active ? 'text-gold-soft ring-gold' : 'text-white ring-white/25'">
                                    {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <div class="misi-body" :data-on="active === {{ $i }} ? '1' : '0'">
                                    <p class="mt-5 font-mono text-[9px] uppercase tracking-[0.18em] text-navy-200">{{ $mission->label }}</p>
                                    <h4 class="mt-2 text-pretty font-display text-[17px] font-bold leading-snug text-white">{{ $mission->title }}</h4>
                                    <p class="mt-2.5 text-pretty text-[13px] leading-relaxed text-navy-200">{{ $mission->content }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== FOUNDER ===================== --}}
    {{-- Tanpa lg:min-h-[88vh]. Tinggi yang dipatok ke tinggi layar membuat seksi
         ini jadi bidang navy hampir sepenuh layar; di monitor tinggi, isinya
         mengambang di tengah dengan ruang kosong besar di atas dan bawahnya.
         Jarak seksi biasa sudah memberi napas yang cukup. --}}
    {{-- Latarnya siluet kota, bukan gradien beranimasi. Aurora yang bergerak
         pelan di belakang satu potret membuat matanya terbelah antara wajah dan
         latar; siluet yang diam menahan diri, dan garis-garis gedungnya yang
         samar justru menegakkan potret di depannya.

         Siluetnya digambar dari kode lewat <x-skyline />, bukan dari berkas
         gambar seperti sebelumnya. Berkas 1920x1080 untuk siluet yang isinya
         hanya kotak-kotak adalah satu permintaan jaringan dan satu aset yang
         harus dipelihara untuk sesuatu yang bisa digambar CSS, dan di layar
         selebar apa pun versi kodenya selalu tajam.

         Sumber yang sama dipakai pita Mitra & Klien di beranda, jadi kedua pita
         kota itu tidak bisa menyimpang satu sama lain. --}}
    <section class="relative overflow-hidden py-20 text-white md:py-28">
        <x-skyline />
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        <div class="container relative">
            <div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-16">
                {{-- Portrait --}}
                <div class="relative mx-auto w-full max-w-md lg:col-span-5 lg:mx-0" data-aos="fade-right">
                    {{-- An outlined frame rather than the solid gold slab that used to sit here:
                         a 450x560 block of gold was by far the largest gold surface on the site,
                         against the "gold as a small touch" rule. Same layered depth, hairline area. --}}
                    <div class="pointer-events-none absolute -left-4 -top-4 h-full w-full rounded-2xl border-2 border-gold/70 md:-left-6 md:-top-6" aria-hidden="true"></div>

                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl border border-white/10 shadow-lift">
                        {{-- <picture> dilepas: cadangan PNG-nya 1,7 MB untuk foto yang
                             tidak punya transparansi, dan seluruh gambar situs ini kini
                             WebP. Ukurannya ditulis supaya tata letaknya tidak bergeser
                             saat gambarnya selesai dimuat. --}}
                        <img src="{{ asset('images/pendiri.webp') }}" alt="{{ $founderName }}"
                             width="1082" height="1454" loading="lazy" decoding="async"
                             class="h-full w-full object-cover object-top">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/75 via-navy-950/10 to-transparent"></div>

                        {{-- Name plate on the portrait: on mobile the bio sits far below the photo,
                             so the face arrives without a caption. --}}
                        <div class="absolute inset-x-0 bottom-0 p-5 lg:hidden">
                            <p class="font-display text-base font-bold leading-tight text-white">{{ $founderName }}</p>
                            <p class="mt-1 font-mono text-[10px] uppercase tracking-wider text-gold-soft">{{ $founderRole }}</p>
                        </div>
                    </div>
                </div>

                {{-- Bio --}}
                <div class="lg:col-span-7" data-aos="fade-left" data-aos-delay="100">
                    {{-- Jabatannya satu baris di atas nama, bukan dua baris yang
                         mengapitnya. "Pendiri" dan "CEO" menyebut orang yang sama,
                         dan memisahkannya hanya menambah baris tanpa menambah arti.
                         Ukurannya kecil tapi tidak renggang: token tracking situs
                         ini sengaja bernilai 0, huruf berjarak lebar justru
                         tampilan yang dihindari. --}}
                    <p class="font-mono text-xs text-gold-soft">{{ $founderRole }}</p>

                    {{-- Nama jadi jelas yang terbesar. Sebelumnya nama dan kutipan
                         hampir sama besar, jadi keduanya berebut jadi yang pertama
                         dibaca. Sekarang tangganya menurun rapi: nama, kutipan,
                         riwayat. --}}
                    <h2 class="mt-3 text-2xl font-bold leading-tight text-white md:text-3xl lg:text-[2rem]">{{ $founderName }}</h2>

                    {{-- Kutipan tanpa garis tepi dan tanpa tanda kutip besar: di
                         atas latar kota yang sudah bergaris-garis, penanda tambahan
                         jadi benda keempat yang bersaing. Miring dan warna putih
                         yang sedikit diredupkan sudah cukup memisahkannya dari
                         kalimat riwayat di bawahnya. --}}
                    <blockquote class="mt-6 max-w-xl">
                        <p class="text-pretty italic leading-[1.7] text-navy-100 md:text-[17px]">“{{ $founderQuote }}”</p>
                    </blockquote>

                    {{-- Riwayatnya jadi satu kartu dengan angka pengalaman di
                         sampingnya. Angka itu dulu terkubur di tengah kalimat
                         ("lebih dari 20 tahun"), tempat yang paling tidak mungkin
                         terbaca orang yang sedang memindai. --}}
                    <div class="mt-8 flex max-w-xl flex-col gap-4 rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 sm:flex-row sm:items-center sm:gap-6 sm:p-6">
                        <p class="shrink-0 sm:w-28">
                            <span class="block font-display text-3xl font-bold leading-none text-gold-soft md:text-4xl">{{ $founderYears }}</span>
                            <span class="mt-1.5 block font-mono text-[10px] uppercase text-navy-100">{{ $founderYearsLabel }}</span>
                        </p>
                        <p class="text-pretty text-sm leading-relaxed text-navy-100 sm:border-l sm:border-white/10 sm:pl-6">{{ $founderBio }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== VALUES =====================
         Foto di kiri, daftar nilai di kanan. Kelima nilai ini dulu berdiri di
         satu tulang punggung tegak yang memanjang ke bawah dan membuat seksinya
         jadi kolom sempit sepanjang layar; disandingkan dengan foto, tingginya
         terpakai dan halamannya berhenti menyempit di bagian akhir.

         Tiap baris: nomor dalam lingkaran, judulnya, lalu keterangannya di
         kolom sebelah — bukan di bawahnya. Keterangan yang duduk sebaris
         judulnya membuat judul-judulnya bisa dipindai tanpa melewati paragraf.

         Nomornya dilingkari tipis, tidak diisi pekat: ia penanda tempat, bukan
         isi, dan sebaris bidang gelap akan menyaingi judul yang justru hendak
         dibaca.

         Jumlah barisnya mengikuti config/company.php, jadi tidak ada angka
         yang ditulis di sini dan tidak ada yang perlu disesuaikan kalau
         daftarnya bertambah atau berkurang. --}}
    @php
        $valuesPhoto = 'images/nilai-perusahaan.webp';
        $hasValuesPhoto = is_file(public_path($valuesPhoto));
    @endphp
    <section class="section border-t border-navy-50 bg-white">
        <div class="container">
            <div class="grid gap-8 lg:grid-cols-12 lg:items-end lg:gap-10" data-aos="fade-up">
                <div class="lg:col-span-8">
                    <h2 class="text-display-lg font-bold text-navy">
                        {{ $isId ? 'Nilai yang menuntun cara kami bekerja' : 'The values that guide how we work' }}
                    </h2>
                    {{-- Menyebut sejak kapan dan lewat apa, bukan menyebut betapa
                         pentingnya nilai. Kalimat kedua baru menyambungkannya ke
                         kelima nilai di bawah. --}}
                    <p class="mt-5 max-w-2xl text-pretty leading-relaxed text-slate-600 md:text-lg">
                        {{ $isId
                            ? 'Sejak berdiri pada 2024, Delta Tiga Enam mengembangkan kompetensi SDM lewat pelatihan, sertifikasi profesi, konsultasi, dan headhunter. Nilai berikut yang menentukan cara setiap programnya dijalankan.'
                            : 'Since it began in 2024, Delta Tiga Enam has developed workforce competency through training, professional certification, consulting, and headhunting. The values below decide how every programme is run.' }}
                    </p>
                </div>
                <div class="lg:col-span-4 lg:flex lg:justify-end">
                    <a href="{{ route('services.index') }}" class="btn-blue">
                        {{ $isId ? 'Lihat Semua Layanan' : 'View All Services' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>

            <div class="mt-12 grid gap-10 lg:mt-14 lg:grid-cols-12 lg:items-start lg:gap-14">
                @if ($hasValuesPhoto)
                    <div class="lg:col-span-5" data-aos="fade-up">
                        <div class="aspect-square overflow-hidden rounded-2xl bg-mist ring-1 ring-navy-100">
                            <img src="{{ asset($valuesPhoto) }}"
                                 alt="{{ $isId ? 'Tim dan peserta program Delta Tiga Enam' : 'The Delta Tiga Enam team and programme participants' }}"
                                 width="1000" height="1000" loading="lazy" decoding="async"
                                 class="h-full w-full object-cover">
                        </div>
                    </div>
                @endif

                {{-- Baris yang ditunjuk menyala, dan keadaannya bertahan setelah
                     tunjukan ditarik — sama dengan lini masa Misi di atas.

                     Yang berbeda cara menandainya. Di pita Misi yang gelap, butir
                     tidak aktif cukup diredupkan; di sini latarnya putih, dan
                     meredupkan teks slate-600 ke 0,6 menjatuhkannya ke 2,88:1,
                     jauh di bawah ambang AA. Jadi tidak ada yang diredupkan:
                     yang aktif justru ditambahi — bidang navy-50 tipis, lingkaran
                     yang terisi, dan pendar emas yang sama dengan Misi.

                     Tidak ada isian yang merambat seperti di Misi. Keempat nilai
                     ini tidak berurutan; garis yang terisi sampai titik tertentu
                     akan menjanjikan urutan yang tidak ada. --}}
                <dl class="{{ $hasValuesPhoto ? 'lg:col-span-7' : 'lg:col-span-12' }} border-b border-navy-100"
                    x-data="{ active: 0 }">
                    @foreach ($values as $i => $value)
                        <div class="nilai-row -mx-4 flex flex-col gap-3 rounded-xl border-t border-navy-100 px-4 py-6 outline-none md:flex-row md:items-center md:gap-8 md:py-7"
                             tabindex="0"
                             x-on:mouseenter="active = {{ $i }}"
                             x-on:focus="active = {{ $i }}"
                             x-on:click="active = {{ $i }}"
                             :data-on="active === {{ $i }} ? '1' : '0'"
                             data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 50 }}">
                            {{-- items-center, bukan items-start: nomornya kini sejajar
                                 titik tengah judulnya, bukan menggantung di barisnya
                                 yang pertama. --}}
                            <dt class="flex items-center gap-4 md:w-72 md:shrink-0">
                                {{-- Nomor navy di dalam lingkaran bergaris: 12,6:1
                                     terhadap latar putih; saat aktif ia berbalik jadi
                                     putih di atas navy-600, 6,6:1. aria-hidden karena
                                     urutannya tidak menyimpan arti apa pun di luar
                                     tampilan. --}}
                                <span class="nilai-node grid h-11 w-11 shrink-0 place-items-center rounded-full font-display text-sm font-bold tabular-nums ring-1 transition-colors duration-300"
                                      aria-hidden="true"
                                      :data-on="active === {{ $i }} ? '1' : '0'"
                                      :class="active === {{ $i }} ? 'bg-navy-600 text-white ring-navy-600' : 'text-navy ring-navy-200'">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="text-base font-bold leading-snug text-navy md:text-lg">{{ $value['title'] }}</span>
                            </dt>
                            {{-- 'detail', bukan 'desc'. Kalimat 'desc' sudah tampil
                                 di beranda. --}}
                            <dd class="text-pretty text-sm leading-relaxed text-slate-600">{{ $value['detail'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

</x-layout>
