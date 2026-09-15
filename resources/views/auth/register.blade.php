@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Daftar' : 'Sign up'"
    :heading="$id ? 'Buat akun' : 'Create your account'"
    :subheading="$id ? 'Cukup sekali isi. Data ini yang nanti terpakai saat Anda memesan layanan.' : 'Fill this in once. These details carry over every time you book a service.'">

    <form method="POST" action="{{ route('register') }}" class="auth-form space-y-4">
        @csrf
        <div style="display:none !important" aria-hidden="true">
            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
        </div>
        {{-- Enam kolom disusun bertiga berpasangan, bukan berderet ke bawah satu
             per satu: formulirnya turun dari lima baris menjadi tiga, setinggi
             halaman Masuk. Pasangannya mengikuti isi, bukan sekadar mengisi
             ruang — jati diri, kontak, lalu kata sandi.

             Berpasangan baru mulai di sm. Di bawah 640px satu kolom tinggal
             sekitar 140px, terlalu sempit untuk mengetik email atau nama, jadi
             di layar kecil keenamnya tetap menumpuk selebar penuh. --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="name" :label="__('site.contact.name')" required />
            <x-field name="email" type="email" :label="__('site.contact.email')" required />
        </div>

        {{-- Keduanya opsional, dan itu perlu dikatakan: tanpa keterangan, kolom
             perusahaan terbaca seperti syarat yang menutup pintu bagi peserta
             perorangan. --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="phone" :label="__('site.contact.phone')" :placeholder="$id ? 'Opsional' : 'Optional'" />
            <x-field name="company" :label="$id ? 'Perusahaan' : 'Company'" :placeholder="$id ? 'Opsional' : 'Optional'" />
        </div>

        {{-- "Ulangi", bukan "Konfirmasi Kata Sandi": label sepanjang itu pecah
             dua baris di kolom separuh lebar dan membuat pasangannya jadi tidak
             sama tinggi. --}}
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="password" type="password" :label="$id ? 'Kata Sandi' : 'Password'" autocomplete="new-password" required />
            <x-field name="password_confirmation" type="password" :label="$id ? 'Ulangi Kata Sandi' : 'Repeat Password'" autocomplete="new-password" required />
        </div>

        <button type="submit" class="btn-blue w-full">{{ $id ? 'Buat Akun' : 'Create account' }}</button>
    </form>

    <p class="auth-anim mt-8 border-t border-navy-100 pt-6 text-center text-sm text-slate-600 [animation-delay:480ms]">
        {{ $id ? 'Sudah punya akun?' : 'Already have an account?' }}
        <a href="{{ route('login') }}" class="font-medium link-underline">{{ __('site.nav.login') }}</a>
    </p>
</x-auth-layout>
