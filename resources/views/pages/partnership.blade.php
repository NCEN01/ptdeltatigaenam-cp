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
        ['Peningkatan Kinerja Perusahaan', 'Dengan SDM yang lebih terlatih dan kompeten, perusahaan dapat mencapai target lebih efisien dan efektif — menjadikan SDM sebagai aset strategis menghadapi persaingan.'],
    ] : [
        ['Improve HR Competency & Productivity', 'Delivers relevant new knowledge and skills, helping the company optimize work processes and improve human capital performance.'],
        ['Customizable Training Solutions', 'Companies can tailor training topics, such as case-study based training, with flexible scheduling.'],
        ['Cost Savings', 'With discounts on public training and certification, companies save budget and improve efficiency.'],
        ['Certification & Competency Recognition', 'Participants earn professionally recognized certificates, boosting credibility and competency in the industry.'],
        ['Priority Service for Premium Partners', 'Gold and Platinum partners receive priority training schedules and additional consulting services.'],
        ['Improved Company Performance', 'With a better-trained, more competent workforce, the company reaches its targets more efficiently — making people a strategic asset.'],
    ];

    $narrative = $id
        ? 'Kami dari PT Delta Tiga Enam, sebagai mitra terpercaya dalam pengembangan sumber daya manusia, mengajukan Penawaran Program Kemitraan Pelatihan SDM yang dirancang untuk mendukung pengembangan kompetensi karyawan di perusahaan Bapak/Ibu. Program kemitraan ini merupakan bentuk komitmen kami untuk memberikan solusi pelatihan yang efektif, relevan, dan dapat disesuaikan dengan kebutuhan perusahaan Anda. Dengan menjalin kemitraan ini, kami percaya perusahaan Bapak/Ibu dapat meningkatkan produktivitas, efisiensi, dan kualitas kinerja tim secara signifikan.'
        : 'PT Delta Tiga Enam, as a trusted partner in human capital development, presents an HR Training Partnership Program designed to support employee competency development at your company. This program reflects our commitment to delivering training solutions that are effective, relevant, and tailored to your needs. Through this partnership, we believe your company can significantly improve productivity, efficiency, and team performance.';

    $steps = $id
        ? ['Isi & kirim formulir pendaftaran', 'Tim kami menghubungi & menjadwalkan presentasi', 'Terima penawaran resmi melalui invoice']
        : ['Fill in & submit the registration form', 'Our team contacts you & schedules a presentation', 'Receive a formal offer via invoice'];
@endphp

<x-layout :title="__('site.nav.partnership')" :description="$narrative">
    <x-page-header
        :eyebrow="$id ? 'Kemitraan Corporate' : 'Corporate Partnership'"
        :title="$id ? 'Program Kemitraan Corporate Training' : 'Corporate Training Partnership Program'"
        :subtitle="$intro ?: ($id ? 'Solusi pelatihan SDM yang efektif, relevan, dan dapat disesuaikan dengan kebutuhan perusahaan Anda.' : 'Effective, relevant HR training solutions tailored to your company needs.')"
        placement="partnership"
        image="photo-1600880292089-90a7e086ee0c">
        <a href="#daftar" class="btn-blue">{{ $id ? 'Daftar Sekarang' : 'Apply Now' }}</a>
    </x-page-header>

    {{-- ===================== NARRATIVE ===================== --}}
    <section class="section bg-white">
        <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7" data-aos="fade-up">
                <p class="eyebrow mb-5"><span class="rule-gold mr-3"></span>{{ $id ? 'Tentang Program' : 'About the Program' }}</p>
                <h2 class="font-display text-3xl leading-tight text-navy text-balance md:text-4xl">{{ $id ? 'Kemitraan PT Delta Tiga Enam' : 'PT Delta Tiga Enam Partnership' }}</h2>
                <p class="mt-7 max-w-2xl text-pretty text-lg leading-relaxed text-slate-700">{{ $narrative }}</p>
            </div>
            <div class="lg:col-span-5" data-aos="fade-left" data-aos-delay="100">
                <div class="relative h-full overflow-hidden rounded-3xl bg-navy-950 p-8 text-white md:p-10">
                    <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-50"></div>
                    <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                    <div class="relative flex h-full flex-col">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl border border-gold/40 text-gold">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v13H4zM4 7l3-3h10l3 3M9 12h6" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                        </span>
                        <p class="mt-6 font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $id ? 'Penagihan' : 'Billing' }}</p>
                        <p class="mt-3 font-display text-2xl leading-snug text-balance">{{ $id ? 'Tanpa pembayaran online — seluruh kerja sama difinalisasi melalui invoice.' : 'No online payment — every partnership is finalized via invoice.' }}</p>
                        <p class="mt-auto border-t border-white/10 pt-6 text-sm leading-relaxed text-navy-200">{{ $id ? 'Tim kami menyiapkan penawaran resmi setelah presentasi.' : 'Our team prepares a formal offer after the presentation.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== BENEFITS ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-neutral-50">
        <div class="container">
            <div class="max-w-2xl" data-aos="fade-up">
                <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $id ? 'Manfaat Program' : 'Program Benefits' }}</p>
                <h2 class="font-display text-3xl text-navy text-balance md:text-4xl">{{ $id ? 'Mengapa menjadi mitra kami' : 'Why partner with us' }}</h2>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @if (isset($benefits) && $benefits->isNotEmpty())
                    @foreach ($benefits as $benefit)
                        <div class="group flex flex-col rounded-2xl border border-navy-100 bg-white p-7 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-navy-500 to-sky-400 font-display text-lg text-white transition-all duration-300 group-hover:from-navy-600 group-hover:to-navy-500">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="mt-6 font-display text-lg text-navy">{{ $benefit->title }}</h3>
                            <p class="mt-2.5 text-pretty text-sm leading-relaxed text-slate-600">{{ $benefit->description }}</p>
                            <span class="mt-5 block h-0.5 w-0 rounded-full bg-gradient-to-r from-gold to-gold-soft transition-all duration-500 group-hover:w-12"></span>
                        </div>
                    @endforeach
                @else
                    @foreach ($manfaat as $item)
                        <div class="group flex flex-col rounded-2xl border border-navy-100 bg-white p-7 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-navy-500 to-sky-400 font-display text-lg text-white transition-all duration-300 group-hover:from-navy-600 group-hover:to-navy-500">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="mt-6 font-display text-lg text-navy">{{ $item[0] }}</h3>
                            <p class="mt-2.5 text-pretty text-sm leading-relaxed text-slate-600">{{ $item[1] }}</p>
                            <span class="mt-5 block h-0.5 w-0 rounded-full bg-gradient-to-r from-gold to-gold-soft transition-all duration-500 group-hover:w-12"></span>
                        </div>
                    @endforeach
                @endif
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
                        <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $id ? 'Penawaran Paket' : 'Package Offerings' }}</p>
                        <h2 class="font-display text-3xl text-navy text-balance md:text-4xl">{{ $id ? 'Pilih paket kemitraan Anda' : 'Choose your partnership package' }}</h2>
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
                            <div class="group relative flex flex-col overflow-hidden rounded-3xl border bg-white shadow-card transition-all duration-300 hover:-translate-y-2 hover:shadow-lift {{ $package->is_highlighted ? 'border-gold ring-1 ring-gold' : 'border-navy-100' }}"
                                 data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                                @if ($package->is_highlighted)
                                    <span class="absolute right-5 top-5 z-10 rounded-full bg-gold px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-navy-950 shadow-gold">{{ $id ? 'Populer' : 'Popular' }}</span>
                                @endif

                                {{-- Gradient header --}}
                                <div class="relative overflow-hidden bg-gradient-to-br {{ $tierStyle[$package->tier] ?? 'from-navy-700 to-navy-950' }} p-7 text-white">
                                    <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
                                    <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
                                    <p class="relative font-mono text-[10px] uppercase tracking-label text-white/70">{{ $id ? 'Paket' : 'Package' }}</p>
                                    <h3 class="relative mt-2 font-display text-3xl">{{ $package->name }}</h3>
                                    {{-- min-height reserves 2 lines so every colored header box is the same size --}}
                                    <p class="relative mt-3 line-clamp-2 min-h-[2.85rem] text-sm leading-relaxed text-white/85">{{ $tagline }}</p>
                                </div>

                                {{-- Body --}}
                                <div class="flex flex-1 flex-col p-7">
                                    <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ $id ? 'Investasi' : 'Investment' }}</p>
                                    <p class="mt-1 font-display text-lg text-navy">{{ $package->price ? 'Rp '.number_format((float) $package->price, 0, ',', '.') : ($id ? 'Via penawaran (invoice)' : 'By offer (invoice)') }}</p>

                                    <ul class="mt-6 flex-1 space-y-3 border-t border-navy-100 pt-6">
                                        @foreach ($perks as $perk)
                                            <li class="flex items-start gap-2.5 text-sm text-slate-700">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                    <p class="eyebrow mb-4 inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $id ? 'Daftar Kemitraan' : 'Partnership Application' }}</p>
                    <h2 class="font-display text-3xl text-navy text-balance md:text-4xl">{{ $id ? 'Mulai kolaborasi' : 'Start a collaboration' }}</h2>
                    <p class="mx-auto mt-5 max-w-xl text-pretty leading-relaxed text-slate-600">
                        {{ $id
                            ? 'Isi formulir dan tim PT Delta Tiga Enam akan menghubungi Anda untuk menjadwalkan presentasi serta menyiapkan penawaran. Tanpa pembayaran online — penagihan melalui invoice.'
                            : 'Submit the form and the PT Delta Tiga Enam team will contact you to schedule a presentation and prepare an offer. No online payment — billing via invoice.' }}
                    </p>
                </div>

                {{-- Steps --}}
                <div class="mx-auto mt-10 grid max-w-3xl grid-cols-3 gap-3 sm:gap-5">
                    @foreach ($steps as $i => $step)
                        <div class="flex flex-col items-center gap-2 text-center md:gap-3">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 font-display text-xs text-white md:h-10 md:w-10 md:text-sm">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
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

                    <form method="POST" action="{{ route('partnership.store') }}" class="overflow-hidden rounded-3xl border border-navy-100 bg-white shadow-lift">
                        @csrf
                        <div style="display:none !important" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="space-y-8 p-5 sm:p-7 md:space-y-10 md:p-10">
                            {{-- A. Company info --}}
                            <div>
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-600">A · {{ $id ? 'Informasi Perusahaan' : 'Company Information' }}</p>
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
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-600">B · {{ $id ? 'Pilihan Paket' : 'Package Choice' }}</p>
                                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                    @foreach ($packages as $package)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="partnership_package_id" value="{{ $package->id }}" x-model="pkg" class="peer sr-only">
                                            <span class="block rounded-2xl border border-navy-200 bg-white px-4 py-3 text-center font-display text-navy transition-all hover:border-navy-300 peer-checked:border-sky-500 peer-checked:text-sky-700 peer-checked:ring-1 peer-checked:ring-sky-500">{{ $package->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('partnership_package_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- C. Scheduling --}}
                            <div class="border-t border-navy-100 pt-8 md:pt-10">
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-600">C · {{ $id ? 'Penjadwalan Presentasi/Meeting' : 'Presentation/Meeting Scheduling' }}</p>
                                <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Pilih waktu yang Anda inginkan; tim kami akan mengonfirmasi.' : 'Choose your preferred time; our team will confirm.' }}</p>
                                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                    <div class="min-w-0">
                                        <label for="preferred_meeting_at" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Waktu Diinginkan' : 'Preferred Time' }}</label>
                                        <input type="datetime-local" id="preferred_meeting_at" name="preferred_meeting_at" value="{{ old('preferred_meeting_at') }}" class="w-full min-w-0 rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                    </div>
                                    <div class="min-w-0">
                                        <label for="alternative_meeting_at" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Waktu Alternatif' : 'Alternative Time' }}</label>
                                        <input type="datetime-local" id="alternative_meeting_at" name="alternative_meeting_at" value="{{ old('alternative_meeting_at') }}" class="w-full min-w-0 rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                    </div>
                                </div>
                            </div>

                            {{-- D. Notes --}}
                            <div class="border-t border-navy-100 pt-8 md:pt-10">
                                <p class="font-mono text-[11px] uppercase tracking-label text-sky-600">D · {{ $id ? 'Catatan Tambahan' : 'Additional Notes' }}</p>
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
</x-layout>
