{{--
    Kotak "Saya bukan robot" (reCAPTCHA v2).

    Tidak merender apa pun selama kuncinya belum diisi di .env, dan itu
    disengaja: situs tetap berjalan persis seperti sebelum fitur ini ada,
    sampai kuncinya tersedia. Lihat config/captcha.php.

    Skripnya dimuat lewat @once di stack 'scripts', bukan di sini langsung:
    satu halaman bisa memuat lebih dari satu formulir, dan memuat skrip
    reCAPTCHA dua kali membuat Google mencatat peringatan lalu merender
    kotaknya dua kali di tempat yang sama.

    async + defer mengikuti anjuran Google: skripnya tidak menghalangi render,
    dan kotaknya muncul begitu halaman siap.
--}}
@if (config('captcha.enabled'))
    @once
        @push('scripts')
            <script src="https://www.google.com/recaptcha/api.js?hl={{ app()->getLocale() }}" async defer></script>
        @endpush
    @endonce

    @php $isId = app()->getLocale() === 'id'; @endphp

    {{-- data-field-box supaya pesan galatnya mendarat di bawah kotak, sejajar
         dengan pesan galat kolom lain — lihat resources/js/form-validate.js. --}}
    <div data-field-box="g-recaptcha-response" {{ $attributes->class(['pt-1']) }}>
        {{-- Pembungkus bertepi membulat menyamakan kotak buatan Google dengan
             kolom lain di formulir ini, yang seluruhnya rounded-2xl. Kotaknya
             sendiri tidak bisa digayakan — yang bisa hanya bingkainya. --}}
        <div class="inline-block overflow-hidden rounded-2xl border border-navy-100 bg-navy-50/40 p-3">
            <div class="g-recaptcha"
                 data-sitekey="{{ config('captcha.site_key') }}"
                 data-theme="light"></div>
        </div>

        @error('g-recaptcha-response')
            <p class="field-note" role="alert">{{ $message }}</p>
        @enderror

        <p class="mt-2 text-xs leading-relaxed text-slate-500">
            {{ $isId
                ? 'Dilindungi reCAPTCHA. Berlaku Kebijakan Privasi dan Persyaratan Layanan Google.'
                : 'Protected by reCAPTCHA. The Google Privacy Policy and Terms of Service apply.' }}
        </p>
    </div>
@endif
