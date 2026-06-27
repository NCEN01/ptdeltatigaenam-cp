@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.services')">
    <x-page-header
        :eyebrow="__('site.home.services_kicker')"
        :title="__('site.home.services_title')"
        :subtitle="$id ? 'Dari konsultasi manajemen hingga sertifikasi kompetensi — dirancang untuk hasil yang terukur.' : 'From management consulting to competency certification — designed for measurable outcomes.'">
        <nav class="mt-10 flex flex-wrap gap-2" aria-label="Categories">
            @foreach ($categories as $cat)
                <a href="#{{ $cat->slug }}" class="rounded-full border border-white/15 px-4 py-2 text-sm text-navy-100 transition-colors hover:border-gold hover:text-gold">{{ $cat->name }}</a>
            @endforeach
        </nav>
    </x-page-header>

    @forelse ($categories as $cat)
        <section id="{{ $cat->slug }}" class="section scroll-mt-28 {{ $loop->odd ? '' : 'bg-mist' }}">
            <div class="container">
                <div class="flex flex-col gap-4 border-b border-navy-100 pb-8 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="eyebrow mb-3"><span class="rule-gold mr-3"></span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h2 class="text-display-lg font-semibold text-navy">{{ $cat->name }}</h2>
                        @if ($cat->short_description)<p class="mt-3 text-pretty text-navy-500">{{ $cat->short_description }}</p>@endif
                    </div>
                </div>

                @if ($cat->services->isNotEmpty())
                    <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($cat->services as $service)
                            <a href="{{ route('services.show', $service->slug) }}" class="card card-hover group flex flex-col overflow-hidden" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                                <div class="relative aspect-[3/2] overflow-hidden bg-navy-100">
                                    @if ($service->image)
                                        <img src="{{ Storage::url($service->image) }}" alt="{{ $service->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                    @else
                                        <div class="h-full w-full bg-gradient-to-br from-navy-200 to-navy-100"></div>
                                    @endif
                                    @if ($service->is_featured)
                                        <span class="absolute left-4 top-4 rounded-full bg-gold px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-ink">{{ __('site.common.featured') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-6">
                                    <h3 class="font-display text-xl font-semibold text-navy">{{ $service->title }}</h3>
                                    @if ($service->short_description)<p class="mt-2 line-clamp-2 text-sm text-navy-500">{{ $service->short_description }}</p>@endif
                                    <div class="mt-6 flex items-end justify-between border-t border-navy-100 pt-5">
                                        <div>
                                            @if ($service->price > 0)
                                                <p class="font-mono text-[10px] uppercase tracking-wider text-navy-300">{{ __('site.common.from') }}</p>
                                                <p class="font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                            @else
                                                <p class="font-display text-lg font-semibold text-navy">{{ $id ? 'Hubungi kami' : 'Contact us' }}</p>
                                            @endif
                                        </div>
                                        <span class="grid h-10 w-10 place-items-center rounded-full border border-navy-200 transition-all group-hover:border-gold group-hover:bg-gold group-hover:text-ink">
                                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="mt-10 text-navy-400">{{ $id ? 'Layanan akan segera hadir.' : 'Services coming soon.' }}</p>
                @endif
            </div>
        </section>
    @empty
        <div class="container section text-navy-400">{{ $id ? 'Belum ada layanan.' : 'No services yet.' }}</div>
    @endforelse

    <x-cta-band />
</x-layout>
