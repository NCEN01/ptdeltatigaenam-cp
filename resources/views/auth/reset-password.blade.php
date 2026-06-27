@php $id = app()->getLocale() === 'id'; @endphp

<x-auth-layout :title="$id ? 'Atur Ulang Kata Sandi' : 'Reset Password'"
    :heading="$id ? 'Kata sandi baru' : 'New password'">

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" type="email" :label="__('site.contact.email')" :value="$email" required />
        <x-field name="password" type="password" :label="$id ? 'Kata Sandi Baru' : 'New Password'" required />
        <x-field name="password_confirmation" type="password" :label="$id ? 'Konfirmasi' : 'Confirm Password'" required />
        <button type="submit" class="btn-primary w-full">{{ $id ? 'Simpan Kata Sandi' : 'Reset Password' }}</button>
    </form>
</x-auth-layout>
