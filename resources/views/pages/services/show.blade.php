@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="$service->title" :description="$service->short_description">
    <section class="relative overflow-hidden bg-navy-950 pt-36 pb-16 text-white md:pt-44 md:pb-24">
        <div class="pointer-events-none absolute inset-0 aurora opacity-60"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-50"></div>
        <div class="container relative grid gap-12 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <a href="{{ route('services.index') }}" class="mb-6 inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-navy-200 hover:text-gold">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $service->category?->name }}
                </a>
                <h1 class="text-display-xl font-semibold text-balance">{{ $service->title }}</h1>
                @if ($service->short_description)<p class="mt-6 max-w-2xl text-lg text-navy-100 text-pretty">{{ $service->short_description }}</p>@endif
                <div class="mt-8 flex flex-wrap items-center gap-6 text-sm text-navy-200">
                    @if ($service->duration)<span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $service->duration }}</span>@endif
                    @if ($service->location)<span class="inline-flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-gold"></span>{{ $service->location }}</span>@endif
                </div>
            </div>
            <div class="lg:col-span-5">
                <div class="rounded-3xl border border-white/10 bg-white/[0.05] p-7 backdrop-blur-xl">
                    <p class="font-mono text-[10px] uppercase tracking-label text-gold">{{ $id ? 'Investasi' : 'Investment' }}</p>
                    @if ($service->price > 0)
                        <p class="mt-2 font-display text-4xl font-semibold">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                        @if ($service->price_label)<p class="mt-1 text-sm text-navy-200">{{ $service->price_label }}</p>@endif
                    @else
                        <p class="mt-2 font-display text-3xl font-semibold">{{ $id ? 'Hubungi kami' : 'Contact us' }}</p>
                    @endif
                    <a href="#jadwal" class="btn-gold mt-6 w-full">{{ $service->is_purchasable ? __('site.cta.buy') : __('site.cta.consult') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container grid gap-12 lg:grid-cols-12">
            {{-- Description + activities --}}
            <div class="lg:col-span-7">
                @if ($service->description)
                    <div class="prose prose-lg max-w-none text-navy-700 prose-headings:font-display prose-headings:text-navy prose-a:text-navy">
                        {!! $service->description !!}
                    </div>
                @endif

                @if ($service->activities->isNotEmpty())
                    <div class="mt-12">
                        <p class="eyebrow mb-6"><span class="rule-gold mr-3"></span>{{ $id ? 'Kegiatan' : 'Activities' }}</p>
                        <div class="space-y-4">
                            @foreach ($service->activities as $activity)
                                <div class="flex gap-5 rounded-2xl border border-navy-100 p-6">
                                    <span class="font-display text-2xl font-semibold text-navy-200">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div>
                                        <h3 class="font-display text-lg font-semibold text-navy">{{ $activity->title }}</h3>
                                        @if ($activity->description)<p class="mt-1 text-pretty text-navy-500">{{ $activity->description }}</p>@endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Schedules --}}
            <div class="lg:col-span-5" id="jadwal">
                <div class="sticky top-28 rounded-3xl border border-navy-100 bg-mist p-7">
                    <p class="eyebrow mb-5">{{ $id ? 'Jadwal & Batch' : 'Schedules & Batches' }}</p>
                    @forelse ($service->schedules as $schedule)
                        <div class="mb-3 rounded-2xl border border-navy-100 bg-white p-5">
                            <div class="flex items-center justify-between">
                                <p class="font-display text-lg font-semibold text-navy">{{ $schedule->start_date?->translatedFormat('d M Y') }}</p>
                                <span class="rounded-full bg-navy-50 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-navy-500">{{ $schedule->mode }}</span>
                            </div>
                            @if ($schedule->location)<p class="mt-1 text-sm text-navy-400">{{ $schedule->location }}</p>@endif
                            @if ($schedule->quota)
                                <p class="mt-2 font-mono text-xs text-navy-400">{{ max(0, $schedule->quota - $schedule->seats_taken) }} {{ $id ? 'kursi tersisa' : 'seats left' }}</p>
                            @endif
                            @if ($schedule->effectivePrice() > 0)
                                <p class="mt-3 font-display text-xl font-semibold text-navy">Rp {{ number_format($schedule->effectivePrice(), 0, ',', '.') }}<span class="text-sm font-normal text-navy-400"> / {{ $id ? 'peserta' : 'person' }}</span></p>
                            @endif
                            @if ($service->is_purchasable)
                                @if (Route::has('checkout.create'))
                                    <a href="{{ route('checkout.create', $schedule->id) }}" class="btn-primary mt-4 w-full !py-2.5 text-sm">{{ __('site.cta.buy') }}</a>
                                @else
                                    <a href="{{ route('contact.index') }}" class="btn-ghost mt-4 w-full !py-2.5 text-sm">{{ __('site.cta.consult') }}</a>
                                @endif
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-navy-400">{{ $id ? 'Belum ada jadwal tersedia. Hubungi kami untuk jadwal khusus.' : 'No schedules yet. Contact us for a custom batch.' }}</p>
                        <a href="{{ route('contact.index') }}" class="btn-ghost mt-5 w-full">{{ __('site.cta.consult') }}</a>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="section bg-mist">
            <div class="container">
                <p class="eyebrow mb-8"><span class="rule-gold mr-3"></span>{{ $id ? 'Layanan terkait' : 'Related services' }}</p>
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('services.show', $r->slug) }}" class="card card-hover group p-6">
                            <h3 class="font-display text-lg font-semibold text-navy group-hover:text-navy-500">{{ $r->title }}</h3>
                            @if ($r->short_description)<p class="mt-2 line-clamp-2 text-sm text-navy-500">{{ $r->short_description }}</p>@endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
