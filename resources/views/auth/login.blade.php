@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="__('site.nav.login')"
    :heading="$id ? 'Selamat datang kembali' : 'Welcome back'"
    :subheading="$id ? 'Masuk untuk melanjutkan pesanan dan melihat riwayatnya.' : 'Sign in to continue an order and review your history.'">

    <form method="POST" action="{{ route('login') }}" class="auth-form space-y-5">
        @csrf
        <x-field name="email" type="email" :label="__('site.contact.email')" required />
        <x-field name="password" type="password" :label="$id ? 'Kata Sandi' : 'Password'" required />

        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Cincin fokusnya biru seperti sisa situs; emas di atas putih tidak
                 cukup kontras untuk penanda fokus. --}}
            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-700">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-navy-200 text-sky-600 focus:ring-sky-500">
                {{ $id ? 'Ingat saya' : 'Remember me' }}
            </label>
            <a href="{{ route('password.request') }}" class="text-sm link-underline">{{ $id ? 'Lupa kata sandi?' : 'Forgot password?' }}</a>
        </div>

        <button type="submit" class="btn-blue w-full">{{ __('site.nav.login') }}</button>
    </form>

    <p class="auth-anim mt-8 border-t border-navy-100 pt-6 text-center text-sm text-slate-600 [animation-delay:420ms]">
        {{ $id ? 'Belum punya akun?' : "Don't have an account?" }}
        <a href="{{ route('register') }}" class="font-medium link-underline">{{ $id ? 'Daftar sekarang' : 'Create one' }}</a>
    </p>
</x-auth-layout>
