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

    {{-- ===================== VISION & MISSION ===================== --}}
    <section class="section-sm relative overflow-hidden text-white">
        {{-- Background image + overlay --}}
        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1920&q=80" alt="" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
        {{-- Same gradient treatment as the "Keunggulan" band — image visible through the middle --}}
        <div class="absolute inset-0 bg-gradient-to-b from-navy-950/75 via-navy-950/45 to-navy-950/88"></div>
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-20"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>

        <div class="container relative">
            <div class="mb-10 max-w-2xl" data-aos="fade-up">
                {{-- Token text-display-lg, sama dengan judul seksi lain. font-display
                     tidak ditulis: app.css sudah memberikannya ke setiap h1–h4. --}}
                <h2 class="text-display-lg">{{ $isId ? 'Visi & Misi' : 'Vision & Mission' }}</h2>
            </div>

            {{-- Asymmetric 2fr/3fr rather than two matching glass boxes: the vision is one
                 statement and the mission is a list, so giving them identical containers
                 flattened the difference and buried the vision at body size. --}}
            <div class="grid items-start gap-10 lg:grid-cols-5 lg:gap-14">

                {{-- Vision: set as the statement it is, not as body copy in a card. --}}
                <div class="lg:col-span-2" data-aos="fade-up">
                    {{-- .kicker-on-dark menggantikan gaya yang ditulis sendiri.
                         Yang lama memakai tracking 0,22em, padahal token
                         tracking-label situs ini sengaja bernilai 0. Garis emas
                         kecil di bawah tiap label juga dilepas: dengan garis emas
                         di tepi atas seksi, ada tiga benda emas di satu layar. --}}
                    <p class="kicker-on-dark">{{ $isId ? 'Visi' : 'Vision' }}</p>
                    <blockquote class="mt-5">
                        <p class="font-display text-xl font-semibold leading-[1.45] text-white text-pretty md:text-2xl">
                            “{{ $vision }}”
                        </p>
                    </blockquote>
                </div>

                {{-- Mission: numbered so the count reads at a glance; the em-dash bullets gave
                     no sense of how many commitments there are. --}}
                <div class="lg:col-span-3" data-aos="fade-up" data-aos-delay="100">
                    <p class="kicker-on-dark">{{ $isId ? 'Misi' : 'Mission' }}</p>

                    @if ($missions->isNotEmpty())
                        <ol class="mt-5 divide-y divide-white/10 border-y border-white/10">
                            @foreach ($missions as $mission)
                                <li class="group flex items-start gap-4 py-5 md:gap-5">
                                    {{-- Nomornya dibuat tenang: ia hanya penghitung, dan
                                         isi misinyalah yang harus terbaca lebih dulu.
                                         Karena itu tinggal angka tanpa ubin kaca, dan
                                         ikut turun ke putih redup. --}}
                                    <span class="mt-1 w-6 shrink-0 font-mono text-[11px] font-bold tabular-nums text-gold-soft" aria-hidden="true">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    {{-- Naik dari 15px ke 16/17px dan dari white/90 ke putih
                                         penuh. Ini kalimat inti halaman, tapi sebelumnya ia
                                         jadi teks terkecil di seksinya sendiri, di atas
                                         gradasi yang bagian tengahnya hanya 45% gelap.

                                         Rata kiri di semua ukuran, tidak lagi rata kanan-kiri
                                         mulai sm: perataan penuh di kolom 3/5 membuka sungai
                                         putih di antara kata, dan pemenggalan otomatis yang
                                         dipasang untuk menutupinya justru memotong istilah
                                         seperti "human capital" di tempat yang salah. --}}
                                    <p class="text-base leading-relaxed text-white md:text-[17px]">{{ $mission->content }}</p>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
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

         Bidang navy di atasnya tetap ada supaya gelapnya seragam berapa pun
         tinggi seksinya: gambarnya 16:9, dan di layar sempit ia tidak menutup
         seluruh bidang. --}}
    <section class="relative overflow-hidden bg-navy-950 py-20 text-white md:py-28">
        <img src="{{ asset('images/bg-skyline.webp') }}" alt="" aria-hidden="true"
             width="1920" height="1080" loading="lazy" decoding="async"
             class="pointer-events-none absolute inset-0 h-full w-full object-cover object-bottom">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-navy-950/70 via-navy-950/40 to-navy-950/80"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
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
                        {{-- WebP first: the PNG is 1.7 MB for a photograph with no transparency,
                             the WebP is 53 KB. Dimensions are declared to avoid layout shift. --}}
                        <picture>
                            <source srcset="{{ asset('images/pendiri.webp') }}" type="image/webp">
                            <img src="{{ asset('images/pendiri.png') }}" alt="{{ $founderName }}"
                                 width="1082" height="1454" loading="lazy" decoding="async"
                                 class="h-full w-full object-cover object-top">
                        </picture>
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

                <dl class="{{ $hasValuesPhoto ? 'lg:col-span-7' : 'lg:col-span-12' }} border-b border-navy-100">
                    @foreach ($values as $i => $value)
                        <div class="flex flex-col gap-3 border-t border-navy-100 py-6 md:flex-row md:items-center md:gap-8 md:py-7"
                             data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 50 }}">
                            <dt class="flex items-start gap-4 md:w-72 md:shrink-0">
                                {{-- Nomor navy di dalam lingkaran bergaris: 12,6:1
                                     terhadap latar putih. aria-hidden karena urutannya
                                     tidak menyimpan arti apa pun di luar tampilan. --}}
                                <span class="mt-0.5 grid h-11 w-11 shrink-0 place-items-center rounded-full font-display text-sm font-bold tabular-nums text-navy ring-1 ring-navy-200" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                                <span class="text-lg font-bold leading-snug text-navy md:text-xl">{{ $value['title'] }}</span>
                            </dt>
                            {{-- 'detail', bukan 'desc'. Kalimat 'desc' sudah tampil
                                 di beranda. --}}
                            <dd class="text-pretty text-sm leading-relaxed text-slate-600 md:text-[15px]">{{ $value['detail'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

</x-layout>
