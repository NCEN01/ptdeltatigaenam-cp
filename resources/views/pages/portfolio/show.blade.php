@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
    $coverUrl = $imgUrl($portfolio->cover_image);
@endphp

<x-layout :title="$portfolio->title" :description="$portfolio->short_description" :ogImage="$coverUrl">
    {{-- The cover becomes the hero. It used to sit as a small figure inside the content
         column while the header fell back to the generic aurora, so the strongest asset on
         the page was shown small in one place and not at all in the other. --}}
    <x-page-header :eyebrow="$portfolio->category?->name" :title="$portfolio->title"
                   :subtitle="$portfolio->client_name" :image="$coverUrl" />

    <section class="section bg-white">
        <div class="container">
            {{-- Back link --}}
            <a href="{{ route('portfolio.index') }}" class="group mb-8 inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition-colors hover:text-navy">
                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $id ? 'Kembali ke Portofolio' : 'Back to Portfolio' }}
            </a>

            <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">

            {{-- Main content --}}
            <div class="lg:col-span-8">
                @if ($portfolio->short_description)
                    {{-- Lead paragraph, no gold side-stripe: a coloured left border reads as a
                         leftover callout rather than a decision. Size carries the emphasis.
                         Justify only from sm — it opens rivers in a narrow column. --}}
                    <p class="mb-8 text-lg leading-relaxed text-navy [hyphens:auto] sm:text-justify">{{ $portfolio->short_description }}</p>
                @endif

                @if ($portfolio->content)
                    <div class="prose prose-lg max-w-none [hyphens:auto]
                                prose-headings:font-display prose-headings:font-normal prose-headings:text-navy
                                prose-p:text-slate-700 prose-p:leading-[1.85]
                                prose-p:text-left sm:prose-p:text-justify
                                prose-li:text-slate-700 prose-a:text-sky-700 prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                                prose-strong:text-navy
                                prose-blockquote:border-0 prose-blockquote:bg-navy-50/70 prose-blockquote:px-6 prose-blockquote:py-4 prose-blockquote:rounded-xl prose-blockquote:not-italic prose-blockquote:text-navy
                                prose-img:rounded-2xl prose-img:shadow-card">
                        {!! \App\Helpers\HtmlSanitizer::clean($portfolio->content) !!}
                    </div>
                @endif
            </div>

            {{-- Sidebar: project facts + CTA --}}
            <aside class="lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    {{-- Facts --}}
                    <div class="rounded-2xl border border-navy-100 bg-neutral-50 p-6">
                        <p class="kicker mb-5"><span class="rule-gold mr-3"></span>{{ $id ? 'Detail Proyek' : 'Project Details' }}</p>
                        <dl class="divide-y divide-navy-100">
                            @if ($portfolio->client_name)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Klien' : 'Client' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->client_name }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->category)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Kategori' : 'Category' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->category->name }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->project_date)
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Tanggal' : 'Date' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->project_date->translatedFormat('d M Y') }}</dd>
                                </div>
                            @endif
                            @if ($portfolio->images->isNotEmpty())
                                <div class="flex items-start justify-between gap-4 py-3">
                                    <dt class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ $id ? 'Dokumentasi' : 'Gallery' }}</dt>
                                    <dd class="text-right text-sm font-semibold text-navy">{{ $portfolio->images->count() }} {{ $id ? 'foto' : 'photos' }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    {{-- CTA --}}
                    <div class="relative overflow-hidden rounded-2xl bg-navy-950 p-7 text-white">
                        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-40"></div>
                        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                        <div class="relative">
                            <p class="font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $id ? 'Tertarik?' : 'Interested?' }}</p>
                            <p class="mt-3 font-display text-xl leading-snug text-balance">{{ $id ? 'Wujudkan proyek serupa untuk organisasi Anda.' : 'Bring a similar project to life for your team.' }}</p>
                            <a href="{{ route('contact.index') }}" class="btn-blue mt-6 w-full justify-center">{{ $id ? 'Konsultasi Gratis' : 'Free Consultation' }}</a>
                        </div>
                    </div>
                </div>
            </aside>
            </div>
        </div>

        {{-- Client voice. The relation was already eager-loaded in the controller but never
             rendered, so this data was fetched and discarded on every request. --}}
        @php $quotes = $portfolio->testimonials->where('is_active', true); @endphp
        @if ($quotes->isNotEmpty())
            <div class="container mt-16">
                <div class="grid gap-6 lg:grid-cols-2">
                    @foreach ($quotes as $quote)
                        <figure class="relative overflow-hidden rounded-2xl bg-navy-950 p-8 text-white md:p-10" data-aos="fade-up">
                            <div class="pointer-events-none absolute inset-0 aurora opacity-30"></div>
                            <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/40 to-transparent"></div>
                            <div class="relative">
                                <svg class="h-8 w-8 text-gold-soft/60" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9.5 5C6.5 6.6 4.7 9.4 4.7 12.7c0 3.2 1.9 5.3 4.4 5.3 2.2 0 3.8-1.6 3.8-3.7 0-2-1.4-3.5-3.3-3.5-.4 0-.8.1-1 .2.3-1.6 1.6-3.2 3.4-4.2L9.5 5zm9.6 0c-3 1.6-4.8 4.4-4.8 7.7 0 3.2 1.9 5.3 4.4 5.3 2.2 0 3.8-1.6 3.8-3.7 0-2-1.4-3.5-3.3-3.5-.4 0-.8.1-1 .2.3-1.6 1.6-3.2 3.4-4.2L19.1 5z"/></svg>
                                <blockquote class="mt-5">
                                    <p class="text-pretty text-lg leading-relaxed text-white md:text-xl">{{ $quote->content }}</p>
                                </blockquote>
                                <figcaption class="mt-7 flex items-center gap-4 border-t border-white/10 pt-6">
                                    @if ($quote->author_photo)
                                        <img src="{{ $imgUrl($quote->author_photo) }}" alt="" loading="lazy" class="h-11 w-11 shrink-0 rounded-full object-cover ring-1 ring-white/20">
                                    @endif
                                    <span class="min-w-0">
                                        <span class="block font-display text-base font-semibold text-white">{{ $quote->author_name }}</span>
                                        <span class="mt-0.5 block text-sm text-navy-200">{{ collect([$quote->author_position, $quote->author_company])->filter()->implode(' · ') }}</span>
                                    </span>
                                </figcaption>
                            </div>
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Gallery (masonry) --}}
        @if ($portfolio->images->isNotEmpty())
            <div class="container mt-16">
                {{-- Was .eyebrow, which is display:none site-wide — the gallery shipped with no
                     heading at all, so the photos appeared without introduction. --}}
                <h2 class="font-display text-2xl font-semibold text-navy md:text-3xl" data-aos="fade-up">{{ $id ? 'Galeri Proyek' : 'Project Gallery' }}</h2>
                <span class="mt-4 mb-8 block h-0.5 w-14 rounded-full bg-gradient-to-r from-gold to-gold-soft" aria-hidden="true"></span>
                <div class="columns-1 gap-4 sm:columns-2 lg:columns-3">
                    @foreach ($portfolio->images as $img)
                        <figure class="group mb-4 break-inside-avoid overflow-hidden rounded-2xl border border-navy-100 bg-navy-900 shadow-card" data-aos="fade-up">
                            <img src="{{ $imgUrl($img->image) }}" alt="{{ $img->caption ?: $portfolio->title }}" loading="lazy" decoding="async" class="w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                            @if ($img->caption)<figcaption class="bg-white px-4 py-3 text-xs text-slate-600">{{ $img->caption }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    @if ($related->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-neutral-50">
            <div class="container">
                <p class="kicker mb-8" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Portofolio terkait' : 'Related portfolio' }}</p>
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
