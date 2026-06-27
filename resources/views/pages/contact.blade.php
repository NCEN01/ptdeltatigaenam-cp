@php $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.contact')">
    <x-page-header
        :eyebrow="__('site.nav.contact')"
        :title="__('site.contact.title')"
        :subtitle="$id ? 'Ceritakan kebutuhan organisasi Anda — tim kami akan merespons dengan cepat.' : 'Tell us about your needs — our team will respond promptly.'" />

    <section class="section">
        <div class="container grid gap-12 lg:grid-cols-12">
            {{-- Form --}}
            <div class="lg:col-span-7">
                @if (session('status'))
                    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status" aria-live="polite">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M16.7 5.7a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 10a1 1 0 011.4-1.4l3.3 3.3 6.8-6.8a1 1 0 011.4 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
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
                <div class="space-y-4">
                    @foreach ($offices as $office)
                        <div class="rounded-2xl border border-navy-100 bg-mist p-6">
                            <p class="eyebrow-muted">{{ ucfirst($office->type) }}</p>
                            <h3 class="mt-2 font-display text-lg font-semibold text-navy">{{ $office->name }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-navy-500">{{ $office->address }}</p>
                            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-1 font-mono text-xs text-navy-400">
                                @if ($office->phone)<span>{{ $office->phone }}</span>@endif
                                @if ($office->email)<a href="mailto:{{ $office->email }}" class="hover:text-gold">{{ $office->email }}</a>@endif
                            </div>
                            @if ($office->map_embed)
                                <div class="mt-4 overflow-hidden rounded-xl [&>iframe]:w-full [&>iframe]:h-48 [&>iframe]:border-0">{!! $office->map_embed !!}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-layout>
