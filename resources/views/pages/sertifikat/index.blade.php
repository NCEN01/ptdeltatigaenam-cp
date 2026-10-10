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

    $proof = $id ? [
        ['Diakui secara nasional', 'Sertifikat diterbitkan melalui skema BNSP bersama LSP mitra, sehingga berlaku untuk audit pelanggan, tender, dan persyaratan regulasi.'],
        ['Diuji, bukan sekadar dilatih', 'Setiap nama di daftar ini melewati uji kompetensi bersama asesor, pada pekerjaan dan peralatan yang benar-benar mereka tangani.'],
        ['Dapat ditelusuri siapa pun', 'Nomor sertifikat dan nomor uji kompetensi terbuka di halaman ini, jadi pemberi kerja bisa memverifikasinya sendiri.'],
    ] : [
        ['Nationally recognised', 'Certificates are issued under the BNSP scheme with our partner assessment bodies, so they hold up for customer audits, tenders, and regulatory requirements.'],
        ['Assessed, not just trained', 'Every name here passed an assessment with a certified assessor, on the work and equipment they actually handle.'],
        ['Open to verification', 'Certificate and assessment numbers are published on this page, so any employer can check them directly.'],
    ];
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
                        ['doc', $id ? 'Sertifikat terbit' : 'Certificates issued', $registry->total],
                        ['building', $id ? 'Perusahaan' : 'Companies', $registry->companies],
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
                                @foreach ($registryFigures as [$icon, $label, $value])
                                    <div class="rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 p-4 shadow-lift ring-1 ring-white/10 sm:w-36 sm:p-5">
                                        <span class="block text-sky-400" aria-hidden="true">
                                            @if ($icon === 'doc')
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                                            @else
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v16"/><path d="M15 9h3a2 2 0 0 1 2 2v10"/><path d="M2 21h20M8 7h2M8 11h2M8 15h2"/></svg>
                                            @endif
                                        </span>
                                        <p class="mt-2.5 text-[11px] leading-tight text-navy-100">{{ $label }}</p>
                                        <p class="mt-1.5 font-display text-2xl font-bold leading-none tabular-nums text-white sm:text-[1.75rem]"
                                           data-counter="{{ $value }}">{{ $value }}</p>
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

    {{-- ===================== MENGAPA INI BERARTI + AJAKAN =====================
         Dulu tiga kartu ikon-judul-teks yang seragam. Diganti daftar bergaris:
         isinya argumen, dan argumen tidak perlu dikotakkan satu per satu. --}}
    <section class="section-sm border-t border-navy-50 bg-neutral-50">
        <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <h2 class="text-2xl text-navy md:text-4xl">
                    {{ $id ? 'Kenapa daftar ini dibuka' : 'Why we publish this list' }}
                </h2>

                {{-- Angka besar yang diredupkan di margin kiri. Murni tipografi:
                     tanpa kotak, tanpa lencana, tanpa blok warna. Yang dipandu
                     bukan sekadar hiasan, sebab tiga butir ini memang terhitung,
                     dan <ol> membuat urutannya ikut terbaca pembaca layar.
                     Angkanya sendiri diberi aria-hidden supaya tidak dibacakan
                     dua kali.

                     Butirnya memakai <h3>, bukan <dt>: ini judul yang
                     memperkenalkan paragraf, bukan istilah dengan definisinya,
                     dan sebagai heading ia ikut terbaca mesin pencari. --}}
                <ol class="mt-10 divide-y divide-navy-200 border-t border-navy-200">
                    @foreach ($proof as $i => [$title, $body])
                        <li class="grid grid-cols-[2.25rem_1fr] gap-x-4 py-7 sm:grid-cols-[3.5rem_1fr] sm:gap-x-6">
                            <span class="text-2xl leading-none tabular-nums text-navy-200 sm:text-3xl" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                            <div>
                                <h3 class="text-xl leading-snug text-navy md:text-2xl">{{ $title }}</h3>
                                <p class="mt-2.5 max-w-[62ch] text-pretty leading-relaxed text-slate-600">{{ $body }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Bukan panel berwarna: hanya blok teks yang menempel di kolomnya,
                 dipisahkan garis rambut. Isian gelap di sini akan menyaingi
                 daftar sertifikatnya sendiri, padahal daftar itulah isi halaman. --}}
            {{-- Garis kolom ada di pembungkus yang meregang setinggi baris, bukan
                 di elemen sticky — kalau ditempel di yang sticky, garisnya ikut
                 melorot bersama bloknya alih-alih membatasi kolom. --}}
            <div class="lg:col-span-5 lg:border-l lg:border-navy-200 lg:pl-10">
                <div class="border-t border-navy-200 pt-8 lg:sticky lg:top-28 lg:border-t-0 lg:pt-0">
                    {{-- <h3>, bukan <p>: ini judul yang memperkenalkan blok ajakan,
                         sejajar dengan ketiga butir alasan di kolom sebelahnya. --}}
                    <h3 class="text-xl leading-snug text-navy text-balance md:text-2xl">
                        {{ $id ? 'Ingin nama Anda ada di daftar ini?' : 'Want your name on this list?' }}
                    </h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ $id
                            ? 'Uji kompetensi dapat digelar di tempat kerja Anda, mengikuti pola sif, tanpa menghentikan produksi.'
                            : 'Assessment can run at your workplace, around your shift pattern, without stopping production.' }}
                    </p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row lg:flex-col">
                        <a href="{{ route('services.index') }}" class="btn-blue justify-center">{{ $id ? 'Lihat Program Sertifikasi' : 'View Certification Programs' }}</a>
                        <a href="{{ route('contact.index') }}" class="btn-ghost justify-center">{{ $id ? 'Konsultasi Gratis' : 'Free Consultation' }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
