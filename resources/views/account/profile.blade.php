@php $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="$id ? 'Akun Saya' : 'My Account'">
    <x-page-header :eyebrow="__('site.nav.account')" :title="$id ? 'Halo, '.$customer->name : 'Hello, '.$customer->name" />

    <section class="section">
        <div class="container grid gap-10 lg:grid-cols-12">
            <aside class="lg:col-span-3"><x-account-nav /></aside>

            <div class="lg:col-span-9">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status">{{ session('status') }}</div>
                @endif

                <div class="rounded-3xl border border-navy-100 p-8">
                    <h2 class="font-display text-xl font-semibold text-navy">{{ $id ? 'Data Profil' : 'Profile Details' }}</h2>
                    <form method="POST" action="{{ route('account.update') }}" class="mt-6 space-y-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-field name="name" :label="__('site.contact.name')" :value="$customer->name" required />
                            <x-field name="phone" :label="__('site.contact.phone')" :value="$customer->phone" />
                            <x-field name="company" :label="$id ? 'Perusahaan' : 'Company'" :value="$customer->company" />
                            <div>
                                <label for="preferred_locale" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Bahasa Pilihan' : 'Preferred Language' }}</label>
                                <select id="preferred_locale" name="preferred_locale" class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                    <option value="id" @selected($customer->preferred_locale === 'id')>Indonesia</option>
                                    <option value="en" @selected($customer->preferred_locale === 'en')>English</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-navy">{{ __('site.contact.email') }}</label>
                            <input type="email" value="{{ $customer->email }}" disabled class="w-full cursor-not-allowed rounded-2xl border border-navy-100 bg-mist px-4 py-3 text-navy-400">
                        </div>
                        <button type="submit" class="btn-primary">{{ $id ? 'Simpan Perubahan' : 'Save Changes' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layout>
