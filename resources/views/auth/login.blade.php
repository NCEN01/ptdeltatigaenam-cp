@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="__('site.nav.login')"
    :heading="$id ? 'Selamat datang kembali' : 'Welcome back'"
    :subheading="$id ? 'Masuk untuk mengelola pesanan Anda.' : 'Sign in to manage your orders.'">

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-field name="email" type="email" :label="__('site.contact.email')" required />
        <x-field name="password" type="password" :label="$id ? 'Kata Sandi' : 'Password'" required />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-navy-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-navy-200 text-navy focus:ring-gold">
                {{ $id ? 'Ingat saya' : 'Remember me' }}
            </label>
            <a href="{{ route('password.request') }}" class="text-sm link-underline">{{ $id ? 'Lupa kata sandi?' : 'Forgot password?' }}</a>
        </div>

        <button type="submit" class="btn-primary w-full">{{ __('site.nav.login') }}</button>
    </form>

    <p class="mt-8 text-center text-sm text-navy-500">
        {{ $id ? 'Belum punya akun?' : "Don't have an account?" }}
        <a href="{{ route('register') }}" class="font-medium link-underline">{{ $id ? 'Daftar' : 'Sign up' }}</a>
    </p>
</x-auth-layout>
