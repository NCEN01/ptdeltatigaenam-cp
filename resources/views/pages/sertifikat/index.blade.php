@php
    /* Anchor tujuan setelah mencari atau berpindah halaman. Diambil dari
       controller supaya id di markup dan fragmen di tautan tidak bisa berbeda. */
    $anchor = App\Http\Controllers\CertificateController::RESULTS_ANCHOR;

    $id = app()->getLocale() === 'id';

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
             dan cakupan daftarnya di kanan, memakai pembatas kolom yang sama
             dengan seksi "Kenapa daftar ini kami buka" di bawah. --}}
        <div class="container relative grid gap-10 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                {{-- Judul seksi turun ukuran dan berganti peran: bukan lagi
                     mengulang judul halaman, melainkan mengajak orang mulai.
                     font-display dan text-balance tidak ditulis: app.css sudah
                     memberikannya ke seluruh h1–h4. --}}
                <h2 class="text-2xl leading-snug text-navy md:text-3xl">
                    {{ $id ? 'Satu nama. Satu nomor. Langsung terbukti.' : 'One name. One number. Proof on the spot.' }}
                </h2>

                {{-- busy dipasang di form, bukan di tombol, supaya keadaan
                     "sedang memeriksa" terlihat sekalipun pengunjung menekan
                     Enter di kolom isian. Tombolnya sengaja tidak dinonaktifkan:
                     menonaktifkan tombol kirim pada peristiwa submit membuat
                     sebagian browser membatalkan pengirimannya. --}}
                <form method="GET" action="{{ route('certificates.index') }}#{{ $anchor }}" class="mt-7"
                      x-data="{ busy: false }" x-on:submit="busy = true" x-bind:aria-busy="busy">
                    <label for="cert-q" class="mb-2 block text-sm font-medium text-navy">
                        {{ $id ? 'Nama peserta, perusahaan, atau nomor sertifikat' : 'Participant name, company, or certificate number' }}
                    </label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            {{-- Placeholder tidak diterjemahkan: isinya contoh nama dan nomor. --}}
                            <input id="cert-q" type="search" name="q" value="{{ $q }}" autocomplete="off"
                                   placeholder="Ahmad Fauzi · PT Baja Perkasa · DTE/BNSP/2026/1000"
                                   class="w-full rounded-2xl border border-navy-200 bg-white py-5 pl-14 pr-5 text-base text-navy transition-colors duration-200 placeholder:text-slate-500 focus:border-sky-600">
                        </div>
                        <button type="submit" class="btn-blue shrink-0 justify-center !rounded-2xl !py-5 active:scale-[0.98] sm:!px-10">
                            <svg x-show="busy" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-opacity="0.3" stroke-width="3"/>
                                <path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                            <span x-text="busy ? @js($id ? 'Memeriksa…' : 'Checking…') : @js($id ? 'Periksa' : 'Check')">{{ $id ? 'Periksa' : 'Check' }}</span>
                        </button>
                    </div>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">
                        {{ $id
                            ? 'Sebagian nama atau sebagian nomor sudah cukup. Hasilnya menampilkan kualifikasi yang diuji dan apakah sertifikatnya masih berlaku hari ini.'
                            : 'Part of a name or part of a number is enough. Results show the qualification assessed and whether the certificate is still valid today.' }}
                    </p>
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
                                   : 'border-navy-200 bg-white text-navy hover:border-sky-600 hover:text-sky-700' }}">
                                {{ $suggestion }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Cakupan daftar. Angka di atas, labelnya tepat di bawah, dipisah
                 garis rambut — bukan tiga kartu statistik bersebelahan: di halaman
                 yang pekerjaannya memverifikasi, angka yang dikotakkan dan
                 diperbesar terdengar seperti pemasaran, bukan bukti.

                 Nilainya tercetak apa adanya di dalam elemennya, jadi tetap
                 terbaca bila animasi penghitungnya tidak berjalan. --}}
            @if ($registry?->total)
                @php
                    $scope = $id ? [
                        [$registry->total, 'sertifikat tercatat'],
                        [$registry->valid, 'masih berlaku hari ini'],
                        [$registry->companies, 'perusahaan berbeda'],
                    ] : [
                        [$registry->total, 'certificates on record'],
                        [$registry->valid, 'still valid today'],
                        [$registry->companies, 'distinct companies'],
                    ];
                @endphp
                <aside class="lg:col-span-5 lg:border-l lg:border-navy-200 lg:pl-10">
                    <h3 class="border-t border-navy-200 pt-8 text-sm font-semibold uppercase tracking-wide text-slate-500 lg:border-t-0 lg:pt-0">
                        {{ $id ? 'Isi daftar ini' : 'Inside this register' }}
                    </h3>
                    <dl class="mt-6 divide-y divide-navy-100 border-y border-navy-100">
                        @foreach ($scope as [$value, $label])
                            {{-- dt sebelum dd mengikuti urutan yang diwajibkan HTML;
                                 flex-col-reverse yang membalik tampilannya, sehingga
                                 angka tampil di atas tanpa merusak strukturnya. --}}
                            <div class="flex flex-col-reverse py-5">
                                <dt class="mt-1 text-sm leading-tight text-slate-600">{{ $label }}</dt>
                                <dd class="font-display text-3xl font-semibold leading-none tabular-nums text-navy" data-counter="{{ $value }}">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
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
                    <span class="text-slate-400" aria-hidden="true">·</span>
                    <span class="text-rose-700">
                        <span class="font-semibold tabular-nums">{{ $expiredCount }}</span>
                        {{ $id ? 'sudah kedaluwarsa' : 'expired' }}
                    </span>
                @endif

                @if ($q !== '')
                    <span class="text-slate-400" aria-hidden="true">·</span>
                    <a href="{{ route('certificates.index') }}#{{ $anchor }}" class="font-medium text-sky-700 underline underline-offset-4 transition-colors hover:text-navy">{{ $id ? 'tampilkan semua' : 'show all' }}</a>
                @endif
            </div>

            {{-- Mobile: daftar kartu — tabel selebar layar memaksa geser ke samping. --}}
            <div class="space-y-3 md:hidden">
                @foreach ($certificates as $c)
                    <article class="rounded-2xl border border-navy-100 bg-white p-5 transition-colors duration-200 ease-out-soft active:bg-neutral-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate font-display text-[15px] font-semibold text-navy">{{ $c->participant_name }}</h3>
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
                                        <span class="font-display text-[15px] font-semibold text-navy">{{ $c->participant_name }}</span>
                                        <x-certificate-status :expires-at="$c->expires_at" class="ml-2 align-middle" />
                                    </td>
                                    <td class="px-6 py-5 text-slate-700">{{ $c->company_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-6 py-5 font-mono text-xs tracking-tight text-slate-600">{{ $c->certificate_number ?: '—' }}</td>
                                    <td class="px-6 py-5">
                                        @if ($c->qualification)
                                            <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">{{ $c->qualification }}</span>
                                        @else <span class="text-slate-400">—</span> @endif
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
                    {{ $id ? 'Kenapa daftar ini kami buka' : 'Why we publish this list' }}
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
                            <span class="font-display text-2xl font-semibold leading-none tabular-nums text-navy-200 sm:text-3xl" aria-hidden="true">{{ sprintf('%02d', $i + 1) }}</span>
                            <div>
                                <h3 class="font-display text-xl font-semibold leading-snug text-navy md:text-2xl">{{ $title }}</h3>
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
                    <h3 class="font-display text-xl font-semibold leading-snug text-navy text-balance md:text-2xl">
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
