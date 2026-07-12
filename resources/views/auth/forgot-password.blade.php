@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Lupa Kata Sandi' : 'Forgot Password'"
    :heading="$id ? 'Atur ulang kata sandi' : 'Reset your password'"
    :subheading="$id ? 'Masukkan email Anda dan kami kirim tautan atur ulang.' : 'Enter your email and we’ll send a reset link.'">

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div style="display:none !important" aria-hidden="true">
            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
        </div>
        <x-field name="email" type="email" :label="__('site.contact.email')" required />
        <button type="submit" class="btn-primary w-full">{{ $id ? 'Kirim Tautan' : 'Send Reset Link' }}</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600">
        <a href="{{ route('login') }}" class="font-medium link-underline">{{ $id ? 'Kembali masuk' : 'Back to sign in' }}</a>
    </p>
</x-auth-layout>
