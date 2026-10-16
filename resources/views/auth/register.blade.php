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

        {{-- Persetujuan atas Kebijakan Privasi dan Syarat & Ketentuan. Dicentang
             sendiri oleh pendaftar, bukan dicentang lebih dulu: Undang-Undang
             Nomor 27 Tahun 2022 menuntut persetujuan yang diberikan secara tegas,
             dan kotak yang sudah tercentang bukan persetujuan yang diberikan.

             Kedua tautannya dibuka di tab baru supaya isian yang sudah diketik
             tidak hilang saat pendaftar memeriksa isinya. --}}
        <div class="pt-1">
            <label for="terms" class="flex cursor-pointer items-start gap-3 text-sm leading-relaxed text-slate-600">
                <input type="checkbox" name="terms" id="terms" value="1" required @checked(old('terms'))
                       class="mt-0.5 h-4 w-4 shrink-0 cursor-pointer rounded border-navy-200 text-navy-600 focus:ring-2 focus:ring-navy-600 focus:ring-offset-0">
                <span>
                    {{ $id ? 'Saya menyetujui ' : 'I agree to the ' }}<a href="{{ route('terms') }}" target="_blank" rel="noopener" class="font-medium text-sky-700 underline-offset-4 hover:underline">{{ $id ? 'Syarat & Ketentuan' : 'Terms & Conditions' }}</a>{{ $id ? ' dan ' : ' and the ' }}<a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="font-medium text-sky-700 underline-offset-4 hover:underline">{{ $id ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a>{{ $id ? '.' : '.' }}
                </span>
            </label>
            @error('terms')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-blue w-full">{{ $id ? 'Buat Akun' : 'Create account' }}</button>
    </form>

    <p class="auth-anim mt-8 border-t border-navy-100 pt-6 text-center text-sm text-slate-600 [animation-delay:480ms]">
        {{ $id ? 'Sudah punya akun?' : 'Already have an account?' }}
        <a href="{{ route('login') }}" class="font-medium link-underline">{{ __('site.nav.login') }}</a>
    </p>
</x-auth-layout>
