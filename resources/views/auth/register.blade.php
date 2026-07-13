@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Daftar' : 'Sign up'"
    :heading="$id ? 'Buat akun' : 'Create your account'"
    :subheading="$id ? 'Daftar untuk memesan layanan dan memantau pesanan.' : 'Register to book services and track your orders.'">

    <form method="POST" action="{{ route('register') }}" class="auth-form space-y-4">
        @csrf
        <div style="display:none !important" aria-hidden="true">
            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
        </div>
        <x-field name="name" :label="__('site.contact.name')" required />
        <x-field name="email" type="email" :label="__('site.contact.email')" required />
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="phone" :label="__('site.contact.phone')" />
            <x-field name="company" :label="$id ? 'Perusahaan' : 'Company'" />
        </div>
        <x-field name="password" type="password" :label="$id ? 'Kata Sandi' : 'Password'" autocomplete="new-password" required />
        <x-field name="password_confirmation" type="password" :label="$id ? 'Konfirmasi Kata Sandi' : 'Confirm Password'" autocomplete="new-password" required />

        <button type="submit" class="btn-blue w-full">{{ $id ? 'Daftar' : 'Create account' }}</button>
    </form>

    <p class="auth-anim mt-6 text-center text-sm text-slate-600 [animation-delay:480ms]">
        {{ $id ? 'Sudah punya akun?' : 'Already have an account?' }}
        <a href="{{ route('login') }}" class="font-medium link-underline">{{ __('site.nav.login') }}</a>
    </p>
</x-auth-layout>
