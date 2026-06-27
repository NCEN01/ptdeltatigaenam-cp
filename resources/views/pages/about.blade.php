@php use Illuminate\Support\Facades\Storage; @endphp

<x-layout :title="__('site.nav.about')" :description="$about">
    <x-page-header
        :eyebrow="__('site.nav.about')"
        :title="app()->getLocale() === 'id' ? 'Membangun human capital yang berdampak' : 'Building human capital that matters'" />

    {{-- Profile --}}
    <section class="section">
        <div class="container grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="eyebrow mb-5"><span class="rule-gold mr-3"></span>{{ app()->getLocale() === 'id' ? 'Profil' : 'Profile' }}</p>
                <div class="prose prose-lg max-w-none text-navy-700 prose-headings:font-display prose-headings:text-navy">
                    <p class="text-pretty text-xl leading-relaxed">{{ $about }}</p>
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-navy-100 bg-mist p-8">
                    <p class="eyebrow mb-4">{{ app()->getLocale() === 'id' ? 'Visi' : 'Vision' }}</p>
                    <p class="font-display text-2xl font-semibold leading-snug text-navy text-balance">{{ $vision }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Missions --}}
    @if ($missions->isNotEmpty())
        <section class="section bg-mist">
            <div class="container">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ app()->getLocale() === 'id' ? 'Misi Kami' : 'Our Mission' }}</p>
                <h2 class="max-w-2xl text-display-lg font-semibold text-navy" data-aos="fade-up">{{ app()->getLocale() === 'id' ? 'Komitmen yang kami pegang' : 'The commitments we hold' }}</h2>

                <div class="mt-14 grid gap-6 md:grid-cols-3">
                    @foreach ($missions as $mission)
                        <div class="card p-8" data-aos="fade-up" data-aos-delay="{{ $loop->index * 90 }}">
                            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-navy text-white font-display text-lg">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <p class="mt-6 text-pretty text-navy-700">{{ $mission->content }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Offices --}}
    @if ($offices->isNotEmpty())
        <section class="section">
            <div class="container">
                <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ __('site.common.offices') }}</p>
                <div class="mt-12 grid gap-px overflow-hidden rounded-3xl border border-navy-100 bg-navy-100 md:grid-cols-3">
                    @foreach ($offices as $office)
                        <div class="bg-white p-8" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                            <p class="eyebrow-muted">{{ ucfirst($office->type) }}</p>
                            <h3 class="mt-3 font-display text-xl font-semibold text-navy">{{ $office->name }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-navy-500">{{ $office->address }}</p>
                            @if ($office->phone)<p class="mt-4 font-mono text-xs text-navy-400">{{ $office->phone }}</p>@endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-cta-band />
</x-layout>
