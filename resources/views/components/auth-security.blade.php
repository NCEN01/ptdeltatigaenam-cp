{{--
    Satu baris keterangan keamanan di halaman Masuk dan Daftar.

    Sebelumnya empat butir berikut judul dan paragraf penutup — lebih panjang
    daripada formulir yang dijaganya. Ironinya, panjang itu justru melemahkan
    maksudnya: orang yang ragu menekan "Buat Akun" tidak berhenti untuk membaca
    empat paragraf tentang bcrypt dan HSTS, dan dinding teks di bawah tombol
    kirim terbaca sebagai syarat dan ketentuan, bukan sebagai jaminan.

    Yang tersisa hanya yang bisa dinilai pembaca dalam sekali lihat. Istilahnya
    tetap jujur: yang mengamankan lalu lintas sebuah situs adalah TLS, bukan
    SSH — SSH memang terenkripsi, tetapi protokol itu untuk masuk ke peladen
    dari jarak jauh dan tidak pernah dipakai peramban membuka halaman.

    Keempat hal yang dulu disebut satu per satu tetap berjalan, hanya tidak
    lagi diceritakan di sini:
      - hash bcrypt satu arah → casts() di app/Models/Customer.php
      - TLS + HSTS            → app/Http/Middleware/AddSecurityHeaders.php
      - token CSRF            → @csrf + validateCsrfTokens() di bootstrap/app.php
      - lima percobaan/menit  → middleware throttle:5,1 di routes/web.php
--}}
@php
    $isId = app()->getLocale() === 'id';
@endphp

<p {{ $attributes->class(['auth-anim mt-6 flex items-start gap-2.5 text-xs leading-relaxed text-slate-500']) }}>
    <svg class="mt-px h-4 w-4 shrink-0 text-navy-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 3l7 3v5.5c0 4.5-3 8-7 9.5-4-1.5-7-5-7-9.5V6z"/><path d="M9 12l2 2 4-4"/>
    </svg>
    <span>{{ $isId
        ? 'Sambungan terenkripsi TLS, kata sandi disimpan sebagai hash satu arah, dan data Anda tidak dibagikan untuk iklan. '
        : 'TLS-encrypted connection, passwords stored as one-way hashes, and your data is never shared for advertising. ' }}<a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="link-underline font-medium">{{ $isId ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a></span>
</p>
