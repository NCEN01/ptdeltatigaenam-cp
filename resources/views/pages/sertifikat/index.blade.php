@php
    $id = app()->getLocale() === 'id';

    /* Angka ringkasan. Ditulis sebagai daftar supaya barisnya dirender satu kali,
       bukan tiga blok markup yang hampir sama.

       Hanya angka kedaluwarsa yang diberi warna, dan hanya kalau memang ada —
       nol yang dimerahkan menakut-nakuti tanpa sebab. Nol diredupkan ke
       slate-500 (4,6:1), bukan navy-200: navy-200 dibuat untuk latar gelap dan
       hanya berkontras 1,5:1 di atas latar terang ini. */
    $figures = [
        ['value' => $certificates->total(), 'label' => $id ? 'Terdaftar' : 'Registered'],
        ['value' => $validCount, 'label' => $id ? 'Masih berlaku' : 'Currently valid'],
        ['value' => $expiredCount, 'label' => $id ? 'Kedaluwarsa' : 'Expired', 'tone' => $expiredCount > 0 ? 'text-rose-700' : 'text-slate-500'],
    ];

    $proof = $id ? [
        ['Diakui secara nasional', 'Sertifikat diterbitkan melalui skema BNSP bersama LSP mitra, sehingga berlaku untuk audit pelanggan, tender, dan persyaratan regulasi.'],
        ['Diuji, bukan sekadar dilatih', 'Setiap nama di daftar ini melewati uji kompetensi bersama asesor — pada pekerjaan dan peralatan yang benar-benar mereka tangani.'],
        ['Dapat ditelusuri siapa pun', 'Nomor sertifikat dan nomor uji kompetensi terbuka di halaman ini, jadi pemberi kerja bisa memverifikasinya sendiri.'],
    ] : [
        ['Nationally recognised', 'Certificates are issued under the BNSP scheme with our partner assessment bodies, so they hold up for customer audits, tenders, and regulatory requirements.'],
        ['Assessed, not just trained', 'Every name here passed an assessment with a certified assessor — on the work and equipment they actually handle.'],
        ['Open to verification', 'Certificate and assessment numbers are published on this page, so any employer can check them directly.'],
    ];
@endphp

<x-layout :title="$id ? 'Daftar Pemegang Sertifikat' : 'Certificate Holders'">
    <x-page-header
        :title="$id ? 'Pemegang Sertifikat' : 'Certificate Holders'"
        :subtitle="$id ? 'Bukti nyata kompetensi — para profesional yang telah lulus sertifikasi resmi bersama kami.' : 'Real proof of competency — professionals who have earned official certification with us.'"
        placement="certificate"
        image="photo-1524178232363-1fb2b075b655" />

    {{-- ===================== PENCARIAN =====================
         Halaman ini dipakai orang untuk satu hal: memeriksa apakah sebuah
         sertifikat benar ada dan masih berlaku. Jadi pencarian yang dulu
         terselip sebagai baris alat kecil kini memimpin halaman, ditemani
         angka nyata yang dihitung dari data — bukan klaim.

         Permukaannya sedikit turun: ini area alat, bukan isi. Kolom pencarian
         yang putih jadi menonjol di atasnya tanpa perlu isian warna. --}}
    <section class="section-sm border-b border-navy-50 bg-neutral-50">
        <div class="container grid gap-10 lg:grid-cols-12 lg:items-end lg:gap-16">
            <div class="lg:col-span-7">
                {{-- font-display dan text-balance tidak ditulis di sini: app.css
                     sudah memberikannya ke seluruh h1–h4. --}}
                <h2 class="text-3xl leading-tight text-navy md:text-4xl">
                    {{ $id ? 'Periksa keabsahan sebuah sertifikat' : 'Check whether a certificate is genuine' }}
                </h2>
                <span class="mt-5 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft" aria-hidden="true"></span>
                <p class="mt-5 max-w-xl text-pretty leading-relaxed text-slate-600">
                    {{ $id
                        ? 'Masukkan nama peserta, nama perusahaan, atau nomor sertifikat. Hasilnya menunjukkan kualifikasi yang diuji beserta status keberlakuannya.'
                        : 'Enter a participant name, company name, or certificate number. Results show the qualification assessed and whether it is still valid.' }}
                </p>

                <form method="GET" action="{{ route('certificates.index') }}" class="mt-8">
                    <label for="cert-q" class="sr-only">{{ $id ? 'Kata kunci pencarian sertifikat' : 'Certificate search keyword' }}</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            {{-- Tanpa focus:outline-none / ring sendiri: app.css sudah punya
                                 :focus-visible bercincin sky-500 penuh. Cincin /30 buatan saya
                                 hanya berkontras 1,3:1 dan justru menimpa yang lebih kuat. --}}
                            <input id="cert-q" type="search" name="q" value="{{ $q }}" autocomplete="off"
                                   placeholder="{{ $id ? 'mis. Ahmad Fauzi, PT Baja Perkasa, DTE/BNSP/…' : 'e.g. Ahmad Fauzi, PT Baja Perkasa, DTE/BNSP/…' }}"
                                   class="w-full rounded-xl border border-navy-200 bg-white py-3.5 pl-11 pr-4 text-[15px] text-navy transition-colors duration-200 placeholder:text-slate-500 focus:border-sky-500">
                        </div>
                        <button type="submit" class="btn-blue shrink-0 justify-center !py-3.5 sm:!px-8">{{ $id ? 'Cari' : 'Search' }}</button>
                    </div>
                </form>

                @if ($q !== '')
                    <p class="mt-4 text-sm text-slate-600">
                        {{ $id ? 'Menampilkan hasil untuk' : 'Showing results for' }}
                        <span class="font-semibold text-navy">&ldquo;{{ $q }}&rdquo;</span>
                        <span class="mx-2 text-slate-400" aria-hidden="true">·</span>
                        {{-- Bukan .link-underline: kelas itu inline-flex tanpa garis saat diam,
                             jadi tautan di tengah kalimat tidak bisa membungkus dan hanya
                             dibedakan warna — 1,5:1 terhadap teks sekitarnya. --}}
                        <a href="{{ route('certificates.index') }}" class="font-medium text-sky-700 underline underline-offset-4 transition-colors hover:text-navy">{{ $id ? 'tampilkan semua' : 'show all' }}</a>
                    </p>
                @endif
            </div>

            {{-- Angka dipisah garis tipis, bukan dikotakkan jadi kartu — tiga kotak
                 berjajar justru menyaingi perhatian dari kolom pencarian. Angkanya
                 sengaja besar: inilah bukti yang dibawa halaman ini. --}}
            {{-- divide-navy-200, bukan -100: garisnya adalah seluruh premis tata
                 letak ini, dan navy-100 di atas neutral-50 hanya 1,2:1. --}}
            <dl class="divide-y divide-navy-200 border-t border-navy-200 lg:col-span-5 lg:border-t-0">
                @foreach ($figures as $figure)
                    <div class="flex items-baseline justify-between gap-6 py-4 lg:py-5">
                        <dt class="text-sm text-slate-600">{{ $figure['label'] }}</dt>
                        <dd class="font-display text-3xl leading-none tabular-nums {{ $figure['tone'] ?? 'text-navy' }} md:text-4xl">{{ $figure['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- ===================== HASIL ===================== --}}
    <section class="section-sm bg-white">
        <div class="container">

            @if ($certificates->isEmpty())
                {{-- Satu keadaan kosong untuk kedua tata letak. Kalau ditaruh di
                     masing-masing @empty, colspan tabel harus ikut dijaga setiap
                     kali jumlah kolomnya berubah. --}}
                <x-certificate-empty :searching="$q !== ''" />
            @else

            {{-- Mobile: daftar kartu — tabel selebar layar memaksa geser ke samping. --}}
            <div class="space-y-3 md:hidden">
                @foreach ($certificates as $c)
                    <article class="rounded-2xl border border-navy-100 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate font-display font-semibold text-navy">{{ $c->participant_name }}</h3>
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
            <div class="hidden overflow-hidden rounded-2xl border border-navy-100 md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] text-left text-sm">
                        <caption class="sr-only">{{ $id ? 'Daftar pemegang sertifikat kompetensi' : 'List of competency certificate holders' }}</caption>
                        <thead>
                            <tr class="border-b border-navy-100 bg-neutral-50 text-navy">
                                <th scope="col" class="px-5 py-3.5 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'No. UJK' : 'Reg. No.' }}</th>
                                <th scope="col" class="px-5 py-3.5 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Peserta' : 'Participant' }}</th>
                                <th scope="col" class="px-5 py-3.5 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Nama Perusahaan' : 'Company' }}</th>
                                <th scope="col" class="px-5 py-3.5 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'No. Sertifikat' : 'Certificate No.' }}</th>
                                <th scope="col" class="px-5 py-3.5 font-mono text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Kualifikasi' : 'Qualification' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-50">
                            @foreach ($certificates as $c)
                                <tr class="transition-colors duration-150 hover:bg-neutral-50/70">
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-slate-500">{{ $c->ujk_number ?: '—' }}</td>
                                    {{-- Status menempel pada nama, sama seperti kartu mobile.
                                         Kolom Tgl. Berakhir dihapus, tetapi penanda berlaku /
                                         kedaluwarsa tetap perlu ada: tanpanya halaman ini
                                         berhenti menjadi alat verifikasi. --}}
                                    <td class="px-5 py-4">
                                        <span class="font-medium text-navy">{{ $c->participant_name }}</span>
                                        <x-certificate-status :expires-at="$c->expires_at" class="ml-2 align-middle" />
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">{{ $c->company_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-600">{{ $c->certificate_number ?: '—' }}</td>
                                    <td class="px-5 py-4">
                                        @if ($c->qualification)
                                            <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">{{ $c->qualification }}</span>
                                        @else — @endif
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
                <h2 class="font-display text-2xl text-navy text-balance md:text-3xl">
                    {{ $id ? 'Kenapa daftar ini kami buka' : 'Why we publish this list' }}
                </h2>
                <span class="mt-4 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft" aria-hidden="true"></span>

                <dl class="mt-8 divide-y divide-navy-100 border-t border-navy-100">
                    @foreach ($proof as [$title, $body])
                        <div class="py-5">
                            <dt class="font-display text-lg text-navy">{{ $title }}</dt>
                            <dd class="mt-1.5 max-w-[65ch] text-pretty text-sm leading-relaxed text-slate-600">{{ $body }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Bukan panel berwarna: hanya blok teks yang menempel di kolomnya,
                 dipisahkan garis rambut. Isian gelap di sini akan menyaingi
                 daftar sertifikatnya sendiri, padahal daftar itulah isi halaman. --}}
            {{-- Garis kolom ada di pembungkus yang meregang setinggi baris, bukan
                 di elemen sticky — kalau ditempel di yang sticky, garisnya ikut
                 melorot bersama bloknya alih-alih membatasi kolom. --}}
            <div class="lg:col-span-5 lg:border-l lg:border-navy-200 lg:pl-10">
                <div class="border-t border-navy-200 pt-8 lg:sticky lg:top-28 lg:border-t-0 lg:pt-0">
                    <p class="font-display text-xl leading-snug text-navy text-balance md:text-2xl">
                        {{ $id ? 'Ingin nama Anda ada di daftar ini?' : 'Want your name on this list?' }}
                    </p>
                    <p class="mt-3 max-w-[65ch] text-sm leading-relaxed text-slate-600">
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
