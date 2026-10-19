@props(['action' => 'login'])

@if (config('services.google.enabled'))
    @php
        $id = app()->getLocale() === 'id';
        $label = $action === 'register'
            ? ($id ? 'Daftar dengan Google' : 'Sign up with Google')
            : ($id ? 'Lanjutkan dengan Google' : 'Continue with Google');
    @endphp

    <div class="auth-anim mb-5">
        <a href="{{ route('auth.google') }}"
           class="flex w-full items-center justify-center gap-3 rounded-2xl border border-navy-200 bg-white px-5 py-3 text-sm font-medium text-navy-900 shadow-sm transition-colors duration-200 hover:border-navy-300 hover:bg-navy-50/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-700 focus-visible:ring-offset-2">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M23.49 12.27c0-.79-.07-1.54-.19-2.27H12v4.51h6.47c-.29 1.48-1.14 2.73-2.4 3.58v3h3.86c2.26-2.09 3.56-5.17 3.56-8.82z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.29v3.09C3.26 21.3 7.31 24 12 24z"/>
                <path fill="#FBBC05" d="M5.27 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.62H1.29a11.99 11.99 0 000 10.76l3.98-3.09z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.62l3.98 3.09c.95-2.85 3.6-4.96 6.73-4.96z"/>
            </svg>
            <span>{{ $label }}</span>
        </a>

        <div class="relative mt-5 flex items-center justify-center">
            <div class="w-full border-t border-navy-100"></div>
            <span class="bg-white px-3 text-xs uppercase tracking-wider text-slate-400">
                {{ $id ? 'atau dengan surel' : 'or with email' }}
            </span>
            <div class="w-full border-t border-navy-100"></div>
        </div>
    </div>
@endif
