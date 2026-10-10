@php
    $id = app()->getLocale() === 'id';

    $tierStyle = [
        'blue' => 'from-sky-500 to-navy-800',
        'silver' => 'from-slate-400 to-slate-600',
        'gold' => 'from-gold to-gold-deep',
        'platinum' => 'from-navy-700 to-navy-950',
    ];

    $tierMeta = $id ? [
        'blue' => ['tagline' => 'Langkah awal kemitraan pelatihan SDM.', 'perks' => ['Akses pelatihan publik terpilih', 'Diskon untuk pelatihan & sertifikasi', 'Sertifikat kompetensi resmi', 'Materi & laporan pelatihan']],
        'silver' => ['tagline' => 'Pelatihan lebih fleksibel & hemat.', 'perks' => ['Seluruh manfaat Blue', 'Pelatihan kustom sesuai kebutuhan', 'Diskon lebih besar pelatihan & sertifikasi', 'Fleksibilitas jadwal pelatihan']],
        'gold' => ['tagline' => 'Pilihan populer dengan layanan prioritas.', 'perks' => ['Seluruh manfaat Silver', 'Prioritas jadwal pelatihan', 'Sesi konsultasi tambahan', 'Pelatihan berbasis studi kasus', 'Diskon premium']],
        'platinum' => ['tagline' => 'Kemitraan strategis paling lengkap.', 'perks' => ['Seluruh manfaat Gold', 'Prioritas tertinggi & dukungan khusus', 'Konsultasi SDM menyeluruh', 'Program pengembangan jangka panjang', 'Penawaran harga terbaik']],
    ] : [
        'blue' => ['tagline' => 'A first step into HR training partnership.', 'perks' => ['Access to selected public training', 'Discounts on training & certification', 'Official competency certificate', 'Training materials & reports']],
        'silver' => ['tagline' => 'More flexible and cost-efficient training.', 'perks' => ['All Blue benefits', 'Custom training to your needs', 'Larger discounts on training & certification', 'Flexible training schedule']],
        'gold' => ['tagline' => 'Popular choice with priority service.', 'perks' => ['All Silver benefits', 'Priority training schedule', 'Additional consulting sessions', 'Case-study based training', 'Premium discounts']],
        'platinum' => ['tagline' => 'The most complete strategic partnership.', 'perks' => ['All Gold benefits', 'Highest priority & dedicated support', 'Comprehensive HR consulting', 'Long-term development program', 'Best pricing offer']],
    ];

    $manfaat = $id ? [
        ['Meningkatkan Kompetensi dan Produktivitas SDM', 'Memberikan pengetahuan dan keterampilan baru yang relevan, membantu perusahaan mengoptimalkan proses kerja dan meningkatkan kinerja sumber daya manusia.'],
        ['Solusi Pelatihan Kustomisasi', 'Perusahaan dapat menyesuaikan judul pelatihan, seperti pelatihan berbasis studi kasus, dengan fleksibilitas jadwal pelatihan.'],
        ['Penghematan Biaya', 'Dengan potongan harga untuk pelatihan publik dan sertifikasi, perusahaan dapat menghemat anggaran dan meningkatkan efisiensi.'],
        ['Sertifikasi dan Pengakuan Kompetensi', 'Peserta pelatihan mendapatkan sertifikat yang diakui secara profesional, meningkatkan kredibilitas dan kompetensi di industri.'],
        ['Prioritas Layanan untuk Mitra Premium', 'Mitra Gold dan Platinum mendapatkan prioritas jadwal pelatihan dan layanan konsultasi tambahan.'],
        ['Peningkatan Kinerja Perusahaan', 'Dengan SDM yang lebih terlatih dan kompeten, perusahaan dapat mencapai target lebih efisien dan efektif, menjadikan SDM sebagai aset strategis menghadapi persaingan.'],
    ] : [
        ['Improve HR Competency & Productivity', 'Delivers relevant new knowledge and skills, helping the company optimize work processes and improve human capital performance.'],
        ['Customizable Training Solutions', 'Companies can tailor training topics, such as case-study based training, with flexible scheduling.'],
        ['Cost Savings', 'With discounts on public training and certification, companies save budget and improve efficiency.'],
        ['Certification & Competency Recognition', 'Participants earn professionally recognized certificates, boosting credibility and competency in the industry.'],
        ['Priority Service for Premium Partners', 'Gold and Platinum partners receive priority training schedules and additional consulting services.'],
        ['Improved Company Performance', 'With a better-trained, more competent workforce, the company reaches its targets more efficiently, making people a strategic asset.'],
    ];

    /* Dipendekkan dari lima kalimat surat penawaran jadi dua. Yang dibuang kalimat
       yang menyebut maksud baik ("komitmen kami", "kami percaya") tanpa menyebut
       satu pun hal yang benar-benar terjadi; yang disimpan hanya yang bisa
       diperiksa pembaca di halaman ini juga. */
    $narrative = $id
        ? "PT Delta Tiga Enam menyusun program pelatihan dan sertifikasi untuk perusahaan yang ingin menaikkan kompetensi timnya tanpa menghentikan operasional.\n\nLewat kemitraan, kebutuhan pelatihan satu tahun diurus dalam satu kesepakatan: materi disusun mengikuti pekerjaan tim Anda, jadwalnya menyesuaikan, dan biayanya memakai harga mitra."
        : "PT Delta Tiga Enam builds training and certification programmes for companies that want to raise their team's competency without stopping operations.\n\nA partnership settles a year of training needs in one agreement: material shaped around the work your team actually does, scheduling that fits around it, and partner pricing.";

    /* Keterangan mesin pencari dipisah dari naskah yang tampil. Naskahnya ditulis
       untuk dibaca di halaman; keterangan ini dipotong pada sekitar 160 huruf di
       hasil pencarian, jadi keduanya memang pekerjaan yang berbeda. */
    $metaDesc = $id
        ? 'Program Kemitraan Pelatihan SDM PT Delta Tiga Enam: pelatihan dan sertifikasi berskema BNSP yang disusun mengikuti kebutuhan perusahaan Anda, dengan harga mitra.'
        : 'PT Delta Tiga Enam HR Training Partnership: BNSP-scheme training and certification shaped around your company needs, at partner pricing.';

    $steps = $id
        ? ['Isi & kirim formulir pendaftaran', 'Tim kami menghubungi & menjadwalkan presentasi', 'Terima penawaran resmi melalui invoice']
        : ['Fill in & submit the registration form', 'Our team contacts you & schedules a presentation', 'Receive a formal offer via invoice'];
@endphp

<x-layout :title="__('site.nav.partnership')" :description="$metaDesc">
    <x-page-header
        :title="$id ? 'Program Kemitraan Corporate Training' : 'Corporate Training Partnership Program'"
        :subtitle="$intro ?: ($id ? 'Solusi pelatihan SDM yang efektif, relevan, dan dapat disesuaikan dengan kebutuhan perusahaan Anda.' : 'Effective, relevant HR training solutions tailored to your company needs.')"
        placement="partnership"
        image="photo-1600880292089-90a7e086ee0c" />

    {{-- ===================== NARRATIVE ===================== --}}
    <section class="section bg-white">
        <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7" data-aos="fade-up">
                {{-- Token text-display-lg dengan aksen miring berwarna. font-display
                     dan text-balance tidak ditulis: app.css sudah memberikannya ke
                     setiap h1–h4. --}}
                <h2 class="text-display-lg text-navy">{{ $id ? 'Kemitraan PT Delta Tiga Enam' : 'Partnership with PT Delta Tiga Enam' }}</h2>

                {{-- Kalimat pembuka naik satu ukuran supaya blok ini punya titik
                     masuk. Pemisahnya baris kosong, bukan titik: naskahnya kini
                     dua paragraf pendek dan memecah pada titik pertama akan
                     memotong kalimat kedua di tengah paragraf yang sama. --}}
                @php
                    [$narrativeLead, $narrativeRest] = array_pad(preg_split('/\n\s*\n/', trim($narrative), 2), 2, '');
                @endphp
                <p class="mt-7 max-w-2xl text-pretty text-lg leading-relaxed text-navy md:text-xl">{{ $narrativeLead }}</p>
                @if ($narrativeRest !== '')
                    <p class="mt-5 max-w-2xl text-pretty leading-relaxed text-slate-600">{{ $narrativeRest }}</p>
                @endif

                {{-- Tiga angka, bukan tiga kalimat lagi. Pembaca di titik ini sedang
                     menakar besarnya, dan angka menjawabnya dengan kata paling
                     sedikit. Ketiganya dibaca dari data halaman ini sendiri —
                     jumlah paket, jumlah lembaga mitra, dan papan angka perusahaan —
                     jadi tidak ada angka yang perlu dijaga manual.

                     Susunannya sama dengan papan angka di beranda dan halaman
                     Sertifikat: angka besar di atas, keterangan di bawah, dipisah
                     garis rambut tegak. --}}
                @php
                    $trainedStat = collect(\App\Support\Company::stats())
                        ->first(fn ($s) => str_contains(mb_strtolower((string) $s['label']), $id ? 'profesional' : 'professional'));

                    $narrativeFigures = array_values(array_filter([
                        $packages->isNotEmpty()
                            ? [(string) $packages->count(), '', $id ? 'Tingkat kemitraan' : 'Partnership tiers']
                            : null,
                        $partners->isNotEmpty()
                            ? [(string) $partners->count(), '', $id ? 'Lembaga sertifikasi mitra' : 'Partner certification bodies']
                            : null,
                        $trainedStat
                            ? [$trainedStat['number'], $trainedStat['suffix'], $trainedStat['label']]
                            : null,
                    ]));
                @endphp
                @if (count($narrativeFigures) > 1)
                    <dl class="mt-10 grid max-w-2xl grid-cols-3 gap-x-4 border-t border-navy-100 pt-7 sm:gap-x-8">
                        @foreach ($narrativeFigures as $i => [$number, $suffix, $label])
                            <div @class(['border-l border-navy-100 pl-4 sm:pl-8' => $i > 0])>
                                <dd class="font-display text-3xl font-bold leading-none tracking-tight text-navy sm:text-4xl">
                                    <span class="tabular-nums" data-counter="{{ $number }}">{{ $number }}</span><span class="text-sky-600">{{ $suffix }}</span>
                                </dd>
                                <dt class="mt-2.5 text-sm leading-snug text-slate-600">{{ $label }}</dt>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="100">
                <div class="relative h-full overflow-hidden rounded-2xl bg-navy-950 p-8 text-white md:p-10">
                    <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-50"></div>
                    <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                    <div class="relative flex h-full flex-col">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl border border-gold/40 text-gold">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v13H4zM4 7l3-3h10l3 3M9 12h6" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                        </span>
                        <p class="mt-6 font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $id ? 'Penagihan' : 'Billing' }}</p>
                        <p class="mt-3 font-display text-2xl leading-snug text-balance">{{ $id ? 'Tanpa pembayaran online. Semuanya lewat invoice.' : 'No online payment. Everything goes through an invoice.' }}</p>
                        <p class="mt-auto border-t border-white/10 pt-6 text-sm leading-relaxed text-navy-100">{{ $id ? 'Penawaran resmi disiapkan tim kami setelah presentasi.' : 'Our team prepares the formal offer after the presentation.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PARTNER BODIES =====================
         Ditempatkan sebelum daftar manfaat karena menjawab pertanyaan yang
         datang lebih dulu: siapa yang menerbitkan sertifikatnya. Tanpa itu,
         "Sertifikasi dan Pengakuan Kompetensi" di bawah hanya sebuah klaim.

         Tanpa label kecil di atas judulnya. Judul dan kalimat pendukung sudah
         menyebut isinya, dan seksi lain di halaman ini pun tidak memakainya. --}}
    @if ($partners->isNotEmpty())
        @php
            // Judul dan deskripsi bisa diganti admin lewat Pengaturan Situs →
            // Seksi Lembaga Mitra. Dikosongkan berarti memakai teks bawaan di
            // bawah ini, jadi seksinya tidak pernah tampil tanpa tulisan.
            //
            // Teks bawaannya menyebut "lembaga sertifikasi", "pelatihan", dan
            // "uji kompetensi": istilah yang memang dicari orang, bukan kiasan
            // yang tidak pernah diketik siapa pun.
            $pTitle = trim((string) ($partnersTitle ?? '')) ?: ($id
                ? 'Lembaga sertifikasi yang *bekerja sama* dengan kami'
                : 'Certification bodies we *work with*');

            $pDesc = trim((string) ($partnersDesc ?? '')) ?: ($id
                ? 'Pelatihan dan uji kompetensi kami dijalankan bersama lembaga sertifikasi profesi berlisensi BNSP. Sertifikat yang diterima karyawan Anda diakui secara nasional.'
                : 'Our training and competency assessment run with BNSP-licensed certification bodies. The certificates your employees earn are recognised nationwide.');

            // Satu penggalan di antara tanda bintang dimiringkan. Tanpa bintang
            // judulnya tegak seluruhnya, jadi admin tidak perlu mengetik HTML
            // dan tidak ada cara mengetik judul yang merusak halamannya.
            $pLead = $pTitle;
            $pAccent = $pTail = '';
            if (preg_match('/^(.*?)\*(.+?)\*(.*)$/', $pTitle, $m)) {
                [$pLead, $pAccent, $pTail] = [trim($m[1]), trim($m[2]), trim($m[3])];
            }
        @endphp

        <section class="section-sm border-t border-navy-50 bg-white">
            <div class="container">
                <div class="max-w-2xl" data-aos="fade-up">
                    {{-- Judul ini datang dari CMS, dan admin sendiri yang memilih
                         kata mana yang dimiringkan lewat tanda *…*. Karena itulah
                         aksennya di sini berwarna: ia dipasang karena diminta,
                         bukan karena tiap seksi otomatis mendapatkannya. --}}
                    <x-heading :lead="$pLead" :accent="$pAccent" :tail="$pTail"
                               class="text-display-lg text-navy" />
                    <p class="mt-5 text-pretty leading-relaxed text-slate-600">{{ $pDesc }}</p>
                </div>

                {{-- Empat kolom mengikuti jumlah mitra saat ini; bila nanti bertambah
                     barisnya yang bertambah, bukan pelatnya yang menyusut — di halaman
                     seputih ini logo yang terlalu kecil langsung hilang terbaca. --}}
                <div class="mt-12 grid gap-5 sm:grid-cols-2 md:gap-6 lg:grid-cols-4">
                    @foreach ($partners as $partner)
                        <x-partner-plate :partner="$partner" tone="light"
                                         data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 50 }}" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ===================== BENEFITS ===================== --}}
    {{-- Normalise DB rows and the hardcoded fallback into one shape, so the card
         markup exists once instead of being copy-pasted per data source. --}}
    @php
        $benefitCards = (isset($benefits) && $benefits->isNotEmpty())
            ? $benefits->map(fn ($b) => ['title' => $b->title, 'desc' => $b->description, 'icon' => $b->icon])
            : collect($manfaat)->map(fn ($m) => ['title' => $m[0], 'desc' => $m[1], 'icon' => null]);

        /* Angka mitra diambil dari sumber yang sama dengan papan angka di beranda
           dan halaman Tentang. Ia hanya dipakai kalau memang ada — kartu pertama
           tetap utuh tanpanya. */
        $partnerStat = collect(\App\Support\Company::stats())
            ->first(fn ($s) => str_contains(mb_strtolower((string) $s['label']), $id ? 'klien' : 'client'));

        /* Ikonnya datang dari kolom 'icon' tiap baris (nama bergaya heroicon yang
           diketik admin). Yang tidak dikenali jatuh ke tanda centang, jadi salah
           ketik di panel tidak pernah menghasilkan kartu tanpa ikon. */
        $benefitIcons = [
            'academic-cap' => '<path d="M12 4.5 2.8 9 12 13.5 21.2 9 12 4.5Z"/><path d="M5.5 10.8v4.4c0 .6.3 1.1.8 1.4 1.5.9 3.6 1.4 5.7 1.4s4.2-.5 5.7-1.4c.5-.3.8-.8.8-1.4v-4.4"/>',
            'badge-check' => '<path d="M12 3.2 14 5l2.6-.3.9 2.4 2.3 1.3-.7 2.6.7 2.6-2.3 1.3-.9 2.4-2.6-.3-2 1.8-2-1.8-2.6.3-.9-2.4-2.3-1.3.7-2.6-.7-2.6L4.5 7.1l.9-2.4L8 5l2-1.8Z"/><path d="m9.3 11.9 1.9 1.9 3.5-3.8"/>',
            'users' => '<path d="M16 19v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 17.5V19"/><circle cx="10" cy="8" r="3.2"/><path d="M20 19v-1.4a3.3 3.3 0 0 0-2.5-3.2M15.5 5.3a3.2 3.2 0 0 1 0 5.9"/>',
            'chart-bar' => '<path d="M4 20h16"/><path d="M7 20v-6M12 20V8M17 20v-9"/>',
            'calendar' => '<rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3.5v3M16 3.5v3"/>',
            'shield-check' => '<path d="M12 3.5 5 6v5.5c0 4 2.9 7.4 7 8.5 4.1-1.1 7-4.5 7-8.5V6l-7-2.5Z"/><path d="m9.3 11.9 1.9 1.9 3.5-3.8"/>',
        ];
        $benefitIconFallback = '<circle cx="12" cy="12" r="8.5"/><path d="m8.5 12.2 2.4 2.4 4.6-5"/>';
    @endphp
    <section class="section-sm border-t border-navy-50 bg-white">
        <div class="container">
            <div class="max-w-2xl" data-aos="fade-up">
                <h2 class="text-display-lg text-navy">{{ $id ? 'Mengapa menjadi mitra kami' : 'Why partner with us' }}</h2>
            </div>

            {{-- Susunan bento, bukan satu bidang berpetak. Enam petak seukuran membuat
                 keenam alasan terbaca sama bobotnya, padahal yang pertama memikul
                 janji utamanya dan sisanya menopang.

                 Dua kartu lebar di baris pertama, tiga kartu di baris kedua. Yang
                 pertama gelap bergradasi karena memang hendak dibaca lebih dulu; yang
                 kelima navy pekat supaya barisnya punya tumpuan di ujung dan tidak
                 melayang sebagai tiga kotak putih berderet.

                 Lebar kartunya ditentukan posisi, jadi urutan di panel admin ikut
                 menentukan mana yang jadi kartu besar. Kartu keenam dan seterusnya
                 mengalir sebagai kartu sepertiga, jadi menambah butir di panel tidak
                 merusak susunannya. --}}
            {{-- Kartu yang ditunjuk menyala, dan keadaannya bertahan setelah
                 tunjukan ditarik — pola yang sama dengan kartu "Kenapa daftar ini
                 dibuka" di halaman Sertifikat, dan memakai kelas yang sama.

                 Tidak ada yang diredupkan. Tiga latar bercampur di satu baris
                 (putih, navy, navy bergradasi), dan teks slate-600 di atas kartu
                 putih jatuh ke 2,88:1 begitu diredupkan ke 0,6 — jauh di bawah
                 ambang AA. Yang aktif justru ditambahi: cincin emas, pendar pada
                 ubin ikonnya, dan naik 4px. --}}
            <div class="mt-12 grid gap-5 md:grid-cols-2 md:gap-6 lg:grid-cols-12" x-data="{ active: 0 }">
                @foreach ($benefitCards as $i => $card)
                    @php
                        $wide = $i < 2;                 // dua kartu pertama: setengah baris
                        $dark = $i === 0 || $i === 4;   // kartu pembuka dan penutup barisnya
                        $icon = $benefitIcons[$card['icon'] ?? ''] ?? $benefitIconFallback;
                    @endphp
                    <div data-aos="fade-up" data-aos-delay="{{ min($i, 3) * 50 }}"
                         tabindex="0"
                         x-on:mouseenter="active = {{ $i }}"
                         x-on:focus="active = {{ $i }}"
                         x-on:click="active = {{ $i }}"
                         :data-on="active === {{ $i }} ? '1' : '0'"
                         class="hover-card relative flex flex-col overflow-hidden rounded-2xl p-7 outline-none md:p-8 {{ $wide ? 'lg:col-span-6' : 'lg:col-span-4' }} {{ $i === 0
                             ? 'bg-gradient-to-br from-navy-700 via-navy-800 to-navy-950 text-white ring-1 ring-white/10'
                             : ($dark ? 'bg-navy-950 text-white ring-1 ring-white/10' : 'bg-white ring-1 ring-navy-100') }}">

                        @if ($i === 0)
                            <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-30"></div>
                        @endif

                        <div class="relative flex flex-1 flex-col">
                            <span class="hover-card-icon grid h-12 w-12 place-items-center rounded-xl {{ $dark ? 'bg-white/10 text-sky-400 ring-1 ring-white/10' : 'bg-sky-50 text-sky-700' }}"
                                  aria-hidden="true"
                                  :data-on="active === {{ $i }} ? '1' : '0'">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                            </span>

                            {{-- Angka mitra hanya menempel di kartu pembuka, sebagai
                                 keterangan kecil di atas judulnya. Ia bukti, bukan
                                 pokok kalimat, jadi ia tidak menggantikan judulnya. --}}
                            @if ($i === 0 && $partnerStat)
                                <p class="mt-6 font-mono text-[11px] uppercase tracking-wider text-gold-soft">
                                    {{ $partnerStat['number'] }}{{ $partnerStat['suffix'] }} {{ mb_strtolower($partnerStat['label']) }}
                                </p>
                            @endif

                            <h3 @class([
                                'leading-snug',
                                'mt-2.5 text-2xl md:text-3xl' => $i === 0 && $partnerStat,
                                'mt-6 text-2xl md:text-3xl' => $i === 0 && ! $partnerStat,
                                'mt-6 text-lg md:text-xl' => $i !== 0,
                                'text-white' => $dark,
                                'text-navy' => ! $dark,
                            ])>{{ $card['title'] }}</h3>

                            <p class="mt-3 text-pretty text-sm leading-relaxed {{ $dark ? 'text-navy-100' : 'text-slate-600' }} {{ $i === 0 ? 'md:text-base' : '' }}">{{ $card['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Packages + Form share the selected-package state so "Pilih Paket" pre-fills the form --}}
    <div x-data="{ pkg: '{{ old('partnership_package_id') }}' }">

        {{-- ===================== PACKAGES ===================== --}}
        @if ($packages->isNotEmpty())
            <section class="section border-t border-navy-50 bg-white">
                <div class="container">
                    <div class="max-w-3xl" data-aos="fade-up">
                        <h2 class="text-display-lg text-navy">{{ $id ? 'Pilih paket kemitraan Anda' : 'Choose your partnership package' }}</h2>
                        <p class="mt-4 font-mono text-xs uppercase tracking-normal text-slate-500">Blue · Silver · Gold · Platinum</p>
                    </div>

                    <div class="mt-14 grid items-stretch gap-6 md:grid-cols-2 lg:grid-cols-4">
                        @foreach ($packages as $package)
                            @php
                                $meta = $tierMeta[$package->tier] ?? ['tagline' => '', 'perks' => []];
                                $tagline = $package->tagline ?: $meta['tagline'];
                                $dbFeatures = $package->features;
                                if (is_string($dbFeatures)) {
                                    $dbFeatures = json_decode($dbFeatures, true);
                                }
                                $perks = (!empty($dbFeatures) && is_array($dbFeatures)) ? $dbFeatures : $meta['perks'];
                            @endphp
                            {{-- The recommended tier is raised and ringed at rest, not only on
                                 hover: a badge alone does not draw the eye on a row of four
                                 equally sized towers. --}}
                            <div class="group relative flex flex-col overflow-hidden rounded-2xl border bg-white transition-all duration-300 hover:-translate-y-2 hover:shadow-lift
                                        {{ $package->is_highlighted
                                            ? 'border-gold shadow-lift lg:-translate-y-4'
                                            : 'border-navy-100 shadow-card' }}"
                                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">

                                {{-- Gradient header --}}
                                <div class="relative overflow-hidden bg-gradient-to-br {{ $tierStyle[$package->tier] ?? 'from-navy-700 to-navy-950' }} p-7 text-white">
                                    <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                                    <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
                                    <div class="relative flex items-center justify-between gap-3">
                                        <p class="font-mono text-[10px] uppercase tracking-label text-white/70">{{ $id ? 'Paket' : 'Package' }}</p>
                                        @if ($package->is_highlighted)
                                            <span class="shrink-0 rounded-full bg-white px-2.5 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider text-navy-950">{{ $id ? 'Populer' : 'Popular' }}</span>
                                        @endif
                                    </div>
                                    <h3 class="relative mt-2 text-3xl">{{ $package->name }}</h3>
                                    {{-- min-height reserves 2 lines so every colored header box is the same size --}}
                                    <p class="relative mt-3 line-clamp-2 min-h-[2.85rem] text-sm leading-relaxed text-white/85">{{ $tagline }}</p>

                                    {{-- Each tier includes the one below it ("Seluruh manfaat Silver"),
                                         but that progression was only stated in prose. These bars show
                                         where the package sits at a glance. --}}
                                    <div class="relative mt-5 flex items-center gap-1.5" aria-hidden="true">
                                        @for ($lvl = 1; $lvl <= $packages->count(); $lvl++)
                                            <span class="h-1 flex-1 rounded-full {{ $lvl <= $loop->iteration ? 'bg-white/85' : 'bg-white/20' }}"></span>
                                        @endfor
                                    </div>
                                </div>

                                {{-- Body --}}
                                <div class="flex flex-1 flex-col p-7">
                                    <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ $id ? 'Investasi' : 'Investment' }}</p>
                                    <p class="mt-1 font-display text-lg text-navy">{{ $package->price ? 'Rp '.number_format((float) $package->price, 0, ',', '.') : ($id ? 'Via penawaran (invoice)' : 'By offer (invoice)') }}</p>

                                    <ul class="mt-6 flex-1 space-y-3 border-t border-navy-100 pt-6">
                                        @foreach ($perks as $perk)
                                            <li class="flex items-start gap-2.5 text-sm text-slate-700">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span>{{ $perk }}</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <a href="#daftar" @click="pkg = '{{ $package->id }}'"
                                       class="{{ $package->is_highlighted ? 'btn-blue' : 'btn-ghost' }} mt-7 w-full">{{ $id ? 'Pilih Paket' : 'Choose Plan' }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ===================== REGISTRATION FORM ===================== --}}
        <section id="daftar" class="section scroll-mt-28 border-t border-navy-50 bg-neutral-50">
            <div class="container">
                {{-- Centered heading --}}
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-display-lg text-navy">{{ $id ? 'Mulai kolaborasi' : 'Start a collaboration' }}</h2>
                    <p class="mx-auto mt-5 max-w-xl text-pretty leading-relaxed text-slate-600">
                        {{ $id
                            ? 'Isi formulir dan tim PT Delta Tiga Enam akan menghubungi Anda untuk menjadwalkan presentasi serta menyiapkan penawaran. Tanpa pembayaran online, penagihan melalui invoice.'
                            : 'Submit the form and the PT Delta Tiga Enam team will contact you to schedule a presentation and prepare an offer. No online payment, billing via invoice.' }}
                    </p>
                </div>

                {{-- Steps --}}
                {{-- A connector line ties the three into one flow; as plain columns they read
                     as unrelated items rather than "first this, then this, then this". --}}
                <div class="relative mx-auto mt-10 grid max-w-3xl grid-cols-3 gap-3 sm:gap-5">
                    <span class="pointer-events-none absolute left-[16.667%] right-[16.667%] top-[18px] hidden h-px bg-gradient-to-r from-sky-200 via-sky-300 to-sky-200 sm:block md:top-5" aria-hidden="true"></span>
                    @foreach ($steps as $i => $step)
                        <div class="flex flex-col items-center gap-2 text-center md:gap-3">
                            <span class="relative grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 font-display text-xs text-white ring-4 ring-neutral-50 md:h-10 md:w-10 md:text-sm">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="text-xs leading-relaxed text-slate-700 md:text-sm">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Form --}}
                <div class="mx-auto mt-12 max-w-3xl">
                    @if (session('status'))
                        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status" aria-live="polite">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M16.7 5.7a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 10a1 1 0 011.4-1.4l3.3 3.3 6.8-6.8a1 1 0 011.4 0z"/></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                            <p class="font-semibold">{{ $id ? 'Periksa kembali isian berikut:' : 'Please review the following:' }}</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('partnership.store') }}" class="overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card">
                        @csrf
                        <div style="display:none !important" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="space-y-8 p-5 sm:p-7 md:space-y-10 md:p-10">
                            {{-- A. Company info --}}
                            <div>
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-700">A · {{ $id ? 'Informasi Perusahaan' : 'Company Information' }}</p>
                                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                    <x-field name="company_name" :label="$id ? 'Nama Perusahaan' : 'Company Name'" required />
                                    <x-field name="pic_name" :label="$id ? 'Nama PIC' : 'PIC Name'" required />
                                    <x-field name="pic_position" :label="$id ? 'Jabatan PIC' : 'PIC Position'" />
                                    <x-field name="phone" :label="$id ? 'No. Telp/WhatsApp' : 'Phone/WhatsApp'" required />
                                    <x-field name="email" type="email" :label="__('site.contact.email')" required />
                                </div>
                                <div class="mt-5">
                                    <x-field name="company_address" type="textarea" :label="$id ? 'Alamat Perusahaan' : 'Company Address'" required />
                                </div>
                            </div>

                            {{-- B. Package --}}
                            <div class="border-t border-navy-100 pt-8 md:pt-10">
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-700">B · {{ $id ? 'Pilihan Paket' : 'Package Choice' }}</p>
                                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    @foreach ($packages as $package)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="partnership_package_id" value="{{ $package->id }}" x-model="pkg" class="peer sr-only">
                                            <span class="block rounded-2xl border border-navy-200 bg-white px-4 py-3 text-center font-display text-navy transition-all hover:border-navy-300 peer-checked:border-sky-700 peer-checked:text-sky-700 peer-checked:ring-1 peer-checked:ring-sky-700">{{ $package->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('partnership_package_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- C. Scheduling --}}
                            <div class="border-t border-navy-100 pt-8 md:pt-10">
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-700">C · {{ $id ? 'Penjadwalan Presentasi/Meeting' : 'Presentation/Meeting Scheduling' }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Pilih waktu yang Anda inginkan; tim kami akan mengonfirmasi.' : 'Choose your preferred time; our team will confirm.' }}</p>
                                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                    <div class="min-w-0">
                                        <label for="preferred_meeting_at" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Waktu Diinginkan' : 'Preferred Time' }}</label>
                                        <input type="datetime-local" id="preferred_meeting_at" name="preferred_meeting_at" value="{{ old('preferred_meeting_at') }}" class="w-full min-w-0 rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-700">
                                    </div>
                                    <div class="min-w-0">
                                        <label for="alternative_meeting_at" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Waktu Alternatif' : 'Alternative Time' }}</label>
                                        <input type="datetime-local" id="alternative_meeting_at" name="alternative_meeting_at" value="{{ old('alternative_meeting_at') }}" class="w-full min-w-0 rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-700">
                                    </div>
                                </div>
                            </div>

                            {{-- D. Notes --}}
                            <div class="border-t border-navy-100 pt-8 md:pt-10">
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-700">D · {{ $id ? 'Catatan Tambahan' : 'Additional Notes' }}</p>
                                <div class="mt-5">
                                    <x-field name="notes" type="textarea" :label="$id ? 'Catatan' : 'Notes'" />
                                </div>
                            </div>
                        </div>

                        {{-- Footer bar --}}
                        <div class="flex flex-col gap-4 border-t border-navy-100 bg-neutral-50 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7 sm:py-6 md:px-10">
                            <p class="max-w-sm text-xs leading-relaxed text-slate-500">{{ $id ? 'Dengan mengirim, Anda setuju dihubungi oleh tim PT Delta Tiga Enam. Tanpa pembayaran online.' : 'By submitting, you agree to be contacted by the PT Delta Tiga Enam team. No online payment.' }}</p>
                            <button type="submit" class="btn-blue shrink-0">
                                {{ __('site.cta.send') }}
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    {{-- ===================== PENUTUP: KONSULTASI =====================
         Penutup halaman Kemitraan, susunannya sama dengan penutup halaman
         Layanan: foto berlapis biru, judul di tengah, satu kalimat, satu
         tombol. Dua halaman yang berakhir dengan bentuk yang sama membuat
         penutup itu terbaca sebagai bagian dari situsnya, bukan sebagai
         kejutan di tiap halaman.

         Isinya justru harus berbeda dari formulir tepat di atasnya. Formulir
         itu untuk yang sudah memilih paket; seksi ini untuk yang menggulir
         melewatinya karena belum tahu paket mana yang masuk akal — dan tanpa
         ini, satu-satunya jalan bagi orang itu adalah menutup halaman.

         Fotonya bukan foto yang sama dengan penutup halaman Layanan: dua
         penutup yang bentuknya sudah seragam akan terbaca sebagai halaman
         yang sama kalau gambarnya pun sama. --}}
    @php
        $closingPhoto = 'images/kemitraan-penutup.webp';
        $hasClosingPhoto = is_file(public_path($closingPhoto));
    @endphp
    <section class="relative isolate overflow-hidden bg-navy-950 text-white">
        @if ($hasClosingPhoto)
            <img src="{{ asset($closingPhoto) }}" alt="" aria-hidden="true"
                 width="1600" height="900" loading="lazy" decoding="async"
                 class="absolute inset-0 -z-10 h-full w-full object-cover object-center">
        @endif

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
                    {{ $id ? 'Masih menimbang tingkat yang mana?' : 'Still weighing up which tier?' }}
                </h2>

                {{-- Menyebut apa yang perlu disiapkan sebelum menghubungi, bukan
                     janji umum. Orang menunda bertanya karena tidak tahu apa yang
                     akan ditanyakan balik. --}}
                <p class="mx-auto mt-5 max-w-xl text-pretty text-lg leading-relaxed text-navy-100">
                    {{ $id
                        ? 'Sebutkan jumlah karyawan dan rencana pelatihan setahun ke depan, lalu kami hitungkan tingkat mana yang paling masuk akal. Tanpa biaya.'
                        : 'Tell us your headcount and what training the year ahead needs, and we will work out which tier makes sense. No charge.' }}
                </p>

                <div class="mt-9 flex justify-center">
                    <a href="{{ route('contact.index') }}" class="btn-blue">
                        {{ $id ? 'Bicarakan Dulu Kebutuhannya' : 'Talk It Through First' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
