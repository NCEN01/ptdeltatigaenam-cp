@php $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="$id ? 'Akun Saya' : 'My Account'">
    <x-page-header :title="$id ? 'Halo, '.$customer->name : 'Hello, '.$customer->name" />

    <section class="section">
        <div class="container grid gap-10 lg:grid-cols-12">
            <aside class="lg:col-span-3"><x-account-nav /></aside>

            <div class="lg:col-span-9">
                @if (session('status'))
                    <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status">{{ session('status') }}</div>
                @endif

                {{-- .card, bukan kotak bergaris yang disusun sendiri: seluruh
                     bidang berisi di situs ini memakai kelas itu, dan halaman
                     akun satu-satunya yang menyusun sudut, tepi, dan bayangannya
                     sendiri. --}}
                <div class="card p-7 sm:p-8">
                    <div class="border-b border-navy-100 pb-5">
                        <h2 class="font-display text-xl font-semibold text-navy">{{ $id ? 'Data Profil' : 'Profile Details' }}</h2>
                        {{-- Menyebut ke mana data ini terpakai. Tanpa itu, kolom
                             "Perusahaan" di halaman akun terbaca sebagai catatan
                             pribadi, padahal ia yang nanti tercetak di pesanan. --}}
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600">
                            {{ $id
                                ? 'Data ini terisi otomatis saat Anda memesan layanan. Nama tercatat sebagai nama peserta, dan perusahaan sebagai nama perusahaan pemesan.'
                                : 'These details prefill your orders. Your name is recorded as the participant, and the company as the ordering organisation.' }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('account.update') }}" class="mt-6 space-y-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-field name="name" :label="__('site.contact.name')" :value="$customer->name" required />
                            <x-field name="phone" :label="__('site.contact.phone')" :value="$customer->phone" />
                            <x-field name="company" :label="$id ? 'Perusahaan' : 'Company'" :value="$customer->company" :placeholder="$id ? 'Opsional' : 'Optional'" />
                            <div>
                                <label for="preferred_locale" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Bahasa Pilihan' : 'Preferred Language' }}</label>
                                {{-- Cincin fokusnya navy-600, bukan emas. Seluruh
                                     kolom isian lain di situs memakai warna itu
                                     lewat <x-field>; hanya pilihan ini yang dulu
                                     berbeda, karena ia satu-satunya yang ditulis
                                     tangan di luar komponen. --}}
                                <select id="preferred_locale" name="preferred_locale"
                                        class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy transition-colors duration-200 focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-600">
                                    <option value="id" @selected($customer->preferred_locale === 'id')>Indonesia</option>
                                    <option value="en" @selected($customer->preferred_locale === 'en')>English</option>
                                </select>
                            </div>
                        </div>

                        {{-- Surel tidak bisa disunting di sini: ia penanda masuk
                             sekaligus alamat verifikasi, dan menggantinya menuntut
                             pembuktian ulang yang belum ada alurnya. Dinyatakan
                             terkunci beserta alasannya, bukan sekadar diredupkan. --}}
                        <div>
                            <label for="account-email" class="mb-1.5 block text-sm font-medium text-navy">{{ __('site.contact.email') }}</label>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <input id="account-email" type="email" value="{{ $customer->email }}" disabled
                                       class="w-full cursor-not-allowed rounded-2xl border border-navy-100 bg-mist px-4 py-3 text-slate-500">
                                @if ($customer->email_verified_at)
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-medium text-emerald-700">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                        {{ $id ? 'Terverifikasi' : 'Verified' }}
                                    </span>
                                @else
                                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-700">
                                        {{ $id ? 'Belum terverifikasi' : 'Not verified' }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500">
                                {{ $id ? 'Alamat surel dipakai untuk masuk dan tidak dapat diubah sendiri. Hubungi kami bila perlu diganti.' : 'Your email is your sign-in address and cannot be changed here. Contact us if it needs updating.' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 border-t border-navy-100 pt-6">
                            <button type="submit" class="btn-blue">{{ $id ? 'Simpan Perubahan' : 'Save Changes' }}</button>
                            <p class="text-xs text-slate-500">
                                {{ $id ? 'Terdaftar sejak ' : 'Member since ' }}{{ $customer->created_at->translatedFormat('d F Y') }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-layout>
