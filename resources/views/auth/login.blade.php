@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="__('site.nav.login')"
    :heading="$id ? 'Selamat datang kembali' : 'Welcome back'"
    :subheading="$id ? 'Masuk untuk melanjutkan pesanan dan melihat riwayatnya.' : 'Sign in to continue an order and review your history.'">

    {{-- Tamu yang menekan Kirim di formulir Kemitraan dipulangkan ke sini oleh
         PartnershipController::store, dan isiannya dititipkan ke sesi. Tanpa
         baris ini, yang bersangkutan hanya tahu dirinya tiba-tiba berada di
         halaman masuk — dan menduga formulir tadi hilang. Penandanya titipan
         isian itu sendiri, bukan pesan sekilas: titipannya bertahan walau ia
         mampir ke halaman Daftar atau Lupa Kata Sandi lebih dulu. --}}
    @if (session()->has('partnership.draft'))
        <div class="auth-anim mb-6 flex items-start gap-3 rounded-2xl border border-navy-200 bg-navy-50 px-4 py-3.5 text-sm leading-relaxed text-navy-800" role="status">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-navy-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 118 0v3"/>
            </svg>
            <span>{{ $id
                ? 'Masuk dulu untuk mengirim pendaftaran kemitraan Anda. Isian formulirnya sudah kami simpan dan akan dikembalikan setelah Anda masuk.'
                : 'Sign in to submit your partnership registration. The details you filled in are saved and will be restored once you are in.' }}</span>
        </div>
    @endif

    @error('oauth')
        <div class="auth-anim mb-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3.5 text-sm leading-relaxed text-rose-800" role="alert">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-11.25a.75.75 0 011.5 0v4.5a.75.75 0 01-1.5 0v-4.5zm.75 7.5a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <x-google-auth-button action="login" />

    <form method="POST" action="{{ route('login') }}" class="auth-form space-y-5">
        @csrf
        <x-field name="email" type="email" :label="__('site.contact.email')" required />
        <x-field name="password" type="password" :label="$id ? 'Kata Sandi' : 'Password'" required />

        <div class="flex flex-wrap items-center justify-between gap-3">
            {{-- Cincin fokusnya biru seperti sisa situs; emas di atas putih tidak
                 cukup kontras untuk penanda fokus. --}}
            <label class="flex cursor-pointer items-center gap-2.5 text-sm text-slate-700">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-navy-200 text-sky-600 focus:ring-sky-700">
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
