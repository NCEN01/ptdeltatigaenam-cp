@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Atur Ulang Kata Sandi' : 'Reset Password'"
    :heading="$id ? 'Kata sandi baru' : 'New password'"
    :subheading="$id ? 'Buat kata sandi baru untuk akun Anda, lalu Anda bisa langsung masuk.' : 'Set a new password for your account, then sign straight in.'">

    <form method="POST" action="{{ route('password.update') }}" class="auth-form space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" type="email" :label="__('site.contact.email')" :value="$email" required />
        {{-- autocomplete new-password pada keduanya, supaya pengelola sandi
             menawarkan sandi baru alih-alih mengisikan yang lama. --}}
        <x-field name="password" type="password" :label="$id ? 'Kata Sandi Baru' : 'New Password'" autocomplete="new-password" required />
        <x-field name="password_confirmation" type="password" :label="$id ? 'Konfirmasi Kata Sandi Baru' : 'Confirm New Password'" autocomplete="new-password" required />
        <button type="submit" class="btn-blue w-full">{{ $id ? 'Simpan Kata Sandi' : 'Reset Password' }}</button>
    </form>
</x-auth-layout>
