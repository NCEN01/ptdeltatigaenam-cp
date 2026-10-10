@php
    /* Anchor tujuan setelah mencari atau berpindah halaman. Diambil dari
       controller supaya id di markup dan fragmen di tautan tidak bisa berbeda. */
    $anchor = App\Http\Controllers\CertificateController::RESULTS_ANCHOR;

    $id = app()->getLocale() === 'id';

    /* Ketiga keadaan dan fotonya dihitung di sini, bukan di dalam seksinya:
       keadaannya kini dipakai di kolom kiri sementara fotonya di kolom kanan,
       jadi tidak ada satu seksi pun yang bisa memilikinya sendiri. */
    $registryPhoto = 'images/sertifikat-verifikasi.webp';
    $hasRegistryPhoto = is_file(public_path($registryPhoto));

    $states = $registry?->total ? ($id ? [
        ['bg-sky-500', 'Berlaku', $registry->valid],
        ['bg-amber-500', 'Segera berakhir', $registry->soon],
        ['bg-rose-500', 'Kedaluwarsa', $registry->expired],
    ] : [
        ['bg-sky-500', 'Valid', $registry->valid],
        ['bg-amber-500', 'Expiring soon', $registry->soon],
        ['bg-rose-500', 'Expired', $registry->expired],
    ]) : [];

    /* Empat butir, bukan tiga. Butir keempat bukan tambahan pengisi ruang: dua
       kolom berisi dua kartu yang mengapit satu foto memang menuntut jumlah
       genap, dan masa berlaku adalah hal yang memang ditanyakan orang setelah
       memastikan sertifikatnya ada — halaman ini menjawabnya di tiap baris
       tetapi tidak pernah menyebutkannya. */
    $proof = $id ? [
        ['seal', 'Diakui secara nasional', 'Diterbitkan lewat skema BNSP bersama LSP mitra, jadi berlaku untuk audit pelanggan, tender, dan persyaratan regulasi.'],
        ['check', 'Diuji, bukan sekadar dilatih', 'Setiap nama di sini melewati uji kompetensi bersama asesor, pada pekerjaan dan peralatan yang benar-benar mereka tangani.'],
        ['search', 'Dapat ditelusuri siapa pun', 'Nomor sertifikat dan nomor uji kompetensi terbuka di halaman ini. Pemberi kerja memeriksanya sendiri, tanpa menghubungi kami.'],
        ['clock', 'Masa berlakunya ikut terbuka', 'Tiap baris menyebut status berlakunya, jadi yang terlihat bukan hanya sertifikatnya ada, tetapi juga masih berlaku hari ini.'],
    ] : [
        ['seal', 'Nationally recognised', 'Issued under the BNSP scheme with our partner assessment bodies, so it holds up for customer audits, tenders, and regulatory requirements.'],
        ['check', 'Assessed, not just trained', 'Every name here passed an assessment with a certified assessor, on the work and equipment they actually handle.'],
        ['search', 'Open to anyone', 'Certificate and assessment numbers are published on this page. Employers check them directly, without contacting us.'],
        ['clock', 'Validity is published too', 'Every row states its status, so what you see is not only that a certificate exists, but that it still holds today.'],
    ];

    $proofPhoto = 'images/sertifikat-alasan.webp';
    $hasProofPhoto = is_file(public_path($proofPhoto));
@endphp

{{-- Judul dan deskripsi mengikuti maksud pencarian orang yang mendarat di sini.
     Sebelumnya deskripsi tidak diisi sama sekali, sehingga layout memakai
     tagline umum perusahaan — sama persis dengan belasan halaman lain. --}}
<x-layout
    :title="$id ? 'Cek Keaslian Sertifikat Kompetensi' : 'Verify a Competency Certificate'"
    :description="$id
        ? 'Cek keaslian sertifikat kompetensi BNSP terbitan PT Delta Tiga Enam. Masukkan nama peserta, nama perusahaan, atau nomor sertifikat untuk melihat kualifikasi yang diuji dan status berlakunya.'
        : 'Verify BNSP competency certificates issued by PT Delta Tiga Enam. Enter a participant name, company, or certificate number to see the qualification assessed and whether it is still valid.'">
    {{-- Judulnya menyebut pekerjaan yang dicari orang, bukan nama daftarnya.
         Sebelumnya h1 berbunyi "Pemegang Sertifikat" lalu beberapa piksel di
         bawahnya h2 berbunyi "Cek Keaslian Sertifikat Kompetensi": dua judul
         besar berurutan yang mengatakan hal serupa, dan kata kunci yang
         sebenarnya dicari orang justru berada di judul yang lebih rendah.
         Sekarang h1 memikulnya, dan seksi pencarian di bawah tinggal mengajak. --}}
    <x-page-header
        :title="$id ? 'Cek Keaslian Sertifikat Kompetensi' : 'Verify a Competency Certificate'"
        :subtitle="$id
            ? 'Daftar terbuka pemegang sertifikat BNSP terbitan PT Delta Tiga Enam. Siapa pun boleh memeriksanya, tanpa perlu menghubungi kami dulu.'
            : 'The open register of BNSP certificate holders issued by PT Delta Tiga Enam. Anyone may check it, without contacting us first.'"
        placement="certificate"
        image="photo-1524178232363-1fb2b075b655" />

    {{-- ===================== PENCARIAN =====================
         Halaman ini dipakai orang untuk satu hal: memeriksa apakah sebuah
         sertifikat benar ada dan masih berlaku. Jadi pencarian yang dulu
         terselip sebagai baris alat kecil kini memimpin halaman.

         Blok tiga angka besar (Terdaftar / Masih berlaku / Kedaluwarsa) pernah
         berdiri di sini lalu dihapus karena dua sebab nyata: angkanya ikut
         menyusut saat orang mencari, sehingga berbunyi "1 1 0" — tiga angka
         terbesar di layar untuk mengatakan nyaris tidak ada apa-apa; dan
         "Terdaftar 25" terbaca membantah klaim situs sendiri yang menulis
         "500+ Profesional Terlatih" di beranda.

         Cakupan daftarnya kembali di kolom kanan, tetapi kedua sebab itu
         ditutup lebih dulu. Angkanya dihitung atas seluruh daftar dan tidak
         pernah mengikuti kata kunci, jadi tidak bisa menyusut jadi "1".
         Kata-katanya pun menyebut "tercatat di daftar ini", bukan "profesional
         terlatih": daftar verifikasi yang terbuka memang bagian kecil dari
         seluruh peserta yang pernah dilatih, dan menyebutnya begitu menghapus
         pertentangannya. --}}
    {{-- Latar putih dengan sapuan cahaya lembut, bukan blok abu rata: memberi
         kedalaman tanpa isian warna, dan kolom pencarian yang putih justru makin
         menonjol di atasnya. Pola yang sama sudah dipakai halaman Layanan. --}}
    <section class="section-sm relative overflow-hidden border-b border-navy-50 bg-white">
        <div class="pointer-events-none absolute inset-0 aurora-light opacity-80"></div>

        {{-- Dua kolom, bukan satu tumpukan. Sebelumnya judul, formulir, pintasan,
             dan angka cakupan berderet ke bawah sepanjang satu kolom sempit,
             menyisakan separuh kanan layar kosong. Kini alat pencariannya di kiri
             dan cakupan daftarnya di kanan.

             Pembatas kolom tegaknya dilepas. Sejak kolom kanan dibuka sebuah foto,
             garis itu jadi dinding di antara dua benda yang sudah jelas terpisah
             batasnya sendiri. --}}
        {{-- Rata atas dengan jarak tetap, bukan rata tengah. Tinggi kolom kiri
             berubah mengikuti ada-tidaknya pintasan dan papan angka, dan rata
             tengah membuat letak fotonya ikut berpindah-pindah bersamanya.
             Jarak 4rem menurunkan foto sampai kira-kira sebaris bilah pencarian,
             sekaligus menyisakan ruang kosong di atas kartu angka yang
             menumpang di tepi atasnya. --}}
        <div class="container relative grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-14">
            <div class="lg:col-span-7">
                {{-- Judul seksi berganti peran: bukan lagi mengulang judul halaman,
                     melainkan mengajak orang mulai. Tiga kalimat dipadatkan jadi dua
                     kata — di atas sebuah bilah pencarian, kalimat panjang hanya
                     menunda orang sampai ke kolom isiannya.

                     Aksen miringnya navy, bukan biru. Pasangan miring-biru sudah
                     dipakai di judul seksi lain halaman ini; di sini yang dibutuhkan
                     hanya penegasan bentuk, bukan warna kedua. --}}
                <x-heading
                    :lead="$id ? 'Satu nama' : 'One name'"
                    :accent="$id ? 'terbukti' : 'proven'"
                    accent-class="text-navy"
                    class="text-display-lg font-bold text-navy" />

                {{-- busy dipasang di form, bukan di tombol, supaya keadaan
                     "sedang memeriksa" terlihat sekalipun pengunjung menekan
                     Enter di kolom isian. Tombolnya sengaja tidak dinonaktifkan:
                     menonaktifkan tombol kirim pada peristiwa submit membuat
                     sebagian browser membatalkan pengirimannya. --}}
                <form method="GET" action="{{ route('certificates.index') }}#{{ $anchor }}" class="mt-7"
                      x-data="{ busy: false }" x-on:submit="busy = true" x-bind:aria-busy="busy">
                    <label for="cert-q" class="mb-3 block text-sm text-slate-600">
                        {{ $id ? 'Cari nama peserta, perusahaan, atau nomor sertifikat' : 'Search by participant name, company, or certificate number' }}
                    </label>

                    {{-- Kolom isian dan tombol menjadi satu benda, bukan dua kotak
                         bersebelahan berjarak. Bilah pencarian adalah pekerjaan
                         halaman ini, jadi ia pantas terbaca sebagai satu alat.

                         Bentuknya kapsul penuh mulai dari sm, mengikuti acuan yang
                         dikirim. Di bawah sm tombolnya turun ke baris sendiri dan
                         pembungkusnya jadi kotak tinggi — kapsul pada bentuk itu
                         melengkung berlebihan, jadi di sana jari-jarinya 24px.

                         Bayangan sengaja tidak dipasang saat diam: garis 1px plus
                         bayangan lebar adalah hiasan kartu hantu. Cincinnya hanya
                         muncul saat fokus, di situ ia menjadi penanda keadaan. --}}
                    <div class="rounded-3xl border border-navy-200 bg-white p-1.5 transition duration-200 focus-within:border-sky-700 focus-within:ring-4 focus-within:ring-sky-700/20 sm:flex sm:items-center sm:gap-2 sm:rounded-full">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-600" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            {{-- Placeholder tidak diterjemahkan: isinya contoh nama dan nomor. --}}
                            <input id="cert-q" type="search" name="q" value="{{ $q }}" autocomplete="off"
                                   placeholder="Ahmad Fauzi · PT Baja Perkasa · DTE/BNSP/2026/1000"
                                   class="w-full rounded-full border-0 bg-transparent py-3.5 pl-12 pr-4 text-[15px] text-navy placeholder:text-slate-500 focus:outline-none focus:ring-0">
                        </div>
                        <button type="submit" class="btn-blue mt-2 w-full justify-center !rounded-full !py-3.5 active:scale-[0.98] sm:mt-0 sm:w-auto sm:!px-8">
                            <svg x-show="busy" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.3" stroke-width="3"/>
                                <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                            <span x-text="busy ? @js($id ? 'Memeriksa…' : 'Checking…') : @js($id ? 'Periksa' : 'Check')">{{ $id ? 'Periksa' : 'Check' }}</span>
                        </button>
                    </div>
                </form>

                {{-- Pintasan dari kualifikasi yang paling banyak dipegang. Bukan hiasan:
                     pengunjung yang belum tahu harus mengetik apa jadi punya titik
                     mulai, dan seksi ini menunjukkan isi daftarnya tanpa diklaim. --}}
                @if ($suggestions->isNotEmpty())
                    <div class="mt-7 flex flex-wrap items-center gap-2 text-sm">
                        <span class="mr-1 text-slate-500">{{ $id ? 'Cari cepat:' : 'Quick search:' }}</span>
                        @foreach ($suggestions as $suggestion)
                            @php $active = $q === $suggestion; @endphp
                            <a href="{{ route('certificates.index', ['q' => $suggestion]) }}#{{ $anchor }}"
                               @if ($active) aria-current="true" @endif
                               class="rounded-full border px-3.5 py-1.5 font-medium transition duration-200 active:scale-[0.97] {{ $active
                                   ? 'border-navy bg-navy text-white'
                                   : 'border-navy-200 bg-white text-navy hover:border-sky-700 hover:text-sky-700' }}">
                                {{ $suggestion }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Ketiga keadaan pindah ke kolom ini dan memakai susunan papan
                     angka yang sama dengan seksi Kategori Layanan di beranda:
                     angka besar di atas, keterangan di bawahnya, dipisah garis
                     rambut tegak. Satu susunan angka untuk seluruh situs, bukan
                     satu per halaman.

                     Titik warnanya tetap ada di samping keterangan: warna yang sama
                     dipakai lencana status di kolom hasil, jadi papan ini sekaligus
                     mengajarkan cara membaca hasilnya sebelum hasilnya muncul.

                     Nilainya tercetak apa adanya di dalam elemennya, jadi tetap
                     terbaca bila animasi penghitungnya tidak berjalan. --}}
                @if ($states)
                    <div class="mt-9 grid grid-cols-3 gap-x-4 border-t border-navy-100 pt-7 sm:gap-x-8">
                        @foreach ($states as $i => [$dot, $label, $value])
                            <div @class(['border-l border-navy-100 pl-4 sm:pl-8' => $i > 0])>
                                <p class="font-display text-3xl font-bold leading-none tracking-tight tabular-nums text-navy sm:text-4xl"
                                   data-counter="{{ $value }}">{{ $value }}</p>
                                <p class="mt-2.5 flex items-center gap-2 text-sm leading-snug text-slate-600">
                                    <span class="h-2 w-2 shrink-0 rounded-full {{ $dot }}" aria-hidden="true"></span>
                                    {{ $label }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Kolom kanan dibuka sebuah foto orang yang baru memegang sertifikatnya.
                 Halaman ini seluruhnya berisi nomor, tabel, dan lencana status; satu
                 foto mengingatkan bahwa di balik tiap baris itu ada orang, dan itu
                 yang membuat daftarnya layak dipercaya, bukan rapinya tabel.

                 Dua angka cakupan menumpang di tepi atas foto sebagai kartu, susunan
                 yang sama dengan papan angka di halaman Tentang. Keduanya menjawab
                 "seberapa besar daftar ini" tanpa menyita baris sendiri di bawah foto.

                 Kalimat "terbuka untuk diperiksa siapa pun" dilepas dari sini: subjudul
                 halaman di atas sudah mengatakan hal yang sama dengan kalimat yang
                 lebih panjang, dan mengulanginya di layar yang sama tidak menambah
                 apa pun. --}}
            @if ($hasRegistryPhoto)
                @php
                    $registryFigures = $registry?->total ? [
                        [$id ? 'Sertifikat terbit' : 'Certificates issued', $registry->total],
                        [$id ? 'Perusahaan' : 'Companies', $registry->companies],
                    ] : [];
                @endphp
                <aside class="lg:col-span-5 lg:mt-16" aria-label="{{ $id ? 'Cakupan daftar sertifikat' : 'Coverage of the register' }}">
                    <div class="relative">
                        <div class="aspect-[4/3] overflow-hidden rounded-2xl bg-mist ring-1 ring-navy-100">
                            <img src="{{ asset($registryPhoto) }}"
                                 alt="{{ $id ? 'Peserta memegang sertifikat kompetensi seusai uji kompetensi' : 'A participant holding a competency certificate after assessment' }}"
                                 width="1200" height="900" loading="lazy" decoding="async"
                                 class="h-full w-full object-cover">
                        </div>

                        {{-- Di layar sempit kartunya turun ke bawah foto: menumpuk di
                             sudut kanan atas foto selebar layar ponsel akan menutupi
                             wajah orang di dalamnya. --}}
                        @if ($registryFigures)
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:absolute sm:-top-6 sm:right-5 sm:mt-0 sm:flex sm:gap-3">
                                @foreach ($registryFigures as [$label, $value])
                                    {{-- Angka dulu, keterangannya di bawah: di kartu sekecil
                                         ini angkanyalah yang dicari mata, dan keterangan di
                                         atasnya membuat mata membaca dua kali. --}}
                                    <div class="rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 p-4 shadow-lift ring-1 ring-white/10 sm:w-36 sm:p-5">
                                        <p class="font-display text-2xl font-bold leading-none tabular-nums text-white sm:text-[1.75rem]"
                                           data-counter="{{ $value }}">{{ $value }}</p>
                                        <p class="mt-2 text-[11px] leading-tight text-navy-100">{{ $label }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </aside>
            @endif
        </div>
    </section>

    {{-- ===================== HASIL ===================== --}}
    <section id="{{ $anchor }}" class="section-sm scroll-mt-28 bg-white">
        <div class="container">

            @if ($certificates->isEmpty())
                {{-- Satu keadaan kosong untuk kedua tata letak. Kalau ditaruh di
                     masing-masing @empty, colspan tabel harus ikut dijaga setiap
                     kali jumlah kolomnya berubah. --}}
                <x-certificate-empty :searching="$q !== ''" />
            @else

            {{-- Keterangan hasil. Menjawab pertanyaan yang benar-benar muncul —
                 "berapa yang cocok, dan apakah ada yang sudah kedaluwarsa" —
                 dengan satu baris, bukan tiga angka sebesar judul. Jumlah
                 kedaluwarsa hanya disebut kalau memang ada. --}}
            <div class="mb-6 flex flex-wrap items-baseline gap-x-2 gap-y-1 text-sm text-slate-600">
                @if ($q !== '')
                    <span>
                        <span class="font-semibold tabular-nums text-navy">{{ $certificates->total() }}</span>
                        {{ $id ? 'hasil untuk' : 'results for' }}
                        <span class="font-semibold text-navy">&ldquo;{{ $q }}&rdquo;</span>
                    </span>
                @else
                    <span>
                        {{ $id ? 'Menampilkan' : 'Showing' }}
                        <span class="font-semibold tabular-nums text-navy">{{ $certificates->firstItem() }}&ndash;{{ $certificates->lastItem() }}</span>
                        {{ $id ? 'dari' : 'of' }}
                        <span class="font-semibold tabular-nums text-navy">{{ $certificates->total() }}</span>
                        {{ $id ? 'sertifikat' : 'certificates' }}
                    </span>
                @endif

                @if ($expiredCount > 0)
                    <span class="text-slate-600" aria-hidden="true">·</span>
                    <span class="text-rose-700">
                        <span class="font-semibold tabular-nums">{{ $expiredCount }}</span>
                        {{ $id ? 'sudah kedaluwarsa' : 'expired' }}
                    </span>
                @endif

                @if ($q !== '')
                    <span class="text-slate-600" aria-hidden="true">·</span>
                    <a href="{{ route('certificates.index') }}#{{ $anchor }}" class="font-medium text-sky-700 underline underline-offset-4 transition-colors hover:text-navy">{{ $id ? 'tampilkan semua' : 'show all' }}</a>
                @endif
            </div>

            {{-- Mobile: daftar kartu — tabel selebar layar memaksa geser ke samping. --}}
            <div class="space-y-3 md:hidden">
                @foreach ($certificates as $c)
                    <article class="rounded-2xl border border-navy-100 bg-white p-5 transition-colors duration-200 ease-out-soft active:bg-neutral-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-[15px] text-navy">{{ $c->participant_name }}</h3>
                                <p class="mt-0.5 truncate text-xs text-slate-600">{{ $c->company_name ?: '—' }}</p>
                            </div>
                            <x-certificate-status :expires-at="$c->expires_at" class="shrink-0" />
                        </div>

                        @if ($c->qualification)
                            <span class="mt-3 inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">{{ $c->qualification }}</span>
                        @endif

                        {{-- Urutan dan label sama dengan tabel: No. UJK lalu No. Sertifikat.
                             Tgl. Berakhir dihapus di kedua tata letak. --}}
                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 border-t border-navy-50 pt-3 text-xs">
                            <div class="min-w-0">
                                <dt class="text-[11px] text-slate-500">{{ $id ? 'No. UJK' : 'Reg. No.' }}</dt>
                                <dd class="truncate font-mono text-slate-700">{{ $c->ujk_number ?: '—' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-[11px] text-slate-500">{{ $id ? 'No. Sertifikat' : 'Certificate No.' }}</dt>
                                <dd class="truncate font-mono text-slate-700">{{ $c->certificate_number ?: '—' }}</dd>
                            </div>
                        </dl>
                    </article>
                @endforeach
            </div>

            {{-- Tabel (tablet & desktop). Keenam kolom asli dipertahankan apa
                 adanya — urutan, judul, dan isi selnya. Yang berubah hanya
                 kepala tabelnya: dari isian navy-anim menjadi terang. --}}
            <div class="hidden overflow-hidden rounded-2xl border border-navy-100 md:block" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] text-left text-sm">
                        <caption class="sr-only">{{ $id ? 'Daftar pemegang sertifikat kompetensi' : 'List of competency certificate holders' }}</caption>
                        {{-- Judul kolom terang dengan garis bawah tegas, bukan isian
                             abu-abu: kepala tabel berhenti menjadi balok dan daftar
                             namanya yang memimpin. --}}
                        <thead>
                            <tr class="border-b-2 border-navy-100 bg-white text-slate-500">
                                <th scope="col" class="px-6 py-4 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'No. UJK' : 'Reg. No.' }}</th>
                                <th scope="col" class="px-6 py-4 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Peserta' : 'Participant' }}</th>
                                <th scope="col" class="px-6 py-4 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Nama Perusahaan' : 'Company' }}</th>
                                <th scope="col" class="px-6 py-4 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'No. Sertifikat' : 'Certificate No.' }}</th>
                                <th scope="col" class="px-6 py-4 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Kualifikasi' : 'Qualification' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($certificates as $c)
                                {{-- Baris berselang samar memandu mata menyusuri lima kolom
                                     tanpa perlu garis di setiap baris. --}}
                                <tr class="transition-colors duration-200 ease-out-soft even:bg-neutral-50/60 hover:bg-sky-50/50">
                                    <td class="whitespace-nowrap px-6 py-5 font-mono text-xs tracking-tight text-slate-500">{{ $c->ujk_number ?: '—' }}</td>
                                    {{-- Status menempel pada nama, sama seperti kartu mobile.
                                         Kolom Tgl. Berakhir dihapus, tetapi penanda berlaku /
                                         kedaluwarsa tetap perlu ada: tanpanya halaman ini
                                         berhenti menjadi alat verifikasi. --}}
                                    <td class="px-6 py-5">
                                        <span class="text-[15px] text-navy">{{ $c->participant_name }}</span>
                                        <x-certificate-status :expires-at="$c->expires_at" class="ml-2 align-middle" />
                                    </td>
                                    <td class="px-6 py-5 text-slate-700">{{ $c->company_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-5 font-mono text-xs tracking-tight text-slate-600">{{ $c->certificate_number ?: '—' }}</td>
                                    <td class="px-6 py-5">
                                        @if ($c->qualification)
                                            <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">{{ $c->qualification }}</span>
                                        @else <span class="text-slate-600">—</span> @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($certificates->hasPages())
                <div class="mt-8">{{ $certificates->links('pagination.brand') }}</div>
            @endif
            @endif
        </div>
    </section>

    {{-- ===================== KENAPA DAFTAR INI DIBUKA + AJAKAN =====================
         Pita navy bergradasi, bukan latar terang. Lapisnya sama persis dengan seksi
         Pendiri di halaman Tentang: bg-navy-anim, aurora, butiran, dan garis emas
         di tepi atas. Ini seksi terakhir sebelum footer yang juga navy; keduanya
         menyatu jadi satu kaki halaman, dan daftar sertifikat di atasnya tetap jadi
         bagian paling terang di layar, yang memang isi halaman.

         Empat kartu mengapit satu foto di tengah: dua di kiri, dua di kanan. Isinya
         argumen tentang orang sungguhan, dan foto di tengahnya menahan seksi ini
         supaya tidak jadi empat kotak teks berderet.

         Penomoran 01/02/03 dilepas. Keempat butir ini tidak berurutan — tidak ada
         yang harus dibaca lebih dulu — dan angka yang tidak menandai urutan apa pun
         hanya hiasan yang menyamar jadi struktur. <ul> tetap dipakai supaya
         pembaca layar tahu ini satu daftar.

         Ajakannya turun ke bawah seksi, bukan berdiri di kolom sebelahnya. Di
         kolom, ia bersaing dengan keempat alasan itu; di bawah, ia dibaca sesudah
         alasannya selesai, yaitu saat orang memang sudah bisa memutuskan. --}}
    <section class="relative overflow-hidden bg-navy-anim text-white">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-35"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-30"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        <div class="container relative section-sm">
            <h2 class="max-w-2xl text-display-lg text-white" data-aos="fade-up">
                {{ $id ? 'Kenapa daftar ini dibuka' : 'Why this register is public' }}
            </h2>

            @php
                /* Dibelah dua supaya kolom kiri dan kanan bisa mengapit fotonya.
                   Di layar sempit urutan bacanya tetap benar: dua kartu, foto, dua
                   kartu berikutnya. */
                $proofLeft = array_slice($proof, 0, 2);
                $proofRight = array_slice($proof, 2);

                $proofIcons = [
                    'seal' => '<path d="M12 3l2.1 1.5 2.5-.3 1 2.3 2.2 1.3-.7 2.5.7 2.5-2.2 1.3-1 2.3-2.5-.3L12 18l-2.1-1.4-2.5.3-1-2.3L4.2 13l.7-2.5-.7-2.5 2.2-1.3 1-2.3 2.5.3z"/><path d="m9.4 10.8 1.9 1.9 3.3-3.6"/>',
                    'check' => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1z"/><path d="M8 6H6.5A1.5 1.5 0 0 0 5 7.5v11A1.5 1.5 0 0 0 6.5 20h11a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 17.5 6H16"/><path d="m9 13 2 2 4-4.5"/>',
                    'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.6-3.6"/>',
                    'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 1.8"/>',
                ];
            @endphp

            <div class="mt-12 grid items-stretch gap-6 md:mt-14 lg:grid-cols-12 lg:gap-8">
                @foreach ([$proofLeft, $proofRight] as $column)
                    {{-- Foto disisipkan di antara kedua kolom. Ditulis sekali di dalam
                         perulangan, bukan dua blok kolom yang disalin. --}}
                    @if ($loop->index === 1 && $hasProofPhoto)
                        <div class="lg:col-span-4" data-aos="fade-up" data-aos-delay="50">
                            <div class="aspect-[3/4] overflow-hidden rounded-2xl ring-1 ring-white/10 lg:aspect-auto lg:h-full">
                                <img src="{{ asset($proofPhoto) }}"
                                     alt="{{ $id ? 'Asesor memandu sesi uji kompetensi' : 'An assessor leading a competency assessment session' }}"
                                     width="900" height="1200" loading="lazy" decoding="async"
                                     class="h-full w-full object-cover">
                            </div>
                        </div>
                    @endif

                    <ul class="flex flex-col gap-6 {{ $hasProofPhoto ? 'lg:col-span-4' : 'lg:col-span-6' }}">
                        @foreach ($column as $i => [$icon, $title, $body])
                            <li class="flex flex-1 flex-col rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 md:p-7"
                                data-aos="fade-up" data-aos-delay="{{ $i * 50 }}">
                                <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-sky-400 ring-1 ring-white/10" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">{!! $proofIcons[$icon] !!}</svg>
                                </span>
                                <h3 class="mt-5 text-lg leading-snug text-white md:text-xl">{{ $title }}</h3>
                                <p class="mt-2.5 text-pretty text-sm leading-relaxed text-navy-100">{{ $body }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>

            {{-- Ajakannya satu baris penuh di bawah, dipisah garis rambut: ajakan
                 bukan alasan kelima, jadi ia tidak boleh terbaca sebagai kartu
                 kelima. --}}
            <div class="mt-14 grid gap-6 border-t border-white/10 pt-10 lg:grid-cols-12 lg:items-center lg:gap-10" data-aos="fade-up">
                <div class="lg:col-span-7">
                    <h3 class="text-2xl leading-snug text-white text-balance md:text-3xl">
                        {{ $id ? 'Ingin nama Anda ada di daftar ini?' : 'Want your name on this list?' }}
                    </h3>
                    <p class="mt-3 max-w-xl text-pretty leading-relaxed text-navy-100">
                        {{ $id
                            ? 'Uji kompetensi dapat digelar di tempat kerja Anda, mengikuti pola sif, tanpa menghentikan produksi.'
                            : 'Assessment can run at your workplace, around your shift pattern, without stopping production.' }}
                    </p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row lg:col-span-5 lg:justify-end">
                    <a href="{{ route('services.index') }}" class="btn-blue justify-center">{{ $id ? 'Lihat Program Sertifikasi' : 'View Certification Programs' }}</a>
                    <a href="{{ route('contact.index') }}" class="btn-ghost-light justify-center">{{ $id ? 'Konsultasi Gratis' : 'Free Consultation' }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
