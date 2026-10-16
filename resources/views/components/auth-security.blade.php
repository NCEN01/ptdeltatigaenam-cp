{{--
    Keterangan keamanan di halaman Masuk dan Daftar.

    Isinya dibatasi pada hal yang benar-benar berjalan di aplikasi ini dan bisa
    ditunjuk berkasnya, bukan kalimat meyakinkan yang tidak ada belakangnya:

      - hash satu arah  → casts() di app/Models/Customer.php menandai kolom
                          password sebagai 'hashed' (bcrypt), dan $hidden
                          membuatnya tidak pernah ikut terkirim
      - HTTPS/TLS+HSTS  → app/Http/Middleware/AddSecurityHeaders.php
      - token CSRF      → @csrf di tiap formulir + validateCsrfTokens()
                          di bootstrap/app.php
      - pembatasan coba → middleware throttle:5,1 pada rute masuk, daftar, dan
                          lupa kata sandi di routes/web.php

    Satu catatan soal istilah: yang mengamankan lalu lintas sebuah situs adalah
    TLS (HTTPS), bukan SSH. SSH memang terenkripsi, tetapi protokol itu untuk
    masuk ke peladen dari jarak jauh dan tidak pernah dipakai peramban untuk
    membuka halaman. Jadi yang disebut di sini TLS — klaim yang keliru soal
    keamanan justru hal pertama yang akan dipersoalkan kalau situs ini diaudit.
--}}
@props(['heading' => null])

@php
    $isId = app()->getLocale() === 'id';

    $points = $isId ? [
        ['Kata sandi tidak tersimpan apa adanya', 'Yang disimpan hanya sidik hash bcrypt satu arah. Isinya tidak bisa dikembalikan menjadi kata sandi Anda, termasuk oleh kami sendiri.'],
        ['Sambungannya terenkripsi TLS', 'Data berjalan lewat HTTPS, dan peramban dilarang menurunkannya ke sambungan biasa oleh header HSTS.'],
        ['Setiap kiriman formulir bertanda', 'Token CSRF per sesi menolak kiriman yang berasal dari situs lain yang menyamar atas nama Anda.'],
        ['Percobaan masuk dibatasi', 'Maksimal lima percobaan per menit, sehingga penebakan kata sandi secara beruntun tidak berjalan.'],
    ] : [
        ['Passwords are never stored as typed', 'Only a one-way bcrypt hash is kept. It cannot be turned back into your password, not even by us.'],
        ['The connection is TLS-encrypted', 'Data travels over HTTPS, and the HSTS header stops browsers from downgrading to a plain connection.'],
        ['Every form submission is signed', 'A per-session CSRF token rejects submissions sent from another site impersonating you.'],
        ['Sign-in attempts are rate-limited', 'Five attempts per minute at most, so repeated password guessing does not get anywhere.'],
    ];
@endphp

{{-- Diletakkan di bawah formulir, bukan di atasnya: yang sampai ke halaman ini
     datang untuk mengisi formulirnya, dan empat baris keterangan di atas kolom
     pertama hanya menunda pekerjaan itu. Yang membacanya adalah orang yang
     ragu-ragu sebelum menekan tombol — dan tombol itu ada di bawah. --}}
<section {{ $attributes->class(['auth-anim mt-8 rounded-2xl border border-navy-100 bg-navy-50/60 p-5 sm:p-6']) }}
         aria-labelledby="auth-security-heading">
    <div class="flex items-start gap-3">
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-navy-200 bg-white text-navy-600">
            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 3l7 3v5.5c0 4.5-3 8-7 9.5-4-1.5-7-5-7-9.5V6z"/><path d="M9 12l2 2 4-4"/>
            </svg>
        </span>
        <div>
            <h2 id="auth-security-heading" class="font-display text-[15px] text-navy">
                {{ $heading ?? ($isId ? 'Data Anda aman di sini' : 'Your data is safe here') }}
            </h2>
            <p class="mt-1 text-sm leading-relaxed text-slate-600">
                {{ $isId
                    ? 'Empat hal berikut sudah berjalan di halaman ini, bukan rencana.'
                    : 'All four of these are already running on this page, not planned.' }}
            </p>
        </div>
    </div>

    {{-- dl, bukan ul: tiap baris memang pasangan "nama perlindungan" dan
         "cara kerjanya", dan daftar deskripsi menyampaikan hubungan itu ke
         pembaca layar tanpa perlu diberi tambahan apa pun. --}}
    <dl class="mt-5 grid gap-x-6 gap-y-4 sm:grid-cols-2">
        @foreach ($points as [$title, $detail])
            <div>
                <dt class="flex items-start gap-2 text-sm font-medium text-navy">
                    <svg class="mt-[3px] h-3.5 w-3.5 shrink-0 text-navy-600" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 10.5l4 4 8-9"/>
                    </svg>
                    {{ $title }}
                </dt>
                <dd class="mt-1 pl-[22px] text-xs leading-relaxed text-slate-600">{{ $detail }}</dd>
            </div>
        @endforeach
    </dl>

    <p class="mt-5 border-t border-navy-200 pt-4 text-xs leading-relaxed text-slate-600">
        {{ $isId ? 'Data yang Anda isi dipakai sebatas untuk melayani pesanan Anda, sesuai ' : 'Your details are used only to serve your orders, in line with our ' }}<a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="link-underline font-medium">{{ $isId ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a>{{ $isId ? ' dan Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi. Tidak dijual dan tidak dibagikan untuk iklan.' : ' and Indonesia’s Personal Data Protection Act (Law 27/2022). Never sold, never shared for advertising.' }}
    </p>
</section>
