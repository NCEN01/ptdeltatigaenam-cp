@php $id = app()->getLocale() === 'id'; @endphp

{{-- auth-form & btn-blue, sama dengan halaman Masuk dan Daftar. Sebelumnya
     ketiga halaman sisa alur ini memakai btn-primary yang navy polos dan tidak
     memakai auth-form, jadi tombol utamanya berganti rupa dan gerak masuknya
     hilang begitu pengguna berpindah dari Masuk ke Lupa Kata Sandi. --}}
<x-auth-layout :title="$id ? 'Lupa Kata Sandi' : 'Forgot Password'"
    :heading="$id ? 'Atur ulang kata sandi' : 'Reset your password'"
    :subheading="$id ? 'Masukkan email akun Anda. Kami kirim tautan untuk membuat kata sandi baru.' : 'Enter your account email. We will send a link for setting a new password.'">

    <form method="POST" action="{{ route('password.email') }}" class="auth-form space-y-5">
        @csrf
        <div style="display:none !important" aria-hidden="true">
            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
        </div>
        <x-field name="email" type="email" :label="__('site.contact.email')" required />
        <button type="submit" class="btn-blue w-full">{{ $id ? 'Kirim Tautan' : 'Send Reset Link' }}</button>
    </form>

    <p class="auth-anim mt-8 border-t border-navy-100 pt-6 text-center text-sm text-slate-600 [animation-delay:420ms]">
        {{ $id ? 'Sudah ingat kata sandinya?' : 'Remembered your password?' }}
        <a href="{{ route('login') }}" class="font-medium link-underline">{{ $id ? 'Kembali masuk' : 'Back to sign in' }}</a>
    </p>
</x-auth-layout>
