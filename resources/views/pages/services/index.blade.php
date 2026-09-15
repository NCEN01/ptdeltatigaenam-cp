@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.services')">
    <x-page-header
        :eyebrow="__('site.home.services_kicker')"
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
        /* Offline diletakkan lebih dulu dan ditandai unggulan karena memang
           mode yang paling banyak dipakai pada data: tujuh dari sepuluh layanan.
           Menonjolkan online justru akan menjanjikan sesuatu yang belum ada
           programnya. */
        $modes = $id ? [
            [
                'label' => 'Offline', 'icon' => 'users', 'featured' => true,
                'lead' => 'Seluruh sesi berlangsung tatap muka, di lokasi kerja Anda atau di ruang pelatihan kami.',
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
                'lead' => 'Seluruh sesi berlangsung daring, diikuti dari mana saja dengan koneksi yang stabil.',
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
                'lead' => 'Teori diselesaikan daring, lalu praktik dan uji kompetensi digelar di lokasi Anda.',
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
                'lead' => 'Every session runs in person, at your workplace or in our training rooms.',
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
                'lead' => 'Every session runs remotely, joined from anywhere with a stable connection.',
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
                'lead' => 'Theory is completed online, then practice and assessment run at your site.',
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
                <h2 class="text-3xl leading-tight text-navy md:text-4xl" data-aos="fade-up">
                    {{ $id ? 'Tiga cara mengikuti program' : 'Three ways to take a programme' }}
                </h2>
                <p class="mt-5 text-pretty leading-relaxed text-slate-600" data-aos="fade-up">
                    {{ $id
                        ? 'Program yang sama dapat dijalankan dengan tiga cara. Yang berbeda hanya tempat dan bentuk praktiknya, bukan materi maupun sertifikat yang Anda terima.'
                        : 'The same programme can run in three ways. What differs is the place and the form of practice, not the material or the certificate you receive.' }}
                </p>
            </div>

            {{-- Kartu unggulan dibuat gelap, dua lainnya terang. Dibedakan supaya
                 tidak menjadi tiga kotak kembar, dan yang ditonjolkan adalah mode
                 yang benar-benar paling banyak dipakai. Semua kartu setinggi sama
                 lewat auto-rows-fr, jadi barisnya rata berapa pun panjang teksnya. --}}
            <div class="mt-12 grid auto-rows-fr gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($modes as $i => $mode)
                    @php $dark = $mode['featured']; @endphp
                    <div data-aos="fade-up" data-aos-delay="{{ $i * 90 }}"
                         class="relative flex flex-col overflow-hidden rounded-2xl p-7 transition-transform duration-500 ease-out-soft hover:-translate-y-1 md:p-8 {{ $dark
                             ? 'bg-navy-950 text-white'
                             : 'border border-navy-100 bg-white' }}">

                        @if ($dark)
                            <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
                        @endif

                        <div class="relative flex flex-1 flex-col">
                            <span class="grid h-12 w-12 place-items-center rounded-xl {{ $dark ? 'bg-white/10 text-white' : 'bg-sky-50 text-sky-700' }}" aria-hidden="true">
                                @if ($mode['icon'] === 'users')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 19v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 17.5V19"/><circle cx="10" cy="8" r="3.2"/><path d="M20 19v-1.4a3.3 3.3 0 0 0-2.5-3.2M15.5 5.3a3.2 3.2 0 0 1 0 5.9"/></svg>
                                @elseif ($mode['icon'] === 'screen')
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="12" rx="2"/><path d="M9 20h6M12 16.5V20"/></svg>
                                @else
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.5 21 8l-9 4.5L3 8l9-4.5Z"/><path d="m3 12.5 9 4.5 9-4.5"/></svg>
                                @endif
                            </span>

                            <h3 class="mt-6 font-display text-2xl font-semibold {{ $dark ? 'text-white' : 'text-navy' }}">{{ $mode['label'] }}</h3>
                            <p class="mt-2.5 leading-relaxed {{ $dark ? 'text-navy-100' : 'text-slate-600' }}">{{ $mode['lead'] }}</p>

                            <ul class="mt-6 space-y-3 border-t pt-6 text-sm {{ $dark ? 'border-white/15' : 'border-navy-100' }}">
                                @foreach ($mode['points'] as $point)
                                    <li class="flex items-start gap-2.5">
                                        <svg class="mt-0.5 h-4 w-4 shrink-0 {{ $dark ? 'text-sky-300' : 'text-sky-600' }}" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m4 10.5 4 4 8-9"/></svg>
                                        <span class="{{ $dark ? 'text-navy-100' : 'text-slate-700' }}">{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
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

    @forelse ($categories as $cat)
        <section id="{{ $cat->slug }}" class="scroll-mt-28 py-14 md:py-20 {{ $loop->odd ? '' : 'bg-mist' }}">
            <div class="container">
                <div class="flex flex-col gap-4 border-b border-navy-100 pb-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="mb-3 font-mono text-[11px] uppercase tracking-normal text-gold-deep">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h2 class="text-3xl font-semibold text-navy md:text-4xl">{{ $cat->name }}</h2>
                        @if ($cat->short_description)<p class="mt-3 text-pretty text-slate-600">{{ $cat->short_description }}</p>@endif
                    </div>
                </div>

                @if ($cat->services->isNotEmpty())
                    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-stagger>
                        @foreach ($cat->services as $service)
                            <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex flex-col overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
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
                                    <div class="flex-1">
                                        <h3 class="line-clamp-2 min-h-14 font-display text-xl font-semibold leading-snug text-navy">{{ $service->title }}</h3>
                                        @if ($service->short_description)<p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $service->short_description }}</p>@endif
                                    </div>
                                    <div class="mt-6 flex items-end justify-between border-t border-navy-100 pt-5">
                                        <div>
                                            @if ($service->price > 0)
                                                @if ($service->hasDiscount())
                                                    <p class="flex flex-wrap items-center gap-1.5">
                                                        <span class="font-mono text-[11px] text-slate-400 line-through">Rp {{ number_format((float) $service->discount_original_price, 0, ',', '.') }}</span>
                                                        <span class="rounded bg-rose-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-rose-600">-{{ $service->discountPercent() }}%</span>
                                                    </p>
                                                    <p class="font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ __('site.common.from') }}</p>
                                                    <p class="font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @endif
                                            @else
                                                {{-- Diberi baris label juga, supaya ketiga bentuk harga
                                                     sama-sama dua baris. Kalau yang satu ini satu baris,
                                                     kartunya jadi lebih pendek dan harga tetangganya
                                                     tidak sejajar meski blok ini sudah didorong ke dasar. --}}
                                                <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ $id ? 'Investasi' : 'Investment' }}</p>
                                                <p class="font-display text-lg font-semibold text-navy">{{ $id ? 'Hubungi kami' : 'Contact us' }}</p>
                                            @endif
                                        </div>
                                        <span class="grid h-10 w-10 place-items-center rounded-full border border-navy-200 transition-all group-hover:border-gold group-hover:bg-gold group-hover:text-ink">
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="mt-10 rounded-3xl border border-dashed border-navy-200 bg-white p-12 text-center">
                        <p class="font-display text-lg font-semibold text-navy">{{ $id ? 'Layanan akan segera hadir' : 'Services coming soon' }}</p>
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

</x-layout>
