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

    $officesJson = json_encode($offices);
@endphp

<x-layout :title="__('site.nav.contact')">
    <x-page-header
        :eyebrow="__('site.nav.contact')"
        :title="__('site.contact.title')"
        :subtitle="$id ? 'Ceritakan kebutuhan organisasi Anda — tim kami akan merespons dengan cepat.' : 'Tell us about your needs — our team will respond promptly.'"
        image="photo-1497366754035-f200968a6e72" />

    <div x-data="{ activeOffice: 0, offices: {{ $officesJson }} }">
        {{-- Main Contact Section --}}
        <section class="section">
            <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">
                
                {{-- Left Column: Contact Methods & Switcher --}}
                <div class="lg:col-span-5 flex flex-col justify-between space-y-10" data-aos="fade-right">
                    
                    {{-- Quick contact info --}}
                    <div class="space-y-4">
                        <p class="eyebrow"><span class="rule-gold mr-3"></span>{{ $id ? 'Informasi Kontak' : 'Contact Info' }}</p>
                        
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                            {{-- Email Card --}}
                            <a href="mailto:{{ $email }}" class="card card-hover flex items-center gap-4 p-5 border-navy-100/60 shadow-card">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-navy/5 text-sky">
                                    <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <div>
                                    <p class="eyebrow-muted">Email</p>
                                    <p class="mt-0.5 text-[13px] font-semibold text-navy leading-none">{{ $email }}</p>
                                </div>
                            </a>

                            {{-- Phone Card --}}
                            <a href="tel:+622158905002" class="card card-hover flex items-center gap-4 p-5 border-navy-100/60 shadow-card">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-navy/5 text-sky">
                                    <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </span>
                                <div>
                                    <p class="eyebrow-muted">{{ $id ? 'Telepon' : 'Phone' }}</p>
                                    <p class="mt-0.5 text-[13px] font-semibold text-navy leading-none">021-5890 5002</p>
                                </div>
                            </a>

                            {{-- Hours Card --}}
                            <div class="card flex items-center gap-4 p-5 border-navy-100/60 shadow-card sm:col-span-2 lg:col-span-1">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-navy/5 text-sky">
                                    <svg class="h-5.5 w-5.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </span>
                                <div>
                                    <p class="eyebrow-muted">{{ $id ? 'Jam Operasional' : 'Office Hours' }}</p>
                                    <p class="mt-0.5 text-[13px] font-semibold text-navy leading-none">{{ $id ? 'Senin – Jumat · 08.00–17.00' : 'Mon – Fri · 8am–5pm' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Office list switcher --}}
                    <div class="space-y-4">
                        <p class="eyebrow"><span class="rule-gold mr-3"></span>{{ $id ? 'Lokasi Kantor' : 'Office Locations' }}</p>
                        
                        <div class="space-y-3">
                            @foreach ($offices as $index => $office)
                                <button
                                    type="button"
                                    x-on:click="activeOffice = {{ $index }}"
                                    class="w-full text-left p-5 rounded-2xl border transition-all duration-300 flex flex-col gap-1 focus:outline-none focus:ring-2 focus:ring-sky/50"
                                    x-bind:class="activeOffice === {{ $index }} ? 'border-sky bg-sky-50/20 shadow-lift' : 'border-navy-100 bg-white hover:border-navy-200'"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex items-center gap-2.5">
                                            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-colors"
                                                  x-bind:class="activeOffice === {{ $index }} ? 'bg-sky text-white' : 'bg-navy-50 text-navy-500'"
                                            >
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                            </span>
                                            <span class="font-display font-semibold text-navy transition-colors text-[15px]"
                                                  x-bind:class="activeOffice === {{ $index }} ? 'text-sky' : 'text-navy'"
                                            >
                                                {{ $office['name'] }}
                                            </span>
                                        </div>
                                        <span class="h-2.5 w-2.5 rounded-full transition-all duration-300"
                                              x-bind:class="activeOffice === {{ $index }} ? 'bg-sky scale-100 shadow-[0_0_8px_rgba(28,125,224,0.6)]' : 'bg-transparent scale-0'"
                                        ></span>
                                    </div>
                                    <p class="mt-2.5 text-xs leading-relaxed text-navy-400 font-sans">
                                        {{ $office['address'] }}
                                    </p>
                                    <div class="mt-4 flex items-center justify-between border-t border-navy-100/50 pt-3 text-[11px] font-mono text-navy-300">
                                        <span>PH. {{ $office['phone'] }}</span>
                                        <span class="text-sky hover:underline inline-flex items-center gap-1">
                                            {{ $id ? 'Lihat di Peta' : 'View on Map' }} &rarr;
                                        </span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Column: Contact Form --}}
                <div class="lg:col-span-7" data-aos="fade-left">
                    <div class="bg-white rounded-3xl border border-navy-100/80 p-8 md:p-10 shadow-lift">
                        <p class="eyebrow mb-3"><span class="rule-gold mr-3"></span>{{ $id ? 'Kirim Pesan' : 'Send Message' }}</p>
                        <h2 class="font-display text-2xl md:text-3xl font-bold text-navy mb-6 text-balance">{{ $id ? 'Mari bicarakan kebutuhan Anda' : "Let's talk about your needs" }}</h2>

                        @if (session('status'))
                            <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 text-sm" role="status" aria-live="polite">
                                <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd"/></svg>
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                            @csrf
                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-field name="name" :label="__('site.contact.name')" required />
                                <x-field name="email" type="email" :label="__('site.contact.email')" required />
                                <x-field name="phone" :label="__('site.contact.phone')" />
                                <x-field name="subject" :label="__('site.contact.subject')" />
                            </div>
                            <x-field name="message" :label="__('site.contact.message')" type="textarea" required />
                            
                            <div class="pt-2">
                                <button type="submit" class="btn-blue w-full sm:w-auto inline-flex items-center justify-center gap-2 group">
                                    <span>{{ __('site.cta.send') }}</span>
                                    <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 flex items-center gap-2.5 border-t border-navy-50 pt-5 text-[11px] text-navy-300 font-mono">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span>{{ $id ? 'Koneksi terenkripsi aman. Kami berkomitmen melindungi privasi data Anda.' : 'Secure connection. We are committed to protecting your data privacy.' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        {{-- Google Maps Section --}}
        <section class="pb-20 lg:pb-36" data-aos="fade-up">
            <div class="container">
                <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span><span x-text="offices[activeOffice].name + ' Map'"></span></p>
                <div class="overflow-hidden rounded-3xl border border-navy-100 shadow-lift bg-mist/30">
                    <iframe
                        x-bind:src="'https://www.google.com/maps?q=' + encodeURIComponent(offices[activeOffice].maps) + '&output=embed'"
                        class="h-[460px] w-full border-0 transition-all duration-500"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        x-bind:title="offices[activeOffice].name"
                        allowfullscreen></iframe>
                </div>
            </div>
        </section>
    </div>
</x-layout>
