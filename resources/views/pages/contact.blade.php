@php
    $id = app()->getLocale() === 'id';

    $email = 'info@deltatigaenam.com';
    $website = 'www.deltatigaenam.com';

    $offices = [
        [
            'name' => 'Kantor Pusat',
            'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan, Kebayoran Baru, Jakarta Selatan, DKI Jakarta',
            'phone' => '021-5890 5002, 0818 834 766',
            'maps' => 'Gedung Bursa Efek Indonesia Tower 1, SCBD, Jakarta Selatan',
        ],
        [
            'name' => 'Kantor Pemasaran',
            'address' => 'Cikarang Technopark, Jalan Inti I Blok C1 No. 7, Cibatu, Cikarang Selatan, Kabupaten Bekasi, Jawa Barat 17530',
            'phone' => '021-8988 1110',
            'maps' => 'Cikarang Technopark, Cikarang Selatan, Bekasi',
        ],
        [
            'name' => 'Kantor Operasional',
            'address' => 'Taman Widya Asri Blok GG No. 18, Serang, Kota Serang, Banten 46111',
            'phone' => '0817 018 6104',
            'maps' => 'Taman Widya Asri, Serang, Banten',
        ],
    ];

    $mapQuery = $offices[0]['maps'];
@endphp

<x-layout :title="__('site.nav.contact')">
    <x-page-header
        :eyebrow="__('site.nav.contact')"
        :title="__('site.contact.title')"
        :subtitle="$id ? 'Ceritakan kebutuhan organisasi Anda — tim kami akan merespons dengan cepat.' : 'Tell us about your needs — our team will respond promptly.'"
        image="photo-1497366754035-f200968a6e72" />

    {{-- Quick contact strip --}}
    <section class="section pb-0 lg:pb-0">
        <div class="container">
            <div class="grid gap-5 sm:grid-cols-3">
                <a href="mailto:{{ $email }}" class="card card-hover flex items-center gap-4 p-6" data-aos="fade-up">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v12H4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <p class="eyebrow-muted">Email</p>
                        <p class="mt-1 font-medium text-navy">{{ $email }}</p>
                    </div>
                </a>
                <a href="tel:+622158905002" class="card card-hover flex items-center gap-4 p-6" data-aos="fade-up" data-aos-delay="80">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M5 4h4l2 5-3 2a12 12 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <p class="eyebrow-muted">{{ $id ? 'Telepon' : 'Phone' }}</p>
                        <p class="mt-1 font-medium text-navy">021-5890 5002</p>
                    </div>
                </a>
                <div class="card flex items-center gap-4 p-6" data-aos="fade-up" data-aos-delay="160">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <div>
                        <p class="eyebrow-muted">{{ $id ? 'Jam Operasional' : 'Office Hours' }}</p>
                        <p class="mt-1 font-medium text-navy">{{ $id ? 'Senin – Jumat · 08.00–17.00' : 'Mon – Fri · 8am–5pm' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Form + offices --}}
    <section class="section">
        <div class="container grid gap-12 lg:grid-cols-12">
            {{-- Form --}}
            <div class="lg:col-span-7">
                <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $id ? 'Kirim Pesan' : 'Send a Message' }}</p>
                <h2 class="text-display-lg font-semibold text-navy text-balance">{{ $id ? 'Mari bicarakan kebutuhan Anda' : "Let's talk about your needs" }}</h2>

                @if (session('status'))
                    <div class="mt-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status" aria-live="polite">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M16.7 5.7a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 10a1 1 0 011.4-1.4l3.3 3.3 6.8-6.8a1 1 0 011.4 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-field name="name" :label="__('site.contact.name')" required />
                        <x-field name="email" type="email" :label="__('site.contact.email')" required />
                        <x-field name="phone" :label="__('site.contact.phone')" />
                        <x-field name="subject" :label="__('site.contact.subject')" />
                    </div>
                    <x-field name="message" :label="__('site.contact.message')" type="textarea" required />
                    <button type="submit" class="btn-primary">
                        {{ __('site.cta.send') }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>
            </div>

            {{-- Offices --}}
            <div class="lg:col-span-5">
                <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $id ? 'Kantor Kami' : 'Our Offices' }}</p>
                <div class="space-y-4">
                    @foreach ($offices as $office)
                        <div class="rounded-2xl border border-navy-100 bg-mist p-6 transition-colors hover:border-navy-200">
                            <div class="flex items-center gap-2.5">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-navy text-gold">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-5.2 7-11a7 7 0 10-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.5"/></svg>
                                </span>
                                <h3 class="font-display text-lg font-semibold text-navy">{{ $office['name'] }}</h3>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-navy-500">{{ $office['address'] }}</p>
                            <div class="mt-4 flex items-center justify-between border-t border-navy-100 pt-4">
                                <span class="font-mono text-xs text-navy-400">PH. {{ $office['phone'] }}</span>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($office['maps']) }}" target="_blank" rel="noopener" class="link-underline text-xs font-medium">
                                    {{ $id ? 'Lihat Peta' : 'View Map' }}
                                    <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Google Maps --}}
    <section class="pb-20 lg:pb-28">
        <div class="container">
            <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Lokasi Kantor Pusat' : 'Head Office Location' }}</p>
            <div class="overflow-hidden rounded-3xl border border-navy-100 shadow-card" data-aos="fade-up">
                <iframe
                    src="https://www.google.com/maps?q={{ urlencode($mapQuery) }}&output=embed"
                    class="h-[420px] w-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="{{ $id ? 'Peta Kantor Pusat PT Delta Tiga Enam' : 'PT Delta Tiga Enam Head Office Map' }}"
                    allowfullscreen></iframe>
            </div>
        </div>
    </section>
</x-layout>
