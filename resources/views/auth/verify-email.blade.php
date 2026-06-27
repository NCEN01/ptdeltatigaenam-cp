@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Verifikasi Email' : 'Verify Email'"
    :heading="$id ? 'Verifikasi email Anda' : 'Verify your email'"
    :subheading="$id ? 'Kami telah mengirim tautan verifikasi ke email Anda. Silakan periksa kotak masuk.' : 'We’ve sent a verification link to your email. Please check your inbox.'">

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn-primary w-full">{{ $id ? 'Kirim Ulang Tautan' : 'Resend Link' }}</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm link-underline">{{ $id ? 'Keluar' : 'Sign out' }}</button>
    </form>
</x-auth-layout>
