@props(['title' => '', 'heading' => '', 'subheading' => null])

@php
    use App\Support\Locale;

    $isId = app()->getLocale() === 'id';

    // Apa yang sebenarnya bisa dilakukan sebuah akun di sini, bukan angka
    // pencapaian perusahaan. Pengunjung yang sudah sampai ke halaman ini tidak
    // sedang menimbang kredibilitas; ia sedang menimbang perlu-tidaknya mendaftar.
    // Ketiganya merujuk fitur yang memang ada: profil tersimpan, riwayat pesanan
    // berikut statusnya, dan pembayaran yang bisa dilanjutkan.
    $perks = $isId ? [
        'Pesan layanan tanpa mengetik ulang data perusahaan',
        'Pantau pesanan Anda, dari menunggu sampai lunas',
        'Lanjutkan pembayaran yang tertunda kapan saja',
    ] : [
        'Book services without retyping your company details',
        'Track every order, from pending through paid',
        'Pick up an unfinished payment whenever you like',
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ Locale::current() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2b83df">
    <title>{{ $title }} — PT Delta Tiga Enam</title>
    <link rel="icon" href="{{ asset('images/logodelta36.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/logodelta36.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-white text-ink antialiased">
    <div class="grid min-h-dvh lg:grid-cols-2">
        {{-- Panel brand. Susunan lapisannya disamakan dengan pita gelap lain di
             situs (foto, tirai navy, gradasi, butir, satu cahaya samar). Sebelumnya
             foto beropasitas 40% ditumpuk di atas bg-navy-anim lalu ditutup aurora,
             empat lapisan bergerak yang saling melawan sehingga fotonya keruh. --}}
        <div class="relative hidden overflow-hidden bg-navy-950 p-12 text-white lg:flex lg:flex-col lg:justify-between">
            <img src="https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1400&q=80"
                 alt="" loading="lazy" class="pointer-events-none absolute inset-0 h-full w-full object-cover">
            <div class="pointer-events-none absolute inset-0 bg-navy-950/82"></div>
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-navy-950/70 via-navy-950/45 to-navy-950/92"></div>
            <div class="pointer-events-none absolute inset-0 grain opacity-25"></div>
            <div class="pointer-events-none absolute -right-24 top-1/4 h-80 w-80 rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/50 to-transparent"></div>

            <a href="{{ route('home') }}" class="auth-anim relative flex items-center gap-3 rounded-xl">
                <img src="{{ asset('images/logodelta36.png') }}" alt="" class="h-11 w-11 shrink-0">
                <span class="font-display text-lg">PT Delta Tiga Enam</span>
            </a>

            <div class="relative">
                {{-- Baris "Human Capital · Training · Certification" dihapus, bukan
                     diganti: kelas .eyebrow bernilai display:none !important di
                     seluruh situs, jadi kalimat itu tidak pernah sekali pun tampil. --}}
                <p class="auth-anim max-w-md text-[2rem] leading-[1.14] text-balance [animation-delay:120ms]">
                    {{ $isId
                        ? 'Satu akun untuk memesan pelatihan, sertifikasi, dan layanan lainnya.'
                        : 'One account for booking training, certification, and every other service.' }}
                </p>

                <ul class="mt-9 space-y-4">
                    {{-- Jedanya lewat style, bukan kelas [animation-delay:..]: Tailwind
                         memindai berkas sumber apa adanya, sehingga kelas yang nilainya
                         baru terbentuk saat render tidak pernah ikut dibuat. --}}
                    @foreach ($perks as $i => $perk)
                        <li class="auth-anim flex items-start gap-3" style="animation-delay: {{ 200 + $i * 70 }}ms">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-sky-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M4 10.5l4 4 8-9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="text-[15px] leading-relaxed text-navy-100">{{ $perk }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="auth-anim relative font-mono text-xs text-navy-200 [animation-delay:440ms]">© {{ now()->year }} PT Delta Tiga Enam</p>
        </div>

        {{-- Form panel. Sisi dan atas-bawahnya lebih rapat di layar kecil: dengan
             py-12 dan px-6, formulir daftar yang enam kolom itu menyisakan jarak
             yang tidak perlu di ponsel. min-h-dvh dipasang di induknya, bukan
             h-dvh, jadi formulir yang lebih tinggi dari layar ikut memanjang
             alih-alih terpotong di atas oleh items-center. --}}
        <div class="flex items-center justify-center px-5 py-10 sm:px-12 sm:py-12">
            <div class="w-full max-w-md">
                {{-- Di layar kecil panel brand tidak ada sama sekali, jadi baris ini
                     satu-satunya tanda pengunjung masih berada di situs yang sama. --}}
                <a href="{{ route('home') }}" class="auth-anim mb-8 inline-flex items-center gap-2.5 rounded-xl text-sm font-medium text-navy transition-colors hover:text-sky-700 lg:hidden sm:mb-10">
                    <img src="{{ asset('images/logodelta36.png') }}" alt="" class="h-9 w-9">
                    PT Delta Tiga Enam
                </a>

                <h1 class="auth-anim font-display text-3xl font-semibold text-navy text-balance sm:text-4xl">{{ $heading }}</h1>
                @if ($subheading)<p class="auth-anim mt-3 leading-relaxed text-slate-600 text-pretty [animation-delay:80ms]">{{ $subheading }}</p>@endif

                @if (session('status'))
                    <div class="mt-7 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm leading-relaxed text-emerald-800" role="status" aria-live="polite">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M16.7 5.7a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 10a1 1 0 011.4-1.4l3.3 3.3 6.8-6.8a1 1 0 011.4 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mt-8">{{ $slot }}</div>
            </div>
        </div>
    </div>
</body>
</html>
