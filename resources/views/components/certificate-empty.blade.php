{{-- Sengaja menerima penanda, bukan kata kuncinya: komponen ini tidak pernah
     menampilkan kata kunci, hanya membedakan dua keadaan kosong. --}}
@props(['searching' => false])

@php
    $isId = app()->getLocale() === 'id';
@endphp

{{-- Dipakai tabel desktop dan daftar kartu mobile. Keadaan kosong karena
     pencarian tidak ketemu berbeda dari keadaan kosong karena datanya memang
     belum ada — yang pertama butuh jalan keluar, yang kedua tidak. --}}
<div class="py-12 text-center">
    <span class="mx-auto grid h-12 w-12 place-items-center rounded-full border border-navy-100 bg-white text-navy-200">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/>
            <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </span>

    <p class="mt-4 font-display text-lg text-navy">
        {{ $searching
            ? ($isId ? 'Tidak ada yang cocok' : 'Nothing matched')
            : ($isId ? 'Daftar masih kosong' : 'The list is still empty') }}
    </p>

    <p class="mx-auto mt-1.5 max-w-sm text-sm leading-relaxed text-slate-600">
        {{ $searching
            ? ($isId
                ? 'Coba potongan nama, nama perusahaan, atau sebagian nomor sertifikatnya saja.'
                : 'Try part of the name, the company, or just a fragment of the certificate number.')
            : ($isId
                ? 'Data pemegang sertifikat akan muncul di sini setelah uji kompetensi pertama selesai.'
                : 'Certificate holders will appear here once the first assessment is complete.') }}
    </p>

    @if ($searching)
        <a href="{{ route('certificates.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-sky-700 underline underline-offset-4 transition-colors hover:text-navy">
            {{ $isId ? 'Tampilkan seluruh daftar' : 'Show the whole list' }}
        </a>
    @endif
</div>
