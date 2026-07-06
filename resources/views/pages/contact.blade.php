@php
    $id = app()->getLocale() === 'id';

    $email = 'info@deltatigaenam.com';


    $waNumber = '62818834766';
    $waText = rawurlencode($id ? 'Halo Delta Tiga Enam, saya ingin bertanya seputar layanan Anda.' : 'Hello Delta Tiga Enam, I would like to ask about your services.');

    $contactRows = [
        ['label' => 'Email', 'value' => $email, 'href' => 'mailto:'.$email, 'icon' => 'mail'],
        ['label' => 'WhatsApp', 'value' => '0818 834 766', 'href' => 'https://wa.me/'.$waNumber.'?text='.$waText, 'icon' => 'whatsapp'],
        ['label' => $id ? 'Telepon' : 'Phone', 'value' => '021-5890 5002', 'href' => 'tel:+622158905002', 'icon' => 'phone'],
        ['label' => $id ? 'Jam Operasional' : 'Office Hours', 'value' => $id ? 'Senin – Jumat · 08.00–17.00' : 'Mon – Fri · 8am–5pm', 'href' => null, 'icon' => 'clock'],
    ];
@endphp

<x-layout :title="__('site.nav.contact')">
    <x-page-header
        :eyebrow="'PT Delta Tiga Enam'"
        :title="__('site.contact.title')"
        :subtitle="$id ? 'Ceritakan kebutuhan organisasi Anda — tim kami akan merespons dengan cepat.' : 'Tell us about your needs — our team will respond promptly.'"
        image="photo-1497366754035-f200968a6e72" />

    {{-- ===================== CONTACT + FORM ===================== --}}
    <section class="section bg-white">
        <div class="container grid gap-12 lg:grid-cols-12 lg:gap-16">

            {{-- Left: intro + quick contact --}}
            <div class="lg:col-span-5" data-aos="fade-right">
                <p class="eyebrow"><span class="rule-gold mr-3"></span>{{ $id ? 'Hubungi Kami' : 'Get in Touch' }}</p>
                <h2 class="mt-4 font-display text-3xl leading-tight text-navy text-balance md:text-4xl">
                    {{ $id ? 'Mari bicarakan kebutuhan Anda' : "Let's talk about your needs" }}
                </h2>
                <p class="mt-5 max-w-md text-pretty leading-relaxed text-navy-500">
                    {{ $id
                        ? 'Punya pertanyaan seputar pelatihan, sertifikasi, atau rekrutmen? Isi formulir di samping atau hubungi kami langsung — tim kami siap membantu.'
                        : 'Questions about training, certification, or recruitment? Fill out the form or reach us directly — our team is ready to help.' }}
                </p>

                <div class="mt-9 space-y-3">
                    @foreach ($contactRows as $row)
                        <{{ $row['href'] ? 'a' : 'div' }} @if ($row['href']) href="{{ $row['href'] }}" @endif
                            @if ($row['href'] && str_starts_with($row['href'], 'http')) target="_blank" rel="noopener" @endif
                            class="group flex items-center gap-4 rounded-2xl border border-navy-100 bg-white p-4 transition-all duration-300 {{ $row['href'] ? 'hover:-translate-y-0.5 hover:border-navy-200 hover:shadow-card' : '' }}">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-navy text-gold transition-colors duration-300 {{ $row['href'] ? 'group-hover:bg-sky-600 group-hover:text-white' : '' }}">
                                @if ($row['icon'] === 'mail')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                @elseif ($row['icon'] === 'whatsapp')
                                    <img src="{{ asset('icons/ic_whatsapp.png') }}" class="h-5 w-5 object-contain" alt="WhatsApp">
                                @elseif ($row['icon'] === 'phone')
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-navy-400">{{ $row['label'] }}</p>
                                <p class="mt-1 truncate text-sm font-semibold text-navy">{{ $row['value'] }}</p>
                            </div>
                            @if ($row['href'])
                                <svg class="ml-auto h-4 w-4 shrink-0 text-navy-300 transition-all duration-300 group-hover:translate-x-0.5 group-hover:text-sky-600" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @endif
                        </{{ $row['href'] ? 'a' : 'div' }}>
                    @endforeach
                </div>
            </div>

            {{-- Right: form --}}
            <div class="lg:col-span-7" data-aos="fade-left">
                <div class="rounded-3xl border border-navy-100 bg-white p-8 shadow-lift md:p-10">
                    <p class="eyebrow mb-3"><span class="rule-gold mr-3"></span>{{ $id ? 'Kirim Pesan' : 'Send a Message' }}</p>
                    <h2 class="mb-7 font-display text-2xl text-navy text-balance md:text-3xl">{{ $id ? 'Isi formulir di bawah ini' : 'Fill out the form below' }}</h2>

                    @if (session('status'))
                        <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800" role="status" aria-live="polite">
                            <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd"/></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="space-y-6">
                        @csrf
                        <div style="display:none !important" aria-hidden="true">
                            <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-field name="name" :label="__('site.contact.name')" required />
                            <x-field name="email" type="email" :label="__('site.contact.email')" required />
                            <x-field name="phone" :label="__('site.contact.phone')" />
                            <x-field name="subject" :label="__('site.contact.subject')" />
                        </div>
                        <x-field name="message" :label="__('site.contact.message')" type="textarea" required />

                        <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="flex items-center gap-2 font-mono text-[11px] text-navy-400">
                                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                {{ $id ? 'Data Anda aman & terlindungi.' : 'Your data is safe & protected.' }}
                            </p>
                            <button type="submit" class="btn-blue group w-full justify-center sm:w-auto">
                                <span>{{ __('site.cta.send') }}</span>
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== OFFICES + MAP ===================== --}}
    <section class="section-sm border-t border-navy-50 bg-neutral-50" x-data="{ activeOffice: 0, offices: {{ json_encode($offices) }} }">
        <div class="container">
            <div class="text-center" data-aos="fade-up">
                <p class="eyebrow inline-flex items-center justify-center"><span class="rule-gold mr-3"></span>{{ $id ? 'Lokasi Kantor' : 'Office Locations' }}</p>
                <h2 class="mt-4 font-display text-3xl text-navy md:text-4xl">{{ $id ? 'Temui kami di lokasi berikut' : 'Find us at these locations' }}</h2>
            </div>

            <div class="mt-12 grid items-stretch gap-6 lg:grid-cols-12">
                {{-- Switcher --}}
                <div class="space-y-3 lg:col-span-5" data-aos="fade-up">
                    @foreach ($offices as $index => $office)
                        <button type="button" x-on:click="activeOffice = {{ $index }}"
                                class="flex w-full flex-col gap-2 rounded-2xl border bg-white p-5 text-left transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500"
                                x-bind:class="activeOffice === {{ $index }} ? 'border-sky-500 shadow-lift' : 'border-navy-100 hover:border-navy-200 hover:shadow-card'">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl transition-colors duration-300"
                                          x-bind:class="activeOffice === {{ $index }} ? 'bg-sky-600 text-white' : 'bg-navy text-gold'">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M15 9h.01M9 13h.01M15 13h.01M10 21v-4h4v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                    <span class="font-display text-[17px] text-navy">{{ $office['name'] }}</span>
                                </div>
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-sky-500 transition-transform duration-300"
                                      x-bind:class="activeOffice === {{ $index }} ? 'scale-100' : 'scale-0'"></span>
                            </div>
                            <p class="text-sm leading-relaxed text-navy-500">{{ $office['address'] }}</p>
                            <div class="mt-1 flex items-center gap-2 border-t border-navy-100 pt-3 font-mono text-[11px] text-navy-400">
                                <svg class="h-3.5 w-3.5 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-3 2a12 12 0 005 5l2-3 5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/></svg>
                                {{ $office['phone'] }}
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Live map --}}
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-delay="80">
                    <div class="h-full min-h-[380px] overflow-hidden rounded-3xl border border-navy-100 shadow-lift">
                        <iframe
                            x-bind:src="'https://www.google.com/maps?q=' + encodeURIComponent(offices[activeOffice].maps) + '&output=embed'"
                            class="h-full min-h-[380px] w-full border-0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            x-bind:title="offices[activeOffice].name"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
