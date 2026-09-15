@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Verifikasi Email' : 'Verify Email'"
    :heading="$id ? 'Cek kotak masuk Anda' : 'Check your inbox'"
    :subheading="$id ? 'Kami mengirim satu tautan verifikasi ke email Anda. Buka tautan itu untuk mengaktifkan akun.' : 'We sent a verification link to your email. Open it to activate your account.'">

    <form method="POST" action="{{ route('verification.resend') }}" class="auth-form">
        @csrf
        <button type="submit" class="btn-blue w-full">{{ $id ? 'Kirim Ulang Tautan' : 'Resend Link' }}</button>
    </form>

    {{-- Saran yang menjawab penyebab tersering sebelum pengguna mengirim ulang
         berkali-kali atau menyerah dan menutup halamannya. --}}
    <p class="auth-anim mt-4 text-center text-sm leading-relaxed text-slate-500 [animation-delay:200ms]">
        {{ $id ? 'Belum masuk juga? Periksa folder spam atau promosi.' : 'Still nothing? Check your spam or promotions folder.' }}
    </p>

    <form method="POST" action="{{ route('logout') }}" class="auth-anim mt-8 border-t border-navy-100 pt-6 text-center [animation-delay:280ms]">
        @csrf
        <button type="submit" class="text-sm link-underline">{{ $id ? 'Keluar' : 'Sign out' }}</button>
    </form>
</x-auth-layout>
