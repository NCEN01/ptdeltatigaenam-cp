@php
    $id = app()->getLocale() === 'id';
    $customer = auth('customer')->user();

    // Satu definisi untuk kedua tautan, supaya keduanya tidak bisa menyimpang
    // saat salah satunya disunting.
    $link = fn (bool $active) => $active
        ? 'bg-navy-600 text-white shadow-[0_8px_20px_-10px_rgba(32,95,159,0.7)]'
        : 'text-slate-700 hover:bg-navy-50 hover:text-navy';
@endphp

<nav class="lg:sticky lg:top-28" aria-label="{{ $id ? 'Navigasi akun' : 'Account' }}">
    {{-- Kartu identitas di atas menunya. Halaman akun dibuka justru untuk
         memeriksa "ini akun yang mana", dan sebelumnya jawabannya hanya ada di
         judul halaman Profil — begitu pindah ke Riwayat Pesanan, keterangan itu
         hilang. --}}
    @if ($customer)
        <div class="card mb-4 flex items-center gap-3 p-4">
            {{-- Huruf depan nama, bukan foto: unggahan foto profil belum ada
                 alurnya, dan lingkaran kosong terbaca sebagai gambar yang gagal
                 dimuat. --}}
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-navy-600 font-display text-base font-bold text-white" aria-hidden="true">
                {{ mb_strtoupper(mb_substr(trim($customer->name), 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="truncate font-display text-sm font-semibold leading-tight text-navy">{{ $customer->name }}</p>
                {{-- Perusahaan bila ada, surel bila tidak. Bagi pemesan korporat
                     nama perusahaannya yang membedakan satu akun dari akun
                     rekannya, bukan surelnya. --}}
                <p class="mt-0.5 truncate text-xs text-slate-500">{{ $customer->company ?: $customer->email }}</p>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-1">
        <a href="{{ route('account.profile') }}"
           @if (request()->routeIs('account.profile')) aria-current="page" @endif
           class="rounded-2xl px-4 py-3 text-sm font-medium transition-colors duration-200 {{ $link(request()->routeIs('account.profile')) }}">
            {{ $id ? 'Profil' : 'Profile' }}
        </a>
        <a href="{{ route('account.orders') }}"
           @if (request()->routeIs('account.orders')) aria-current="page" @endif
           class="rounded-2xl px-4 py-3 text-sm font-medium transition-colors duration-200 {{ $link(request()->routeIs('account.orders')) }}">
            {{ $id ? 'Riwayat Pesanan' : 'Order History' }}
        </a>

        {{-- Keluar dipisah garis, bukan sekadar diberi warna merah. Ia bukan
             halaman ketiga melainkan tindakan, dan garis itu yang menyatakannya
             — merah saja membuatnya terbaca sebagai peringatan bahaya, padahal
             keluar dari akun tidak menghapus apa pun.

             Warnanya ikut slate seperti tautan lain saat diam, dan baru memerah
             saat disentuh: niatnya terbaca tepat ketika kursor sudah di atasnya,
             bukan sepanjang waktu. --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-navy-100 pt-2">
            @csrf
            <button type="submit"
                    class="group flex w-full items-center gap-2.5 rounded-2xl px-4 py-3 text-left text-sm font-medium text-slate-700 transition-colors duration-200 hover:bg-rose-50 hover:text-rose-700">
                <svg class="h-4 w-4 shrink-0 text-slate-400 transition-colors duration-200 group-hover:text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>
                </svg>
                {{ $id ? 'Keluar' : 'Sign out' }}
            </button>
        </form>
    </div>
</nav>
