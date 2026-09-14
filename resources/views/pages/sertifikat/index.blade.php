@php
    $id = app()->getLocale() === 'id';

    /* Angka ringkasan. Ditulis sebagai daftar supaya barisnya dirender satu kali,
       bukan tiga blok markup yang hampir sama. */
    $figures = [
        ['value' => $certificates->total(), 'label' => $id ? 'Terdaftar' : 'Registered', 'tone' => 'text-white'],
        ['value' => $validCount, 'label' => $id ? 'Masih berlaku' : 'Currently valid', 'tone' => 'text-sky-300'],
        ['value' => $expiredCount, 'label' => $id ? 'Kedaluwarsa' : 'Expired', 'tone' => 'text-navy-200'],
    ];

    /* Satu definisi untuk teks masa berlaku. Formatnya berbeda per tempat —
       kartu mobile punya ruang untuk nama bulan penuh, sel tabel tidak — tetapi
       teks penggantinya saat tanpa tanggal harus tetap sama. */
    $validUntil = fn ($cert, string $format) => $cert->expires_at?->translatedFormat($format)
        ?: ($id ? 'Tanpa batas waktu' : 'No expiry');

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
         angka nyata yang dihitung dari data — bukan klaim. --}}
    <section class="relative overflow-hidden bg-navy-950 py-14 text-white md:py-20">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-55"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>

        <div class="container relative grid gap-10 lg:grid-cols-12 lg:items-end lg:gap-14">
            <div class="lg:col-span-7">
                <h2 class="font-display text-3xl leading-tight text-balance md:text-4xl">
                    {{ $id ? 'Periksa keabsahan sebuah sertifikat' : 'Check whether a certificate is genuine' }}
                </h2>
                <p class="mt-4 max-w-xl text-pretty leading-relaxed text-navy-100">
                    {{ $id
                        ? 'Masukkan nama peserta, nama perusahaan, atau nomor sertifikat. Hasilnya menunjukkan kualifikasi yang diuji beserta masa berlakunya.'
                        : 'Enter a participant name, company name, or certificate number. Results show the qualification assessed and how long it remains valid.' }}
                </p>

                <form method="GET" action="{{ route('certificates.index') }}" class="mt-7">
                    <label for="cert-q" class="sr-only">{{ $id ? 'Kata kunci pencarian sertifikat' : 'Certificate search keyword' }}</label>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-navy-200" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                            <input id="cert-q" type="search" name="q" value="{{ $q }}" autocomplete="off"
                                   placeholder="{{ $id ? 'mis. Ahmad Fauzi, PT Baja Perkasa, DTE/BNSP/…' : 'e.g. Ahmad Fauzi, PT Baja Perkasa, DTE/BNSP/…' }}"
                                   class="w-full rounded-xl border border-white/15 bg-white/[0.07] py-3.5 pl-11 pr-4 text-[15px] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.08)] transition-colors duration-200 placeholder:text-navy-200/80 focus:border-sky-400 focus:bg-white/[0.12] focus:outline-none focus:ring-2 focus:ring-sky-400/60">
                        </div>
                        <button type="submit" class="btn-blue shrink-0 justify-center !py-3.5 sm:!px-8">{{ $id ? 'Cari' : 'Search' }}</button>
                    </div>
                </form>

                @if ($q !== '')
                    <p class="mt-4 text-sm text-navy-100">
                        {{ $id ? 'Menampilkan hasil untuk' : 'Showing results for' }}
                        <span class="font-semibold text-white">&ldquo;{{ $q }}&rdquo;</span>
                        <span class="mx-2 text-navy-200" aria-hidden="true">·</span>
                        <a href="{{ route('certificates.index') }}" class="font-medium text-sky-300 underline underline-offset-4 transition-colors hover:text-white">{{ $id ? 'tampilkan semua' : 'show all' }}</a>
                    </p>
                @endif
            </div>

            {{-- Angka dipisah garis tipis, bukan dikotakkan jadi kartu — tiga kotak
                 berjajar justru menyaingi perhatian dari kolom pencarian. --}}
            <dl class="divide-y divide-white/10 border-t border-white/10 lg:col-span-5 lg:border-t-0">
                @foreach ($figures as $figure)
                    <div class="flex items-baseline justify-between gap-6 py-3.5 lg:py-4">
                        <dt class="text-sm text-navy-100">{{ $figure['label'] }}</dt>
                        <dd class="font-display text-2xl tabular-nums {{ $figure['tone'] }} md:text-3xl">{{ $figure['value'] }}</dd>
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
                            <p class="mt-3 text-sm font-medium text-sky-800">{{ $c->qualification }}</p>
                        @endif

                        <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 border-t border-navy-50 pt-3 text-xs">
                            <div class="min-w-0">
                                <dt class="text-[11px] text-slate-400">{{ $id ? 'No. Sertifikat' : 'Certificate No.' }}</dt>
                                <dd class="truncate font-mono text-slate-700">{{ $c->certificate_number ?: '—' }}</dd>
                            </div>
                            <div class="min-w-0">
                                <dt class="text-[11px] text-slate-400">{{ $id ? 'No. UJK' : 'Reg. No.' }}</dt>
                                <dd class="truncate font-mono text-slate-500">{{ $c->ujk_number ?: '—' }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-[11px] text-slate-400">{{ $id ? 'Berlaku sampai' : 'Valid until' }}</dt>
                                <dd class="text-slate-700">{{ $validUntil($c, 'd F Y') }}</dd>
                            </div>
                        </dl>
                    </article>
                @endforeach
            </div>

            {{-- Tabel (tablet & desktop). Nama+perusahaan dan kedua nomor
                 digabung per sel: enam kolom sempit memaksa geser horizontal,
                 padahal keduanya memang satu kesatuan informasi. --}}
            <div class="hidden overflow-hidden rounded-2xl border border-navy-100 md:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left text-sm">
                        <caption class="sr-only">{{ $id ? 'Daftar pemegang sertifikat kompetensi' : 'List of competency certificate holders' }}</caption>
                        <thead>
                            <tr class="border-b border-navy-100 bg-neutral-50 text-navy">
                                <th scope="col" class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Peserta' : 'Participant' }}</th>
                                <th scope="col" class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Kualifikasi' : 'Qualification' }}</th>
                                <th scope="col" class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Nomor' : 'Numbers' }}</th>
                                <th scope="col" class="px-5 py-3.5 text-[11px] font-semibold uppercase tracking-wider">{{ $id ? 'Masa Berlaku' : 'Validity' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-50">
                            @foreach ($certificates as $c)
                                <tr class="transition-colors duration-150 hover:bg-neutral-50/70">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-navy">{{ $c->participant_name }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $c->company_name ?: '—' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">{{ $c->qualification ?: '—' }}</td>
                                    <td class="px-5 py-4">
                                        <p class="whitespace-nowrap font-mono text-xs text-slate-700">{{ $c->certificate_number ?: '—' }}</p>
                                        <p class="mt-0.5 whitespace-nowrap font-mono text-[11px] text-slate-400">{{ $c->ujk_number ?: '—' }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <x-certificate-status :expires-at="$c->expires_at" />
                                        <p class="mt-1.5 whitespace-nowrap text-xs text-slate-500">{{ $validUntil($c, 'd M Y') }}</p>
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

            <div class="lg:col-span-5">
                <div class="relative overflow-hidden rounded-2xl bg-navy-950 p-8 text-white lg:sticky lg:top-28">
                    <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                    <div class="relative">
                        <p class="font-display text-xl leading-snug text-balance md:text-2xl">
                            {{ $id ? 'Ingin nama Anda ada di daftar ini?' : 'Want your name on this list?' }}
                        </p>
                        <p class="mt-3 text-sm leading-relaxed text-navy-100">
                            {{ $id
                                ? 'Uji kompetensi dapat digelar di tempat kerja Anda, mengikuti pola sif, tanpa menghentikan produksi.'
                                : 'Assessment can run at your workplace, around your shift pattern, without stopping production.' }}
                        </p>
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row lg:flex-col">
                            <a href="{{ route('services.index') }}" class="btn-blue justify-center">{{ $id ? 'Lihat Program Sertifikasi' : 'View Certification Programs' }}</a>
                            <a href="{{ route('contact.index') }}" class="btn-ghost-light justify-center">{{ $id ? 'Konsultasi Gratis' : 'Free Consultation' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
