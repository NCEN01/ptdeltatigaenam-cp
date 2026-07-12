@php
    $id = app()->getLocale() === 'id';

    $trust = $id ? [
        ['t' => 'Diakui Secara Nasional', 'd' => 'Sertifikat kompetensi resmi yang diakui secara nasional bersama LSP mitra kami.', 'i' => 'shield'],
        ['t' => 'Sesuai Standar Industri', 'd' => 'Diuji mengikuti standar kompetensi kerja yang berlaku di industri.', 'i' => 'check'],
        ['t' => 'Karier Lebih Kompetitif', 'd' => 'Meningkatkan kredibilitas, daya saing, dan peluang karier profesional.', 'i' => 'trend'],
    ] : [
        ['t' => 'Nationally Recognized', 'd' => 'Official competency certificates recognized nationally with our partner LSPs.', 'i' => 'shield'],
        ['t' => 'Industry Standards', 'd' => 'Assessed against prevailing national work competency standards.', 'i' => 'check'],
        ['t' => 'A Competitive Edge', 'd' => 'Boosts credibility, competitiveness, and professional opportunities.', 'i' => 'trend'],
    ];
@endphp

<x-layout :title="$id ? 'Daftar Pemegang Sertifikat' : 'Certificate Holders'">
    <x-page-header
        :eyebrow="'PT Delta Tiga Enam'"
        :title="$id ? 'Pemegang Sertifikat' : 'Certificate Holders'"
        :subtitle="$id ? 'Bukti nyata kompetensi — para profesional yang telah lulus sertifikasi resmi bersama kami.' : 'Real proof of competency — professionals who have earned official certification with us.'"
        placement="certificate"
        image="photo-1524178232363-1fb2b075b655" />

    {{-- ===================== SELLING INTRO + TRUST ===================== --}}
    <section class="section-sm bg-white">
        <div class="container">
            <div class="max-w-3xl" data-aos="fade-up">
                <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $id ? 'Kompetensi Teruji' : 'Proven Competency' }}</p>
                <h2 class="font-display text-3xl text-navy text-balance md:text-4xl">{{ $id ? 'Setiap nama, sebuah bukti kompetensi.' : 'Every name, a proof of competency.' }}</h2>
                <p class="mt-5 max-w-2xl text-pretty leading-relaxed text-slate-600">
                    {{ $id
                        ? 'Daftar di bawah ini memuat para profesional yang telah menyelesaikan program sertifikasi kompetensi resmi bersama PT Delta Tiga Enam — diakui secara nasional dan siap bersaing di industri. Bergabunglah bersama ratusan talenta terbaik yang telah mempercayakan pengembangan kompetensinya kepada kami.'
                        : 'The list below features professionals who have completed official competency certification programs with PT Delta Tiga Enam — nationally recognized and industry-ready. Join hundreds of top talents who have trusted us to develop their competencies.' }}
                </p>
            </div>

            <div class="mt-10 grid gap-6 sm:grid-cols-3" data-aos="fade-up" data-aos-delay="80">
                @foreach ($trust as $item)
                    <div class="group rounded-2xl border border-navy-100 bg-white p-6 shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-sky-400 text-white transition-colors duration-300 group-hover:text-gold">
                            @if ($item['i'] === 'shield')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 3l7 3v6c0 4.4-3 7.6-7 9-4-1.4-7-4.6-7-9V6l7-3z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @elseif ($item['i'] === 'check')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 15a4 4 0 100-8 4 4 0 000 8z" stroke="currentColor" stroke-width="1.5"/><path d="M9 14l-1 7 4-2 4 2-1-7" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M3 17l6-6 4 4 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 8h5v5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </span>
                        <h3 class="mt-5 font-display text-lg text-navy">{{ $item['t'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $item['d'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== DIRECTORY (SEARCH + TABLE + PAGINATION) ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-neutral-50">
        <div class="container">
            {{-- Toolbar: count + search --}}
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4 text-sm text-slate-600" data-aos="fade-up">
                    <span>
                        <span class="font-display text-2xl text-gold-deep">{{ $certificates->total() }}</span>
                        {{ $id ? 'pemegang sertifikat' : 'certificate holders' }}
                    </span>
                    @if ($q !== '')
                        <a href="{{ route('certificates.index') }}" class="link-underline font-medium">{{ $id ? 'Bersihkan filter' : 'Clear filters' }}</a>
                    @endif
                </div>

                <form method="GET" action="{{ route('certificates.index') }}" class="flex w-full items-center gap-2 md:max-w-md" data-aos="fade-up">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <input type="text" name="q" value="{{ $q }}" placeholder="{{ $id ? 'Cari peserta, perusahaan, no. sertifikat…' : 'Search participant, company, cert no…' }}"
                               class="w-full rounded-full border border-navy-200 bg-white py-2.5 pl-10 pr-4 text-sm text-navy placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <button type="submit" class="btn-blue shrink-0 !px-6 !py-2.5">{{ $id ? 'Cari' : 'Search' }}</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-left text-sm">
                        <thead>
                            <tr class="bg-sky-400 text-white">
                                <th class="px-5 py-4 font-mono text-[11px] font-medium uppercase tracking-wider">{{ $id ? 'No. UJK' : 'Reg. No.' }}</th>
                                <th class="px-5 py-4 font-mono text-[11px] font-medium uppercase tracking-wider">{{ $id ? 'Peserta' : 'Participant' }}</th>
                                <th class="px-5 py-4 font-mono text-[11px] font-medium uppercase tracking-wider">{{ $id ? 'Nama Perusahaan' : 'Company' }}</th>
                                <th class="px-5 py-4 font-mono text-[11px] font-medium uppercase tracking-wider">{{ $id ? 'No. Sertifikat' : 'Certificate No.' }}</th>
                                <th class="px-5 py-4 font-mono text-[11px] font-medium uppercase tracking-wider">{{ $id ? 'Kualifikasi' : 'Qualification' }}</th>
                                <th class="px-5 py-4 font-mono text-[11px] font-medium uppercase tracking-wider">{{ $id ? 'Tgl. Berakhir' : 'Expiry Date' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-100 bg-white">
                            @forelse ($certificates as $c)
                                <tr class="transition-colors duration-150 hover:bg-neutral-50">
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-slate-500">{{ $c->ujk_number ?: '—' }}</td>
                                    <td class="px-5 py-4 font-medium text-navy">{{ $c->participant_name }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $c->company_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-600">{{ $c->certificate_number ?: '—' }}</td>
                                    <td class="px-5 py-4">
                                        @if ($c->qualification)
                                            <span class="inline-flex rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">{{ $c->qualification }}</span>
                                        @else — @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ $c->expires_at?->translatedFormat('d M Y') ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-sky-400 text-white">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M12 15a4 4 0 100-8 4 4 0 000 8z" stroke="currentColor" stroke-width="1.4"/><path d="M9 14l-1 7 4-2 4 2-1-7" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                                        </span>
                                        <p class="mt-4 font-display text-lg text-navy">{{ $id ? 'Data tidak ditemukan' : 'No records found' }}</p>
                                        <p class="mt-1 text-sm text-slate-600">{{ $q !== '' ? ($id ? 'Coba kata kunci lain atau bersihkan filter.' : 'Try another keyword or clear the filter.') : ($id ? 'Data pemegang sertifikat akan tampil di sini.' : 'Certificate holder records will appear here.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($certificates->hasPages())
                <div class="mt-8">{{ $certificates->links() }}</div>
            @endif

            {{-- Inline CTA — the "selling" nudge --}}
            <div class="mt-10 flex flex-col items-center justify-between gap-5 rounded-3xl border border-navy-100 bg-white p-7 shadow-card md:flex-row md:p-8" data-aos="fade-up">
                <div>
                    <p class="font-display text-xl text-navy md:text-2xl">{{ $id ? 'Ingin nama Anda ada di sini?' : 'Want your name on this list?' }}</p>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $id ? 'Ikuti program sertifikasi kompetensi resmi bersama PT Delta Tiga Enam.' : 'Join an official competency certification program with PT Delta Tiga Enam.' }}</p>
                </div>
                <div class="flex shrink-0 gap-3">
                    <a href="{{ route('services.index') }}" class="btn-blue">{{ $id ? 'Lihat Program' : 'View Programs' }}</a>
                    <a href="{{ route('contact.index') }}" class="btn-ghost">{{ $id ? 'Konsultasi' : 'Consult' }}</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
