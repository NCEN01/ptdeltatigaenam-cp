@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.services')">
    <x-page-header
        :title="__('site.home.services_title')"
        :subtitle="$id ? 'Dari konsultasi manajemen hingga sertifikasi kompetensi, dirancang untuk hasil yang terukur.' : 'From management consulting to competency certification, designed for measurable outcomes.'"
        placement="services"
        image="photo-1524178232363-1fb2b075b655" />

    {{-- ===================== CARA MENGIKUTI =====================
         Kolom mode (online / offline / hybrid) sudah ada di data dan tampil pada
         kartu jadwal di halaman detail, tetapi tidak pernah dijelaskan artinya.
         Seksi ini menjelaskannya sebelum pengunjung menemui lencana itu.

         Disajikan sebagai tiga kartu, dan sengaja tidak kembar: yang paling
         banyak dipakai dibuat gelap sebagai penanda, dua sisanya terang. --}}
    @php
        /* Satu peta, bukan satu kunci 'img' di enam entri: daftar $modes ditulis
           dua kali (ID dan EN) dan gambarnya sama untuk keduanya, jadi menaruhnya
           di dalam entri berarti menyimpan nilai yang sama enam kali.

           Berkasnya diperiksa keberadaannya sebelum dipakai. Gambar ini akan
           diganti pemilik dengan foto aslinya; kalau salah satunya belum ada,
           kartunya tampil tanpa foto, bukan dengan gambar yang patah. */
        $modeImages = [
            'Offline' => 'images/mode-offline.webp',
            'Online' => 'images/mode-online.webp',
            'Hybrid' => 'images/mode-hybrid.webp',
        ];

        /* Offline diletakkan lebih dulu dan ditandai unggulan karena memang
           mode yang paling banyak dipakai pada data: tujuh dari sepuluh layanan.
           Menonjolkan online justru akan menjanjikan sesuatu yang belum ada
           programnya. */
        $modes = $id ? [
            [
                'label' => 'Offline', 'icon' => 'users', 'featured' => true,
                'lead' => 'Tatap muka penuh, di lokasi kerja Anda atau di ruang pelatihan kami.',
                'points' => [
                    'Praktik pada alat dan proses yang dipakai sehari-hari',
                    'Pengajar hadir langsung mendampingi peserta',
                    'Uji kompetensi digelar di tempat yang sama',
                    'Jadwal dapat mengikuti pola sif, produksi tetap jalan',
                    'Sertifikat BNSP bagi yang dinyatakan kompeten',
                ],
            ],
            [
                'label' => 'Online', 'icon' => 'screen', 'featured' => false,
                'lead' => 'Daring penuh, diikuti dari mana saja dengan koneksi yang stabil.',
                'points' => [
                    'Kelas langsung bersama pengajar, bukan rekaman',
                    'Studi kasus dan simulasi dikerjakan bersama',
                    'Uji kompetensi daring bersama asesor',
                    'Bukti kerja diserahkan secara digital',
                    'Cocok untuk tim yang tersebar di banyak lokasi',
                ],
            ],
            [
                'label' => 'Hybrid', 'icon' => 'layers', 'featured' => false,
                'lead' => 'Teori daring, praktik dan uji kompetensi di lokasi Anda.',
                'points' => [
                    'Materi dasar dituntaskan lebih dulu secara daring',
                    'Pertemuan langsung dipakai penuh untuk praktik',
                    'Uji kompetensi pada hari yang sama dengan praktik',
                    'Waktu peserta meninggalkan pekerjaan lebih singkat',
                    'Cocok untuk program berdurasi panjang',
                ],
            ],
        ] : [
            [
                'label' => 'Offline', 'icon' => 'users', 'featured' => true,
                'lead' => 'Fully in person, at your workplace or in our training rooms.',
                'points' => [
                    'Practice on the equipment and processes used daily',
                    'The instructor is present and works alongside participants',
                    'Assessment is held at the same location',
                    'Scheduling can follow your shift pattern, production keeps running',
                    'BNSP certificate for those judged competent',
                ],
            ],
            [
                'label' => 'Online', 'icon' => 'screen', 'featured' => false,
                'lead' => 'Fully remote, joined from anywhere with a stable connection.',
                'points' => [
                    'Live classes with the instructor, not recordings',
                    'Case studies and simulations worked through together',
                    'Assessment held online with a certified assessor',
                    'Work evidence submitted digitally',
                    'Suits teams spread across several sites',
                ],
            ],
            [
                'label' => 'Hybrid', 'icon' => 'layers', 'featured' => false,
                'lead' => 'Theory online, practice and assessment at your site.',
                'points' => [
                    'Core material is covered online first',
                    'In-person days are spent entirely on practice',
                    'Assessment on the same day as the practical session',
                    'Participants spend less time away from the job',
                    'Suits longer programmes',
                ],
            ],
        ];
    @endphp

    <section class="section-sm border-b border-navy-50 bg-white">
        <div class="container">
            <div class="max-w-2xl">
                {{-- Token text-display-lg dengan aksen miring berwarna, sama dengan
                     judul seksi di seluruh situs. Pasangan text-3xl/md:text-4xl
                     sebelumnya melompat sekali di satu titik putus lalu diam. --}}
                <h2 class="text-display-lg text-navy" data-aos="fade-up">{{ $id ? 'Tiga cara mengikuti program' : 'Three ways to take a programme' }}</h2>
                <p class="mt-5 text-pretty leading-relaxed text-slate-600" data-aos="fade-up">
                    {{ $id
                        ? 'Program yang sama dapat dijalankan dengan tiga cara. Yang berbeda hanya tempat dan bentuk praktiknya, bukan materi maupun sertifikat yang Anda terima.'
                        : 'The same programme can run in three ways. What differs is the place and the form of practice, not the material or the certificate you receive.' }}
                </p>
            </div>

            {{-- Tiap kartu dibuka sebuah foto: ketiga cara ini berbeda pada tempat
                 dan bentuknya, dan satu foto menunjukkan perbedaan itu sebelum
                 kalimat mana pun sempat dibaca.

                 Lencana ikonnya menumpang di tepi bawah foto, bukan berdiri sendiri
                 di atas judul. Di situ ia mengikat foto dengan tulisan di bawahnya
                 jadi satu kartu, bukan dua blok yang kebetulan bertumpuk.

                 Kartu unggulan tetap gelap, dua lainnya terang. Dibedakan supaya
                 tidak menjadi tiga kotak kembar, dan yang ditonjolkan adalah mode
                 yang benar-benar paling banyak dipakai. Semua kartu setinggi sama
                 lewat auto-rows-fr, jadi barisnya rata berapa pun panjang teksnya. --}}
            <div class="mt-12 grid auto-rows-fr gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($modes as $i => $mode)
                    @php
                        $dark = $mode['featured'];
                        $imgRel = $modeImages[$mode['label']] ?? null;
                        $hasImg = $imgRel && is_file(public_path($imgRel));
                    @endphp
                    <div data-aos="fade-up" data-aos-delay="{{ $i * 50 }}"
                         class="group relative flex flex-col overflow-hidden rounded-2xl p-5 transition-transform duration-500 ease-out-soft hover:-translate-y-1 md:p-6 {{ $dark
                             ? 'bg-navy-950 text-white'
                             : 'border border-navy-100 bg-white' }}">

                        @if ($dark)
                            <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
                        @endif

                        <div class="relative flex flex-1 flex-col">
                            <div class="relative">
                                @if ($hasImg)
                                    <div class="aspect-[16/10] overflow-hidden rounded-xl {{ $dark ? 'bg-navy-900 ring-1 ring-white/10' : 'bg-mist ring-1 ring-navy-100' }}">
                                        <img src="{{ asset($imgRel) }}" alt="{{ $id ? 'Pelatihan mode '.$mode['label'] : $mode['label'].' delivery' }}"
                                             width="1200" height="750" loading="lazy" decoding="async"
                                             class="h-full w-full object-cover transition-transform duration-700 ease-out-soft group-hover:scale-105">
                                    </div>
                                @endif

                                {{-- ring-4 sewarna kartunya: itu yang membuat lencana ini
                                     terbaca duduk di atas foto, bukan tenggelam di dalamnya. --}}
                                <span class="{{ $hasImg ? 'absolute -bottom-6 left-4' : '' }} grid h-12 w-12 place-items-center rounded-full ring-4 {{ $dark
                                         ? 'bg-sky-400 text-navy-950 ring-navy-950'
                                         : 'bg-navy-500 text-white ring-white' }}" aria-hidden="true">
                                    @if ($mode['icon'] === 'users')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 19v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 17.5V19"/><circle cx="10" cy="8" r="3.2"/><path d="M20 19v-1.4a3.3 3.3 0 0 0-2.5-3.2M15.5 5.3a3.2 3.2 0 0 1 0 5.9"/></svg>
                                    @elseif ($mode['icon'] === 'screen')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="12" rx="2"/><path d="M9 20h6M12 16.5V20"/></svg>
                                    @else
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 21 8l-9 4.5L3 8l9-4.5Z"/><path d="m3 12.5 9 4.5 9-4.5"/></svg>
                                    @endif
                                </span>
                            </div>

                            <div class="{{ $hasImg ? 'mt-10' : 'mt-6' }} px-1">
                                <h3 class="text-2xl font-bold {{ $dark ? 'text-white' : 'text-navy' }}">{{ $mode['label'] }}</h3>
                                <p class="mt-2.5 text-pretty leading-relaxed {{ $dark ? 'text-navy-100' : 'text-slate-600' }}">{{ $mode['lead'] }}</p>

                                <ul class="mt-5 space-y-3 border-t pt-5 text-sm {{ $dark ? 'border-white/15' : 'border-navy-100' }}">
                                    @foreach ($mode['points'] as $point)
                                        <li class="flex items-start gap-2.5">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $dark ? 'text-sky-300' : 'text-sky-600' }}" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10.5 4 4 8-9"/></svg>
                                            <span class="{{ $dark ? 'text-navy-100' : 'text-slate-700' }}">{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="mt-10 max-w-3xl text-sm leading-relaxed text-slate-600" data-aos="fade-up">
                {{ $id
                    ? 'Cara mengikuti ditentukan per angkatan, bukan per program. Jadwal yang sama bisa dibuka offline bulan ini dan hybrid pada angkatan berikutnya. Label pada tiap kartu jadwal menunjukkan yang berlaku.'
                    : 'The delivery mode is set per intake, not per programme. The same course can run offline this month and hybrid for the next. The label on each schedule card shows which applies.' }}
            </p>
        </div>
    </section>

    {{-- ===================== AGENDA MENDATANG =====================
         Isinya datang dari dua tempat yang berbeda peran:

         Kartunya dibaca dari menu Agenda yang sudah ada, bukan dari daftar
         baru. Satu agenda cukup dimasukkan sekali lalu muncul di mana pun ia
         ditampilkan; daftar terpisah akan menuntut admin mengisi hal yang sama
         dua kali dan menyimpang begitu salah satunya disunting.

         Berkas jadwalnya (PDF atau Excel) diunggah lewat Konten > Agenda di
         Halaman Layanan. Kartunya menampilkan yang terdekat, berkasnya memuat
         seluruh periode — keduanya menjawab pertanyaan yang berbeda, jadi
         keduanya ada.

         Seluruh seksi ini bisa dimatikan dari panel admin. --}}
    @if ($agendaActive && ($agendas->isNotEmpty() || $agendaFile))
        <section class="section-sm relative overflow-hidden border-t border-navy-50 bg-paper">
            <div class="pointer-events-none absolute inset-0 aurora-light opacity-50"></div>

            {{-- data-hscroll dipasang di pembungkus, bukan di relnya: initHscroll
                 mencari tombol panah di dalam akar yang sama, dan panahnya berdiri
                 di baris judul, bukan di dalam trek. --}}
            <div class="container relative" data-hscroll>
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between md:gap-10">
                    <div class="max-w-2xl" data-aos="fade-up">
                        <h2 class="text-display-lg text-navy">{{ $agendaTitle ?: ($id ? 'Agenda Mendatang' : 'Upcoming Agenda') }}</h2>
                        @if ($agendaIntro)
                            <p class="mt-4 text-pretty leading-relaxed text-slate-600">{{ $agendaIntro }}</p>
                        @endif
                    </div>

                    {{-- Panah geser di kanan atas, sejajar garis bawah judul,
                         mengikuti pola rel di seksi lain situs ini. --}}
                    @if ($agendas->count() > 1)
                        <div class="hidden shrink-0 items-center gap-3 md:flex" data-aos="fade-up">
                            <button type="button" data-hscroll-prev aria-label="{{ $id ? 'Sebelumnya' : 'Previous' }}"
                                    class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 bg-white text-navy shadow-card transition-colors hover:border-navy-600 hover:bg-navy-600 hover:text-white active:scale-95">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" data-hscroll-next aria-label="{{ $id ? 'Berikutnya' : 'Next' }}"
                                    class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 bg-white text-navy shadow-card transition-colors hover:border-navy-600 hover:bg-navy-600 hover:text-white active:scale-95">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    @endif
                </div>

                @if ($agendas->isNotEmpty())
                    {{-- Rel geser, bukan grid. Dengan jadwal setahun jumlah kartunya
                         bisa belasan; grid akan memanjang ke bawah dan mendorong
                         seluruh daftar kategori layanan jauh keluar layar. --}}
                    <div data-hscroll-track class="-mx-1 mt-10 flex cursor-grab snap-x snap-proximity select-none items-stretch gap-5 overflow-x-auto scroll-smooth px-1 py-2 active:cursor-grabbing [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($agendas as $i => $agenda)
                            @php
                                $agendaImg = $agenda->image
                                    ? (str_starts_with($agenda->image, 'http') ? $agenda->image : Storage::url($agenda->image))
                                    : null;

                                // Tiga keadaan, bukan dua. "Segera hadir" hanya untuk
                                // satu agenda terdekat yang belum lewat: kalau semua
                                // yang akan datang ditandai begitu, penandanya berhenti
                                // menunjuk apa pun.
                                $isPast = $agenda->starts_at && $agenda->starts_at->isPast();
                                $isSoonest = ! $isPast && $i === 0;

                                if ($isPast)        { $pill = 'bg-slate-600/90 text-white'; $dot = 'bg-white/80';    $pillText = $id ? 'Sudah berakhir' : 'Ended'; }
                                elseif ($isSoonest) { $pill = 'bg-gold text-navy-950';      $dot = 'bg-navy-950/70'; $pillText = $id ? 'Segera hadir' : 'Starting soon'; }
                                else                { $pill = 'bg-navy-600/90 text-white';  $dot = 'bg-white/90';    $pillText = $id ? 'Akan datang' : 'Upcoming'; }
                            @endphp
                            {{-- .card + .card-hover, kelas yang sama dengan kartu layanan
                                 di bawah seksi ini. Sebelumnya kartu agenda menyusun
                                 sendiri sudut, latar, garis tepi, bayangan, dan hovernya,
                                 padahal keduanya berdiri di satu halaman yang sama.

                                 .card-hover sengaja tidak dipasang pada kartu terdekat.
                                 Di dalamnya ada hover:border-navy-200, dan itu akan
                                 menghapus garis emas penandanya justru saat kartunya
                                 disentuh. Kartu itu memang sudah terangkat sejak diam,
                                 jadi tidak ada yang hilang. --}}
                            <article @class([
                                        'card group relative flex min-w-0 shrink-0 basis-[82%] snap-start flex-col overflow-hidden sm:basis-[calc((100%_-_1.25rem)/2)] lg:basis-[calc((100%_-_2.5rem)/3)]',
                                        'border-2 border-gold shadow-lift' => $isSoonest,
                                        'card-hover' => ! $isSoonest,
                                    ]) data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 50 }}">
                                <div class="relative aspect-[3/2] overflow-hidden bg-navy-900">
                                    @if ($agendaImg)
                                        {{-- Yang sudah berakhir tampil meredup tanpa warna.
                                             Pembedaannya harus terbaca sebelum pengunjung
                                             sempat membaca lencananya. --}}
                                        <img src="{{ $agendaImg }}" alt="{{ $agenda->title }}" loading="lazy"
                                             @class([
                                                 'h-full w-full object-cover transition-all duration-700',
                                                 'opacity-45 grayscale' => $isPast,
                                                 'group-hover:scale-105' => ! $isPast,
                                             ])>
                                    @else
                                        <div class="absolute inset-0 aurora {{ $isPast ? 'opacity-20 grayscale' : 'opacity-50' }}"></div>
                                    @endif
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/65 via-transparent to-transparent"></div>

                                    {{-- Lencana tanggal. Tanggal adalah hal pertama yang
                                         dicari orang di kartu agenda, jadi ia duduk di
                                         gambar alih-alih menunggu giliran di badan kartu. --}}
                                    @if ($agenda->starts_at)
                                        <div @class([
                                                'absolute left-4 top-4 flex w-12 flex-col items-center rounded-xl py-1.5 text-center shadow-lift ring-1 backdrop-blur',
                                                'bg-gold ring-gold-soft/70' => $isSoonest,
                                                'bg-white/95 ring-white/60' => ! $isSoonest,
                                            ])>
                                            <span class="font-display text-lg font-semibold leading-none {{ $isSoonest ? 'text-navy-950' : 'text-navy' }}">{{ $agenda->starts_at->format('d') }}</span>
                                            <span class="font-mono text-[9px] font-bold uppercase tracking-normal {{ $isSoonest ? 'text-navy-950/70' : ($isPast ? 'text-slate-600' : 'text-navy-600') }}">{{ $agenda->starts_at->translatedFormat('M') }}</span>
                                        </div>
                                    @endif

                                    <span class="absolute right-4 top-4 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-normal shadow-lift backdrop-blur {{ $pill }}">
                                        <span class="h-1 w-1 rounded-full {{ $dot }}"></span>{{ $pillText }}
                                    </span>
                                </div>

                                <div class="flex flex-1 flex-col p-6">
                                    <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 font-mono text-[11px] uppercase tracking-normal text-slate-600">
                                        @if ($agenda->starts_at)
                                            <span>{{ $agenda->starts_at->translatedFormat('H.i') }} WIB</span>
                                        @endif
                                        <x-meta-location :value="$agenda->location" />
                                    </div>

                                    {{-- Setakaran dengan judul kartu layanan: text-xl, dan
                                         warnanya tidak berubah saat disentuh. Perubahan warna
                                         judul dulu menjadi satu-satunya penanda bahwa kartu
                                         ini bisa diklik; sejak panah bundar di kakinya ikut
                                         berisi dan berputar, penanda itu sudah ada dan lebih
                                         jelas. Dua penanda untuk satu hal membuat kartu
                                         agenda bergerak lebih ramai daripada tetangganya.

                                         Ringkasan dijatah dua baris, bukan tiga, supaya
                                         tinggi kartunya sejajar dengan kartu layanan. --}}
                                    <div class="flex-1">
                                        <h3 class="mt-2.5 line-clamp-2 text-xl leading-snug text-navy">{{ $agenda->title }}</h3>

                                        @if ($agenda->excerpt)
                                            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $agenda->excerpt }}</p>
                                        @endif
                                    </div>

                                    {{-- Agenda yang sudah berakhir tidak membawa ajakan
                                         mendaftar: menanyakan jadwal yang sudah lewat
                                         tidak ada gunanya, dan tombolnya hanya akan
                                         menyesatkan.

                                         Yang masih berlaku menautkan ke WhatsApp, bukan ke
                                         halaman Agenda: halaman itu sedang disembunyikan
                                         lewat config/company.php, dan menautkan ke halaman
                                         yang menjawab 404 lebih buruk daripada tidak
                                         menautkan sama sekali. --}}
                                    {{-- Kakinya mengikuti kartu layanan: garis rambut, pt-5,
                                         teks di kiri dan panah bundar di kanan. Panah kecil
                                         emas yang bergeser ke samping dilepas — bentuk panah
                                         ketiga di satu halaman yang sama, sesudah panah
                                         bundar kartu layanan dan panah rel di atasnya. --}}
                                    @if ($isPast)
                                        <p class="mt-6 border-t border-navy-100 pt-5 font-mono text-[11px] uppercase tracking-normal text-slate-600">
                                            {{ $id ? 'Sudah terlaksana' : 'Already held' }}
                                        </p>
                                    @else
                                        <a href="https://wa.me/62818834766?text={{ rawurlencode(($id ? 'Halo Delta Tiga Enam, saya ingin bertanya soal agenda: ' : 'Hello Delta Tiga Enam, I would like to ask about this agenda: ').$agenda->title) }}"
                                           target="_blank" rel="noopener"
                                           class="mt-6 flex items-center justify-between gap-4 border-t border-navy-100 pt-5 text-sm font-medium text-navy transition-colors duration-300 hover:text-navy-600">
                                            {{ $id ? 'Tanyakan jadwal ini' : 'Ask about this date' }}
                                            <span class="card-arrow grid h-10 w-10 shrink-0 place-items-center rounded-full border border-navy-200 text-navy group-hover:border-navy-600 group-hover:bg-navy-600 group-hover:text-white" aria-hidden="true">
                                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                {{-- Berkas jadwal setahun. Bentuknya sengaja sama dengan kartu unduh
                     Profil Perusahaan di halaman Tentang: keduanya satu dokumen resmi
                     yang bisa dilihat atau diunduh, jadi pengunjung yang sudah
                     mengenali bentuk itu di satu halaman langsung mengenalinya di
                     sini.

                     Satu panel melebar, bukan kartu tambahan di dalam rel: isinya
                     bukan satu agenda melainkan seluruhnya, jadi ia tidak boleh
                     ikut tergeser bersama kartu-kartu agendanya. --}}
                @if ($agendaFile)
                    <div class="card mt-8 flex flex-col gap-7 p-7 md:flex-row md:items-center md:justify-between md:p-9" data-aos="fade-up">
                        <div class="group flex items-start gap-6">
                            <div class="relative shrink-0">
                                <div class="grid h-24 w-[4.5rem] place-items-center rounded-xl bg-gradient-to-br from-navy-800 to-navy-950 shadow-lift ring-1 ring-white/10 transition-transform duration-300 ease-out-soft group-hover:-translate-y-1 group-hover:-rotate-3">
                                    <svg class="h-9 w-9 text-white/90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 13h6M9 17h4"/></svg>
                                </div>
                                {{-- Jenis berkasnya dibaca dari berkasnya sendiri, jadi
                                     lencana ini ikut berubah saat PDF diganti Excel. --}}
                                <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 rounded-md bg-gold px-2.5 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider text-navy-950 shadow-gold">{{ $agendaFile['ext'] }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="kicker mb-2">{{ $id ? 'Jadwal Setahun' : 'Full-Year Schedule' }}</p>
                                <p class="font-display text-2xl font-bold leading-tight text-navy md:text-3xl">{{ $agendaFile['label'] ?: ($id ? 'Jadwal lengkap' : 'Full schedule') }}</p>
                                @if ($agendaFile['note'])
                                    <p class="mt-2.5 max-w-md text-pretty text-sm leading-relaxed text-slate-600">{{ $agendaFile['note'] }}</p>
                                @endif
                                {{-- Jenis dan ukuran disebut sebelum tombolnya. Tautan
                                     unduhan yang tidak menyebutkan keduanya membuat orang
                                     menekan tanpa tahu apa yang akan terbuka. --}}
                                <p class="mt-3 inline-flex items-center gap-1.5 font-mono text-[10px] uppercase tracking-wider text-slate-600">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                    {{ $agendaFile['ext'] }} · {{ $agendaFile['size'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex w-full shrink-0 flex-col gap-3 sm:flex-row md:w-auto">
                            <a href="{{ $agendaFile['url'] }}" target="_blank" rel="noopener" class="btn-ghost justify-center">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                {{ $id ? 'Lihat' : 'View' }}
                            </a>
                            <a href="{{ $agendaFile['url'] }}" download class="btn-blue justify-center">
                                {{ $id ? 'Unduh' : 'Download' }}
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v11m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @forelse ($categories as $cat)
        {{-- bg-paper, bukan bg-mist. Keduanya off-white kebiruan yang nyaris sama
             (#f2f8fd dan #eaf3fb), dan seluruh situs memakai bg-paper untuk irama
             antar-seksi; dua nada yang beda tipis hanya terbaca sebagai ketidak-
             rapian, bukan sebagai irama. --}}
        <section id="{{ $cat->slug }}" class="scroll-mt-28 section-sm {{ $loop->odd ? '' : 'bg-paper' }}">
            <div class="container">
                {{-- Penomoran 01/02/03 di atas judul dilepas. Kategori layanan bukan
                     urutan langkah: nomornya tidak memberi tahu apa pun yang tidak
                     sudah dikatakan judulnya, dan perancah bernomor di atas tiap
                     seksi adalah pola yang justru dihindari PRODUCT.md.

                     Yang menggantikannya di sisi kanan adalah jumlah layanan di
                     kategori itu — keterangan yang benar-benar membawa kabar, dan
                     bentuk yang sama dengan kepala halaman Blog serta Portofolio. --}}
                <div class="flex flex-col gap-4 border-b border-navy-100 pb-6 md:flex-row md:items-end md:justify-between md:gap-10">
                    <div class="max-w-2xl">
                        <h2 class="text-display-lg text-navy">{{ $cat->name }}</h2>
                        @if ($cat->short_description)<p class="mt-3 text-pretty text-slate-600">{{ $cat->short_description }}</p>@endif
                    </div>
                    @if ($cat->services->isNotEmpty())
                        <p class="shrink-0 font-mono text-xs text-slate-600">
                            <span class="text-sm font-semibold text-navy">{{ str_pad($cat->services->count(), 2, '0', STR_PAD_LEFT) }}</span>
                            {{ $id ? 'layanan' : 'services' }}
                        </p>
                    @endif
                </div>

                @if ($cat->services->isNotEmpty())
                    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-stagger>
                        @foreach ($cat->services as $service)
                            <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex flex-col overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 50 }}">
                                <div class="relative aspect-[3/2] overflow-hidden bg-navy-100">
                                    @if ($service->image)
                                        <img src="{{ Storage::url($service->image) }}" alt="{{ $service->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-navy-200 to-navy-100"></div>
                                    @endif
                                    @if ($service->is_featured)
                                        <span class="absolute left-4 top-4 rounded-full bg-gold px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-ink">{{ __('site.common.featured') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-6">
                                    {{-- Judul dan deskripsi dibungkus satu blok yang tumbuh (flex-1),
                                         sehingga sisa ruang kartu diserap di sini dan baris harga
                                         selalu mendarat di dasar kartu. Tanpa ini, harga menempel
                                         tepat di bawah deskripsi, jadi kartu berjudul dua baris
                                         menampilkan harganya lebih rendah daripada tetangganya.

                                         Judul dijatah dua baris agar bagian atas kartu juga rata,
                                         bukan hanya harganya. --}}
                                    {{-- min-h-14 pada judul dilepas. Ia menjatah dua baris
                                         supaya deskripsi tiap kartu mulai di ketinggian
                                         yang sama, tapi judul satu baris jadi menyisakan
                                         28px kosong yang terbaca sebagai cacat, bukan
                                         sebagai perataan. Perataan harganya tetap aman:
                                         yang mengerjakannya flex-1 di pembungkus ini,
                                         bukan jatah baris judulnya. --}}
                                    <div class="flex-1">
                                        <h3 class="line-clamp-2 text-xl leading-snug text-navy">{{ $service->title }}</h3>
                                        @if ($service->short_description)<p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $service->short_description }}</p>@endif
                                    </div>
                                    <div class="mt-6 flex items-end justify-between border-t border-navy-100 pt-5">
                                        <div>
                                            @if ($service->price > 0)
                                                @if ($service->hasDiscount())
                                                    <p class="flex flex-wrap items-center gap-1.5">
                                                        <span class="font-mono text-[11px] text-slate-500 line-through">Rp {{ number_format((float) $service->discount_original_price, 0, ',', '.') }}</span>
                                                        {{-- Emas berteks tinta, bukan merah muda
                                                             berteks merah. Merah itu satu-satunya
                                                             di situs ini, dan lencana diskon di
                                                             kartu Pelatihan sudah memakai emas. --}}
                                                        <span class="rounded bg-gold px-1.5 py-0.5 font-mono text-[10px] font-bold text-ink">-{{ $service->discountPercent() }}%</span>
                                                    </p>
                                                    <p class="text-lg text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ __('site.common.from') }}</p>
                                                    <p class="text-lg text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @endif
                                            @else
                                                {{-- Diberi baris label juga, supaya ketiga bentuk harga
                                                     sama-sama dua baris. Kalau yang satu ini satu baris,
                                                     kartunya jadi lebih pendek dan harga tetangganya
                                                     tidak sejajar meski blok ini sudah didorong ke dasar. --}}
                                                <p class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Investasi' : 'Investment' }}</p>
                                                <p class="text-lg text-navy">{{ $id ? 'Hubungi kami' : 'Contact us' }}</p>
                                            @endif
                                        </div>
                                        {{-- Sentuhnya ke navy, bukan emas. Panah bundar di
                                             seluruh situs (rel Pelatihan, Agenda, Berita)
                                             semuanya berisi navy saat disentuh, dan emas di
                                             sini membuat kartu layanan satu-satunya yang
                                             berbeda.

                                             .card-arrow menambahkan putaran -45° saat kartunya
                                             disentuh, sehingga panahnya menunjuk ke atas-kanan
                                             seperti di indeks Portofolio. Putarannya ada di CSS,
                                             bukan lewat -rotate-45: utilitas itu menulis
                                             --tw-rotate, dan custom property tidak diinterpolasi
                                             peramban sehingga panahnya akan mematah. --}}
                                        <span class="card-arrow grid h-10 w-10 shrink-0 place-items-center rounded-full border border-navy-200 text-navy group-hover:border-navy-600 group-hover:bg-navy-600 group-hover:text-white">
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="mt-10 rounded-3xl border border-dashed border-navy-200 bg-white p-12 text-center">
                        <p class="text-lg text-navy">{{ $id ? 'Layanan akan segera hadir' : 'Services coming soon' }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Kami sedang menyiapkan layanan untuk kategori ini.' : 'We are preparing services for this category.' }}</p>
                    </div>
                @endif
            </div>
        </section>
    @empty
        <section class="section">
            <div class="container">
                <div class="rounded-3xl border border-dashed border-navy-200 bg-mist p-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Belum ada layanan' : 'No services yet' }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Daftar layanan akan ditampilkan di sini setelah tersedia.' : 'Our service catalog will appear here once available.' }}</p>
                </div>
            </div>
        </section>
    @endforelse

    {{-- ===================== PENUTUP: KONSULTASI =====================
         Penutup halaman Layanan. Setelah menggulir seluruh kategori, pembaca
         berada di titik keputusan tapi tidak punya jalan ke mana pun kecuali
         footer — seksi ini memberi jalan itu.

         Komposisi di tengah di atas foto, bukan dua kolom di atas pita polos.
         Fotonya kelas pelatihan yang baru selesai: wajah orang yang sudah
         melewati prosesnya. Itu yang sedang ditimbang pembaca di titik ini,
         dan satu foto menjawabnya lebih cepat daripada satu paragraf.

         Lapisan birunya pekat (80%) dan bukan hitam. Pekat karena foto ini
         punya jendela yang nyaris putih: pada 80% pun titik paling terang di
         baliknya masih memberi kontras 7,5:1 untuk teks putih, jadi tidak ada
         sudut layar yang kalimatnya hilang. Biru karena hitam akan memutus
         halaman ini dari warna seluruh situs.

         Satu pertanyaan dan satu tombol, tidak ada yang lain. Pembaca sudah
         melewati seluruh katalog sebelum sampai ke sini; yang dibutuhkannya
         tinggal satu jalan keluar, bukan paragraf meyakinkan atau pilihan
         kedua yang menunda keputusan. --}}
    <section class="relative isolate overflow-hidden bg-navy-950 text-white">
        <img src="{{ asset('images/konsultasi-layanan.webp') }}" alt="" aria-hidden="true"
             width="1600" height="900" loading="lazy" decoding="async"
             class="absolute inset-0 -z-10 h-full w-full object-cover object-center">

        {{-- Lapis biru transparan: satu bidang rata untuk keterbacaan, satu
             gradien tipis yang menggelapkan tepi atas dan bawah supaya seksi
             ini menyatu dengan footer navy di bawahnya. --}}
        <div class="pointer-events-none absolute inset-0 -z-10 bg-navy-900/80"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-navy-950/70 via-navy-900/20 to-navy-950/80"></div>
        <div class="pointer-events-none absolute inset-0 -z-10 grain opacity-20"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>

        <div class="container relative py-20 text-center md:py-28 lg:py-32">
            <div class="mx-auto max-w-2xl" data-aos="fade-up">
                <h2 class="text-display-xl text-white">
                    {{ $id ? 'Belum yakin program mana yang pas?' : 'Not sure which programme fits?' }}
                </h2>

                {{-- Satu kalimat, bukan paragraf. Isinya apa yang perlu disiapkan
                     pembaca sebelum menekan tombol, supaya langkahnya terasa kecil
                     dan jelas, bukan janji umum yang menambah panjang bacaan. --}}
                <p class="mx-auto mt-5 max-w-xl text-pretty text-lg leading-relaxed text-navy-100">
                    {{ $id
                        ? 'Ceritakan pekerjaan dan jumlah tim Anda, lalu kami bantu pilihkan skema beserta jadwalnya. Tanpa biaya.'
                        : 'Tell us about the work and the size of your team, and we will help pick a scheme and a schedule. No charge.' }}
                </p>

                <div class="mt-9 flex justify-center">
                    <a href="{{ route('contact.index') }}" class="btn-blue">
                        {{ $id ? 'Konsultasikan Kebutuhan Anda' : 'Discuss Your Needs' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layout>
