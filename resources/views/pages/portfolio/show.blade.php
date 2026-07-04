@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
    $coverUrl = $imgUrl($portfolio->cover_image);
@endphp

<x-layout :title="$portfolio->title" :description="$portfolio->short_description" :ogImage="$coverUrl">
    <x-page-header :eyebrow="$portfolio->category?->name" :title="$portfolio->title" />

    <section class="section bg-white">
        <div class="container">
            {{-- Back link --}}
            <a href="{{ route('portfolio.index') }}" class="group mb-8 inline-flex items-center gap-2 text-sm font-medium text-navy-500 transition-colors hover:text-navy">
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $id ? 'Kembali ke Portofolio' : 'Back to Portfolio' }}
            </a>

            <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">

            {{-- Main content --}}
            <div class="lg:col-span-8">
                {{-- Cover — sits with the details, aligned to the sidebar --}}
                @if ($coverUrl)
                    <figure class="mb-8 overflow-hidden rounded-3xl shadow-card ring-1 ring-navy-100" data-aos="fade-up">
                        <img src="{{ $coverUrl }}" alt="{{ $portfolio->title }}" class="aspect-[16/10] w-full object-cover object-center">
                    </figure>
                @endif

                @if ($portfolio->short_description)
                    <p class="mb-8 border-l-2 border-gold pl-5 text-lg leading-relaxed text-navy-600 text-pretty">{{ $portfolio->short_description }}</p>
                @endif

                @if ($portfolio->content)
                    <div class="prose prose-lg max-w-none [hyphens:auto]
                                prose-headings:font-display prose-headings:font-normal prose-headings:text-navy
                                prose-p:text-navy-600 prose-p:leading-[1.85] prose-p:text-justify
                                prose-li:text-navy-600 prose-a:text-sky-700 prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                                prose-strong:text-navy
                                prose-blockquote:border-l-2 prose-blockquote:border-gold prose-blockquote:not-italic prose-blockquote:text-navy-600
                                prose-img:rounded-2xl prose-img:shadow-card">
                        {!! $portfolio->content !!}
                    </div>
                @endif
            </div>

            {{-- Sidebar: project facts + CTA --}}
            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    {{-- Facts --}}
                    <div class="rounded-3xl border border-navy-100 bg-neutral-50 p-6">
                        <p class="eyebrow mb-5"><span class="rule-gold mr-3"></span>{{ $id ? 'Detail Proyek' : 'Project Details' }}</p>
                        <dl class="divide-y divide-navy-100">
                            @if ($portfolio->client_name)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-navy-400">{{ $id ? 'Klien' : 'Client' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->client_name }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->category)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-navy-400">{{ $id ? 'Kategori' : 'Category' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->category->name }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->project_date)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-navy-400">{{ $id ? 'Tanggal' : 'Date' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->project_date->translatedFormat('d M Y') }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->images->isNotEmpty())
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-navy-400">{{ $id ? 'Dokumentasi' : 'Gallery' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->images->count() }} {{ $id ? 'foto' : 'photos' }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- CTA --}}
                    <div class="relative overflow-hidden rounded-3xl bg-navy-950 p-7 text-white">
                        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                        <div class="relative">
                            <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-gold-soft">{{ $id ? 'Tertarik?' : 'Interested?' }}</p>
                            <p class="mt-3 font-display text-xl leading-snug text-balance">{{ $id ? 'Wujudkan proyek serupa untuk organisasi Anda.' : 'Bring a similar project to life for your team.' }}</p>
                            <a href="{{ route('contact.index') }}" class="btn-blue mt-6 w-full justify-center">{{ $id ? 'Konsultasi Gratis' : 'Free Consultation' }}</a>
                        </div>
                    </div>
                </div>
            </aside>
            </div>
        </div>

        {{-- Gallery (masonry) --}}
        @if ($portfolio->images->isNotEmpty())
            <div class="container mt-16">
                <p class="eyebrow mb-8" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Galeri Proyek' : 'Project Gallery' }}</p>
                <div class="columns-1 gap-4 sm:columns-2 lg:columns-3">
                    @foreach ($portfolio->images as $img)
                        <figure class="group mb-4 break-inside-avoid overflow-hidden rounded-2xl border border-navy-100 bg-navy-900 shadow-card" data-aos="fade-up">
                            <img src="{{ $imgUrl($img->image) }}" alt="{{ $img->caption }}" loading="lazy" class="w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                            @if ($img->caption)<figcaption class="bg-white px-4 py-3 text-xs text-navy-500">{{ $img->caption }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    @if ($related->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-neutral-50">
            <div class="container">
                <p class="eyebrow mb-8" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Proyek lain' : 'More projects' }}</p>
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('portfolio.show', $r->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift">
                            <div class="relative aspect-[4/3] shrink-0 overflow-hidden bg-navy-900">
                                @if ($imgUrl($r->cover_image))<img src="{{ $imgUrl($r->cover_image) }}" alt="{{ $r->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">@else<div class="absolute inset-0 aurora opacity-60"></div>@endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                @if ($r->client_name)<p class="font-mono text-[10px] uppercase tracking-wider text-gold-deep">{{ $r->client_name }}</p>@endif
                                <h3 class="mt-1.5 line-clamp-2 font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $r->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
