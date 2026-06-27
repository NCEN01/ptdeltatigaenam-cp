@php
    $id = app()->getLocale() === 'id';
    $tierStyle = [
        'blue' => 'from-blue-600 to-navy-700',
        'silver' => 'from-slate-400 to-slate-600',
        'gold' => 'from-gold to-gold-deep',
        'platinum' => 'from-navy-700 to-navy-950',
    ];
@endphp

<x-layout :title="__('site.nav.partnership')" :description="$intro">
    <x-page-header
        :eyebrow="$id ? 'Kemitraan Corporate' : 'Corporate Partnership'"
        :title="$id ? 'Program Kemitraan Corporate Training' : 'Corporate Training Partnership Program'"
        :subtitle="$intro">
        <div class="mt-8">
            <a href="#daftar" class="btn-gold">{{ $id ? 'Daftar Sekarang' : 'Apply Now' }}</a>
        </div>
    </x-page-header>

    {{-- Benefits --}}
    @if ($benefits->isNotEmpty())
        <section class="section">
            <div class="container">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Manfaat Program' : 'Program Benefits' }}</p>
                <h2 class="max-w-2xl text-display-lg font-semibold text-navy" data-aos="fade-up">{{ $id ? 'Manfaat Program Kemitraan Corporate' : 'Corporate Partnership Benefits' }}</h2>

                <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($benefits as $benefit)
                        <div class="card p-7" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy-50 text-navy">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <h3 class="mt-5 font-display text-lg font-semibold text-navy">{{ $benefit->title }}</h3>
                            @if ($benefit->description)<p class="mt-2 text-pretty text-navy-500">{{ $benefit->description }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Packages --}}
    @if ($packages->isNotEmpty())
        <section class="section bg-mist">
            <div class="container">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Penawaran Paket' : 'Package Offerings' }}</p>
                <h2 class="max-w-2xl text-display-lg font-semibold text-navy" data-aos="fade-up">Blue · Silver · Gold · Platinum</h2>

                <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($packages as $package)
                        @php $features = $package->features; @endphp
                        <div class="relative flex flex-col rounded-3xl border bg-white p-7 transition-all duration-300 {{ $package->is_highlighted ? 'border-gold shadow-gold lg:-translate-y-3' : 'border-navy-100 shadow-card' }}"
                             data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                            @if ($package->is_highlighted)
                                <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-gold px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-ink">{{ $id ? 'Populer' : 'Popular' }}</span>
                            @endif
                            <span class="inline-flex w-fit rounded-full bg-gradient-to-br {{ $tierStyle[$package->tier] ?? 'from-navy-700 to-navy-950' }} px-4 py-1.5 font-display text-sm font-semibold text-white">{{ $package->name }}</span>
                            @if ($package->tagline)<p class="mt-4 text-sm text-navy-500">{{ $package->tagline }}</p>@endif
                            <div class="mt-5">
                                @if ($package->price)
                                    <p class="font-display text-2xl font-semibold text-navy">Rp {{ number_format((float) $package->price, 0, ',', '.') }}</p>
                                @else
                                    <p class="font-display text-xl font-semibold text-navy">{{ $package->price_note ?: ($id ? 'Hubungi kami' : 'Contact us') }}</p>
                                @endif
                            </div>
                            @if (is_array($features) && count($features))
                                <ul class="mt-6 flex-1 space-y-3 border-t border-navy-100 pt-6">
                                    @foreach ($features as $feature)
                                        <li class="flex items-start gap-2.5 text-sm text-navy-600">
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-gold" viewBox="0 0 16 16" fill="none"><path d="M3 8l3.5 3.5L13 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="flex-1"></div>
                            @endif
                            <a href="#daftar" class="{{ $package->is_highlighted ? 'btn-gold' : 'btn-ghost' }} mt-7 w-full">{{ $id ? 'Pilih Paket' : 'Choose Plan' }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Registration form --}}
    <section id="daftar" class="section scroll-mt-28">
        <div class="container grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="eyebrow mb-4"><span class="rule-gold mr-3"></span>{{ $id ? 'Daftar Kemitraan' : 'Partnership Application' }}</p>
                <h2 class="text-display-lg font-semibold text-navy">{{ $id ? 'Mulai kolaborasi' : 'Start a collaboration' }}</h2>
                <p class="mt-4 text-pretty text-navy-500">
                    {{ $id
                        ? 'Isi formulir dan tim PT Delta Tiga Enam akan menghubungi Anda untuk menjadwalkan presentasi serta menyiapkan penawaran. Tidak ada pembayaran online — penagihan dilakukan melalui invoice.'
                        : 'Submit the form and the PT Delta Tiga Enam team will contact you to schedule a presentation and prepare an offer. No online payment — billing is handled via invoice.' }}
                </p>
            </div>

            <div class="lg:col-span-8">
                @if (session('status'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status" aria-live="polite">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M16.7 5.7a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 10a1 1 0 011.4-1.4l3.3 3.3 6.8-6.8a1 1 0 011.4 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('partnership.store') }}" class="space-y-8">
                    @csrf

                    {{-- A. Company info --}}
                    <fieldset class="rounded-3xl border border-navy-100 p-7">
                        <legend class="px-2 font-mono text-[11px] uppercase tracking-label text-gold">A · {{ $id ? 'Informasi Perusahaan' : 'Company Information' }}</legend>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-field name="company_name" :label="$id ? 'Nama Perusahaan' : 'Company Name'" required />
                            <x-field name="pic_name" :label="$id ? 'Nama PIC' : 'PIC Name'" required />
                            <x-field name="pic_position" :label="$id ? 'Jabatan PIC' : 'PIC Position'" />
                            <x-field name="phone" :label="$id ? 'No. Telp/WhatsApp' : 'Phone/WhatsApp'" required />
                            <x-field name="email" type="email" :label="__('site.contact.email')" required />
                        </div>
                        <div class="mt-5">
                            <x-field name="company_address" type="textarea" :label="$id ? 'Alamat Perusahaan' : 'Company Address'" required />
                        </div>
                    </fieldset>

                    {{-- B. Package --}}
                    <fieldset class="rounded-3xl border border-navy-100 p-7">
                        <legend class="px-2 font-mono text-[11px] uppercase tracking-label text-gold">B · {{ $id ? 'Pilihan Paket' : 'Package Choice' }}</legend>
                        <div class="grid gap-3 sm:grid-cols-4">
                            @foreach ($packages as $package)
                                <label class="cursor-pointer">
                                    <input type="radio" name="partnership_package_id" value="{{ $package->id }}" class="peer sr-only" @checked(old('partnership_package_id') == $package->id)>
                                    <span class="block rounded-2xl border border-navy-200 px-4 py-3 text-center font-display font-semibold text-navy transition-all peer-checked:border-gold peer-checked:bg-gold/10 peer-checked:text-navy hover:border-navy">{{ $package->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('partnership_package_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </fieldset>

                    {{-- C. Scheduling --}}
                    <fieldset class="rounded-3xl border border-navy-100 p-7">
                        <legend class="px-2 font-mono text-[11px] uppercase tracking-label text-gold">C · {{ $id ? 'Penjadwalan Presentasi/Meeting' : 'Presentation/Meeting Scheduling' }}</legend>
                        <p class="mb-5 text-sm text-navy-500">{{ $id ? 'Pilih waktu yang Anda inginkan; tim PT Delta Tiga Enam akan mengonfirmasi.' : 'Choose your preferred time; the PT Delta Tiga Enam team will confirm.' }}</p>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="preferred_meeting_at" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Waktu Diinginkan' : 'Preferred Time' }}</label>
                                <input type="datetime-local" id="preferred_meeting_at" name="preferred_meeting_at" value="{{ old('preferred_meeting_at') }}" class="w-full rounded-2xl border border-navy-200 px-4 py-3 text-navy focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                            </div>
                            <div>
                                <label for="alternative_meeting_at" class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Waktu Alternatif' : 'Alternative Time' }}</label>
                                <input type="datetime-local" id="alternative_meeting_at" name="alternative_meeting_at" value="{{ old('alternative_meeting_at') }}" class="w-full rounded-2xl border border-navy-200 px-4 py-3 text-navy focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                            </div>
                        </div>
                    </fieldset>

                    {{-- D. Notes --}}
                    <fieldset class="rounded-3xl border border-navy-100 p-7">
                        <legend class="px-2 font-mono text-[11px] uppercase tracking-label text-gold">D · {{ $id ? 'Catatan Tambahan' : 'Additional Notes' }}</legend>
                        <x-field name="notes" type="textarea" :label="$id ? 'Catatan' : 'Notes'" />
                    </fieldset>

                    <button type="submit" class="btn-gold w-full sm:w-auto">
                        {{ __('site.cta.send') }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <p class="text-xs text-navy-400">{{ $id ? 'Dengan mengirim, Anda setuju dihubungi oleh tim PT Delta Tiga Enam. Tanpa pembayaran online.' : 'By submitting, you agree to be contacted by the PT Delta Tiga Enam team. No online payment.' }}</p>
                </form>
            </div>
        </div>
    </section>
</x-layout>
