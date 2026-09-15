@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.services')">
    <x-page-header
        :eyebrow="__('site.home.services_kicker')"
        :title="__('site.home.services_title')"
        :subtitle="$id ? 'Dari konsultasi manajemen hingga sertifikasi kompetensi, dirancang untuk hasil yang terukur.' : 'From management consulting to competency certification, designed for measurable outcomes.'"
        placement="services"
        image="photo-1524178232363-1fb2b075b655">
        {{-- Category jump-nav. Highlights the section you are currently reading, so on a page
             of seven stacked sections the chips act as a position indicator, not just links.
             Mobile is a scroll-snap strip rather than the old auto-running marquee: chips that
             drift sideways are hard to hit, and `hover:pause` does not fire on touch. --}}
        <nav class="mt-8 w-full sm:mt-10" aria-label="{{ $id ? 'Kategori layanan' : 'Service categories' }}"
             x-data="sectionNav(@js($categories->pluck('slug')->all()))">
            <ul class="mask-fade-x -mx-5 flex snap-x snap-mandatory gap-2 overflow-x-auto px-5 pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:mx-0 sm:flex-wrap sm:[mask-image:none] sm:justify-center sm:overflow-visible sm:px-0">
                @foreach ($categories as $cat)
                    <li class="shrink-0 snap-start sm:shrink" x-data="{ slug: @js($cat->slug) }">
                        <a href="#{{ $cat->slug }}"
                           class="block whitespace-nowrap rounded-full border px-4 py-2 text-sm transition-all duration-300 ease-out-soft"
                           :class="active === slug
                               ? 'border-white bg-white text-navy font-medium'
                               : 'border-white/15 text-navy-100 hover:border-white/50 hover:text-white'"
                           :aria-current="active === slug ? 'true' : null">{{ $cat->name }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </x-page-header>

    {{-- ===================== CARA MENGIKUTI =====================
         Kolom mode (online / offline / hybrid) sudah ada di data dan tampil pada
         kartu jadwal di halaman detail, tetapi tidak pernah dijelaskan artinya.
         Seksi ini menjelaskannya sebelum pengunjung menemui lencana itu.

         Tiga kolom bersebelahan memang bentuk yang tepat untuk perbandingan, jadi
         bukan grid kartu seragam. Tanpa kotak dan tanpa isian warna: pemisahnya
         satu garis rambut tegak, mengikuti bahasa visual halaman lain. --}}
    @php
        $modes = $id ? [
            [
                'label' => 'Online',
                'lead' => 'Fleksibel, diikuti dari mana saja.',
                'points' => [
                    ['Tempat', 'Dari kantor, rumah, atau lokasi kerja Anda. Cukup koneksi yang stabil.'],
                    ['Bentuk praktik', 'Studi kasus dan simulasi terpandu, dikerjakan bersama pengajar secara langsung.'],
                    ['Uji kompetensi', 'Dijadwalkan daring bersama asesor, dengan penyerahan bukti kerja secara digital.'],
                    ['Paling cocok', 'Tim yang tersebar di banyak lokasi, atau materi yang tidak menuntut peragaan alat.'],
                ],
            ],
            [
                'label' => 'Tatap Muka',
                'lead' => 'Praktik langsung, digelar di tempat Anda.',
                'points' => [
                    ['Tempat', 'Di kantor atau pabrik Anda, atau di ruang pelatihan kami.'],
                    ['Bentuk praktik', 'Langsung pada alat dan proses yang benar-benar dipakai peserta sehari-hari.'],
                    ['Uji kompetensi', 'Digelar di tempat kerja dan dapat mengikuti pola sif, tanpa menghentikan produksi.'],
                    ['Paling cocok', 'Keterampilan yang harus diperagakan, dan tim yang berada di satu lokasi.'],
                ],
            ],
            [
                'label' => 'Gabungan',
                'lead' => 'Teori daring, praktik di tempat kerja.',
                'points' => [
                    ['Tempat', 'Sesi teori diikuti dari mana saja, sesi praktik digelar di lokasi Anda.'],
                    ['Bentuk praktik', 'Materi dituntaskan lebih dulu secara daring, sehingga hari tatap muka dipakai penuh untuk praktik.'],
                    ['Uji kompetensi', 'Digelar di tempat kerja pada hari yang sama dengan sesi praktiknya.'],
                    ['Paling cocok', 'Program panjang yang sayang bila seluruh harinya menuntut peserta meninggalkan pekerjaan.'],
                ],
            ],
        ] : [
            [
                'label' => 'Online',
                'lead' => 'Flexible, joined from anywhere.',
                'points' => [
                    ['Where', 'From your office, your home, or your work site. A stable connection is enough.'],
                    ['Practice', 'Guided case studies and simulations, worked through live with the instructor.'],
                    ['Assessment', 'Scheduled online with an assessor, with work evidence submitted digitally.'],
                    ['Best for', 'Teams spread across sites, or material that does not require handling equipment.'],
                ],
            ],
            [
                'label' => 'In Person',
                'lead' => 'Hands-on practice, run at your site.',
                'points' => [
                    ['Where', 'At your office or plant, or in our training rooms.'],
                    ['Practice', 'Directly on the equipment and processes participants actually use every day.'],
                    ['Assessment', 'Run at the workplace and able to follow shift patterns, without stopping production.'],
                    ['Best for', 'Skills that must be demonstrated, and teams based in one location.'],
                ],
            ],
            [
                'label' => 'Blended',
                'lead' => 'Theory online, practice on site.',
                'points' => [
                    ['Where', 'Theory sessions are joined from anywhere; practice sessions run at your site.'],
                    ['Practice', 'Material is covered online first, so the in-person days are spent entirely on practice.'],
                    ['Assessment', 'Held at the workplace on the same day as the practical session.'],
                    ['Best for', 'Longer programmes where taking people off the job every day would be costly.'],
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

            <div class="mt-12 grid gap-10 lg:grid-cols-3 lg:gap-12">
                @foreach ($modes as $i => $mode)
                    {{-- Pemisah berlaku untuk tiap kolom setelah yang pertama: garis
                         mendatar saat menumpuk di layar sempit, tegak saat berjajar. --}}
                    <div data-aos="fade-up" data-aos-delay="{{ $i * 90 }}"
                         class="{{ $i > 0 ? 'border-t border-navy-200 pt-10 lg:border-l lg:border-navy-200 lg:border-t-0 lg:pl-12 lg:pt-0' : '' }}">
                        <h3 class="font-display text-2xl font-semibold text-navy">{{ $mode['label'] }}</h3>
                        <p class="mt-2 leading-relaxed text-slate-600">{{ $mode['lead'] }}</p>

                        <dl class="mt-7 divide-y divide-navy-200 border-t border-navy-200">
                            @foreach ($mode['points'] as [$term, $desc])
                                <div class="py-4">
                                    <dt class="font-mono text-[11px] uppercase tracking-wider text-slate-500">{{ $term }}</dt>
                                    <dd class="mt-1.5 text-sm leading-relaxed text-slate-700">{{ $desc }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>

            <p class="mt-10 max-w-3xl text-sm leading-relaxed text-slate-600" data-aos="fade-up">
                {{ $id
                    ? 'Cara mengikuti ditentukan per angkatan, bukan per program. Jadwal yang sama bisa dibuka tatap muka bulan ini dan gabungan pada angkatan berikutnya. Label pada tiap kartu jadwal menunjukkan yang berlaku.'
                    : 'The delivery mode is set per intake, not per programme. The same course can run in person this month and blended for the next. The label on each schedule card shows which applies.' }}
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
