@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.services')">
    <x-page-header
        :eyebrow="__('site.home.services_kicker')"
        :title="__('site.home.services_title')"
        :subtitle="$id ? 'Dari konsultasi manajemen hingga sertifikasi kompetensi, dirancang untuk hasil yang terukur.' : 'From management consulting to competency certification, designed for measurable outcomes.'"
        placement="services"
        image="photo-1524178232363-1fb2b075b655">
        {{-- Category jump-nav. Highlights the section you are currently reading, so on a page
             of seven stacked sections the chips act as a position indicator, not just links.
             Mobile is a scroll-snap strip rather than the old auto-running marquee: chips that
             drift sideways are hard to hit, and `hover:pause` does not fire on touch. --}}
        <nav class="mt-8 w-full sm:mt-10" aria-label="{{ $id ? 'Kategori layanan' : 'Service categories' }}"
             x-data="sectionNav(@js($categories->pluck('slug')->all()))">
            <ul class="mask-fade-x -mx-5 flex snap-x snap-mandatory gap-2 overflow-x-auto px-5 pb-1 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:mx-0 sm:flex-wrap sm:[mask-image:none] sm:justify-center sm:overflow-visible sm:px-0">
                @foreach ($categories as $cat)
                    <li class="shrink-0 snap-start sm:shrink" x-data="{ slug: @js($cat->slug) }">
                        <a href="#{{ $cat->slug }}"
                           class="block whitespace-nowrap rounded-full border px-4 py-2 text-sm transition-all duration-300 ease-out-soft"
                           :class="active === slug
                               ? 'border-white bg-white text-navy font-medium'
                               : 'border-white/15 text-navy-100 hover:border-white/50 hover:text-white'"
                           :aria-current="active === slug ? 'true' : null">{{ $cat->name }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </x-page-header>

    @forelse ($categories as $cat)
        <section id="{{ $cat->slug }}" class="scroll-mt-28 py-14 md:py-20 {{ $loop->odd ? '' : 'bg-mist' }}">
            <div class="container">
                <div class="flex flex-col gap-4 border-b border-navy-100 pb-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="mb-3 font-mono text-[11px] uppercase tracking-normal text-gold-deep"><span class="rule-gold mr-3"></span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h2 class="text-3xl font-semibold text-navy md:text-4xl">{{ $cat->name }}</h2>
                        @if ($cat->short_description)<p class="mt-3 text-pretty text-slate-600">{{ $cat->short_description }}</p>@endif
                    </div>
                </div>

                @if ($cat->services->isNotEmpty())
                    <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3" data-stagger>
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
                                    {{-- Judul dan deskripsi dibungkus satu blok yang tumbuh (flex-1),
                                         sehingga sisa ruang kartu diserap di sini dan baris harga
                                         selalu mendarat di dasar kartu. Tanpa ini, harga menempel
                                         tepat di bawah deskripsi, jadi kartu berjudul dua baris
                                         menampilkan harganya lebih rendah daripada tetangganya.

                                         Judul dijatah dua baris agar bagian atas kartu juga rata,
                                         bukan hanya harganya. --}}
                                    <div class="flex-1">
                                        <h3 class="line-clamp-2 min-h-14 font-display text-xl font-semibold leading-snug text-navy">{{ $service->title }}</h3>
                                        @if ($service->short_description)<p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $service->short_description }}</p>@endif
                                    </div>
                                    <div class="mt-6 flex items-end justify-between border-t border-navy-100 pt-5">
                                        <div>
                                            @if ($service->price > 0)
                                                @if ($service->hasDiscount())
                                                    <p class="flex flex-wrap items-center gap-1.5">
                                                        <span class="font-mono text-[11px] text-slate-400 line-through">Rp {{ number_format((float) $service->discount_original_price, 0, ',', '.') }}</span>
                                                        <span class="rounded bg-rose-100 px-1.5 py-0.5 font-mono text-[10px] font-bold text-rose-600">-{{ $service->discountPercent() }}%</span>
                                                    </p>
                                                    <p class="font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @else
                                                    <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ __('site.common.from') }}</p>
                                                    <p class="font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $service->price, 0, ',', '.') }}</p>
                                                @endif
                                            @else
                                                {{-- Diberi baris label juga, supaya ketiga bentuk harga
                                                     sama-sama dua baris. Kalau yang satu ini satu baris,
                                                     kartunya jadi lebih pendek dan harga tetangganya
                                                     tidak sejajar meski blok ini sudah didorong ke dasar. --}}
                                                <p class="font-mono text-[10px] uppercase tracking-wider text-slate-400">{{ $id ? 'Investasi' : 'Investment' }}</p>
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
                    <div class="mt-10 rounded-3xl border border-dashed border-navy-200 bg-white p-12 text-center">
                        <p class="font-display text-lg font-semibold text-navy">{{ $id ? 'Layanan akan segera hadir' : 'Services coming soon' }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Kami sedang menyiapkan layanan untuk kategori ini.' : 'We are preparing services for this category.' }}</p>
                    </div>
                @endif
            </div>
        </section>
    @empty
        <section class="section">
            <div class="container">
                <div class="rounded-3xl border border-dashed border-navy-200 bg-mist p-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-gold">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </span>
                    <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Belum ada layanan' : 'No services yet' }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Daftar layanan akan ditampilkan di sini setelah tersedia.' : 'Our service catalog will appear here once available.' }}</p>
                </div>
            </div>
        </section>
    @endforelse

</x-layout>
