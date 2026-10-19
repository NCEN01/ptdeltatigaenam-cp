@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Daftar' : 'Sign up'"
    :heading="$id ? 'Buat akun' : 'Create your account'"
    :subheading="$id ? 'Cukup sekali isi. Data ini yang nanti terpakai saat Anda memesan layanan.' : 'Fill this in once. These details carry over every time you book a service.'">

    {{-- Sama seperti di halaman Masuk: tamu yang datang dari formulir Kemitraan
         perlu tahu isiannya tidak hilang. Di sini kalimatnya menyebut "membuat
         akun", karena yang membuka halaman ini memang belum punya. --}}
    @if (session()->has('partnership.draft'))
        <div class="auth-anim mb-6 flex items-start gap-3 rounded-2xl border border-navy-200 bg-navy-50 px-4 py-3.5 text-sm leading-relaxed text-navy-800" role="status">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-navy-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 118 0v3"/>
            </svg>
            <span>{{ $id
                ? 'Buat akun dulu untuk mengirim pendaftaran kemitraan Anda. Isian formulirnya sudah kami simpan dan akan dikembalikan setelah akun Anda siap.'
                : 'Create an account to submit your partnership registration. The details you filled in are saved and will be restored once your account is ready.' }}</span>
        </div>
    @endif

    <x-google-auth-button action="register" />

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
            {{-- Nomor telepon berkode negara, bawaan +62. Komponennya sendiri,
                 bukan <x-field>: kolom ini butuh dua kendali yang terlihat
                 (pemilih negara dan angka) yang digabung jadi satu nilai saat
                 dikirim, dan menyelipkan percabangan itu ke dalam <x-field>
                 akan membebani sepuluh kolom lain yang tidak membutuhkannya. --}}
            <x-phone-field name="phone" :label="__('site.contact.phone')" />
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
        {{-- data-field-box di pembungkusnya, bukan di label: form-validate.js
             menaruh kalimat peringatannya di dalam wadah ini, dan label di
             bawah ini sebuah flex row — satu paragraf yang disisipkan ke
             dalamnya akan berdiri di sebelah kanan kotak centang, bukan di
             bawahnya. Wadah ini juga tempat pesan @error('terms') dari
             peladen muncul, jadi keduanya sejajar. --}}
        {{-- Kotak centang bawaan peramban diganti kotak yang digambar sendiri.
             Yang bawaan tidak bisa diberi sudut membulat, tidak bisa diberi
             peralihan warna, dan centangnya langsung muncul utuh — di tengah
             formulir yang seluruh kolomnya rounded-2xl dengan peralihan
             halus, satu-satunya persegi bersudut tajam di halaman ini justru
             yang paling akhir harus disentuh sebelum tombol kirim.

             Input-nya tidak dihapus, hanya disembunyikan dengan sr-only: ia
             tetap kotak centang sungguhan yang bisa disinggahi papan tik,
             dibaca pembaca layar, dan dikirim bersama formulir. Yang digambar
             di sebelahnya hanya penampakannya, dan keadaannya mengikuti
             peer-checked milik input itu. --}}
        <div class="pt-1" data-field-box="terms">
            <label for="terms"
                   class="group flex cursor-pointer items-start gap-3.5 rounded-2xl border border-navy-100 bg-navy-50/40 px-4 py-3.5 text-sm leading-relaxed text-slate-600 transition-colors duration-300 ease-out-soft hover:border-navy-200 hover:bg-navy-50 has-[:checked]:border-navy-200 has-[:checked]:bg-navy-50">
                <input type="checkbox" name="terms" id="terms" value="1" required @checked(old('terms'))
                       class="peer sr-only">

                {{-- Centangnya digambar dengan stroke-dasharray: garisnya
                     tertarik dari pangkal ke ujung, bukan muncul sekaligus.
                     peer-checked ditujukan ke svg lewat [&>svg] karena svg ini
                     cucu input-nya, bukan saudara sekandung — peer-checked yang
                     ditulis di svg sendiri tidak akan pernah menyala. --}}
                {{-- data-checkbox-face dibaca app.css. Ketika form-validate.js
                     menandai input yang belum dicentang dengan .field-invalid,
                     penanda merahnya mendarat di input yang sr-only dan tidak
                     terlihat di mana pun — jadi warnanya harus diteruskan ke
                     kotak yang benar-benar tampil, yaitu span ini. --}}
                <span data-checkbox-face
                      class="mt-px grid h-[22px] w-[22px] shrink-0 place-items-center rounded-lg border-[1.5px] border-navy-200 bg-white shadow-sm transition-all duration-300 ease-out-soft group-hover:border-navy-400 peer-checked:border-navy-600 peer-checked:bg-navy-600 peer-checked:[&>svg]:[stroke-dashoffset:0] peer-focus-visible:ring-2 peer-focus-visible:ring-navy-600 peer-focus-visible:ring-offset-2"
                      aria-hidden="true">
                    <svg class="h-3 w-3 text-white [stroke-dasharray:18] [stroke-dashoffset:18] transition-[stroke-dashoffset] duration-300 ease-out-soft"
                         viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 10.5l4 4 8-9"/>
                    </svg>
                </span>

                <span>
                    {{ $id ? 'Saya menyetujui ' : 'I agree to the ' }}<a href="{{ route('terms') }}" target="_blank" rel="noopener" class="font-medium text-sky-700 underline-offset-4 hover:underline">{{ $id ? 'Syarat & Ketentuan' : 'Terms & Conditions' }}</a>{{ $id ? ' dan ' : ' and the ' }}<a href="{{ route('privacy') }}" target="_blank" rel="noopener" class="font-medium text-sky-700 underline-offset-4 hover:underline">{{ $id ? 'Kebijakan Privasi' : 'Privacy Policy' }}</a>{{ $id ? '.' : '.' }}
                </span>
            </label>
            @error('terms')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <x-recaptcha />

        <button type="submit" class="btn-blue w-full">{{ $id ? 'Buat Akun' : 'Create account' }}</button>
    </form>

    <p class="auth-anim mt-8 border-t border-navy-100 pt-6 text-center text-sm text-slate-600 [animation-delay:480ms]">
        {{ $id ? 'Sudah punya akun?' : 'Already have an account?' }}
        <a href="{{ route('login') }}" class="font-medium link-underline">{{ __('site.nav.login') }}</a>
    </p>
</x-auth-layout>
