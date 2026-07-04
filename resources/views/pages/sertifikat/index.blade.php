@php $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="$id ? 'Daftar Pemegang Sertifikat' : 'Certificate Holders'">
    <x-page-header
        :eyebrow="$id ? 'Sertifikasi' : 'Certification'"
        :title="$id ? 'Daftar Pemegang Sertifikat' : 'Certificate Holders'"
        :subtitle="$id ? 'Peserta yang telah berhasil menyelesaikan sertifikasi & pelatihan bersama PT Delta Tiga Enam.' : 'Participants who have successfully completed certification & training with PT Delta Tiga Enam.'"
        image="photo-1524178232363-1fb2b075b655" />

    <section class="section">
        <div class="container">
            {{-- Toolbar: count + search --}}
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-4 text-sm text-navy-500" data-aos="fade-up">
                    <span>
                        <span class="font-display text-lg font-semibold text-navy">{{ $certificates->total() }}</span>
                        {{ $id ? 'pemegang sertifikat' : 'certificate holders' }}
                    </span>
                    @if ($q !== '')
                        <a href="{{ route('certificates.index') }}" class="link-underline font-medium">{{ $id ? 'Bersihkan filter' : 'Clear filters' }}</a>
                    @endif
                </div>

                <form method="GET" action="{{ route('certificates.index') }}" class="flex w-full items-center gap-2 md:max-w-md" data-aos="fade-up">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-navy-300" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <input type="text" name="q" value="{{ $q }}" placeholder="{{ $id ? 'Cari peserta, perusahaan, no. sertifikat…' : 'Search participant, company, cert no…' }}"
                               class="w-full rounded-full border border-navy-200 bg-white py-2.5 pl-10 pr-4 text-sm text-navy placeholder:text-navy-300 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                    <button type="submit" class="btn-blue shrink-0 !px-6 !py-2.5">{{ $id ? 'Cari' : 'Search' }}</button>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-hidden rounded-2xl border border-navy-100 shadow-card" data-aos="fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[920px] text-left text-sm">
                        <thead>
                            <tr class="bg-navy text-white">
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
                                <tr class="transition-colors duration-150 hover:bg-mist">
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-navy-400">{{ $c->ujk_number ?: '—' }}</td>
                                    <td class="px-5 py-4 font-medium text-navy">{{ $c->participant_name }}</td>
                                    <td class="px-5 py-4 text-navy-600">{{ $c->company_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-navy-500">{{ $c->certificate_number ?: '—' }}</td>
                                    <td class="px-5 py-4">
                                        @if ($c->qualification)
                                            <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">{{ $c->qualification }}</span>
                                        @else — @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-navy-600">{{ $c->expires_at?->translatedFormat('d M Y') ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <span class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-navy text-sky-400">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M12 15a4 4 0 100-8 4 4 0 000 8z" stroke="currentColor" stroke-width="1.4"/><path d="M9 14l-1 7 4-2 4 2-1-7" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                                        </span>
                                        <p class="mt-4 font-display text-lg font-semibold text-navy">{{ $id ? 'Data tidak ditemukan' : 'No records found' }}</p>
                                        <p class="mt-1 text-sm text-navy-500">{{ $q !== '' ? ($id ? 'Coba kata kunci lain atau bersihkan filter.' : 'Try another keyword or clear the filter.') : ($id ? 'Data pemegang sertifikat akan tampil di sini.' : 'Certificate holder records will appear here.') }}</p>
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
        </div>
    </section>
</x-layout>
