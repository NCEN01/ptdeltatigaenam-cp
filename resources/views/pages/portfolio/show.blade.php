@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
    $coverUrl = $imgUrl($portfolio->cover_image);
@endphp

<x-layout :title="$portfolio->title" :description="$portfolio->short_description" :ogImage="$coverUrl">
    <x-page-header :eyebrow="$portfolio->category?->name" :title="$portfolio->title" :subtitle="$portfolio->short_description">
        <div class="mt-8 flex flex-wrap gap-6 font-mono text-xs uppercase tracking-wider text-navy-200">
            @if ($portfolio->client_name)<span>{{ $id ? 'Klien' : 'Client' }}: {{ $portfolio->client_name }}</span>@endif
            @if ($portfolio->project_date)<span>{{ $portfolio->project_date->translatedFormat('M Y') }}</span>@endif
        </div>
    </x-page-header>

    @if ($coverUrl)
        <div class="container -mt-10 md:-mt-16">
            <div class="overflow-hidden rounded-3xl shadow-lift ring-1 ring-navy-100">
                <img src="{{ $coverUrl }}" alt="{{ $portfolio->title }}" class="aspect-[16/9] w-full object-cover">
            </div>
        </div>
    @endif

    <section class="section">
        <div class="container max-w-3xl">
            @if ($portfolio->content)
                <div class="prose prose-lg max-w-none text-navy-700 prose-headings:font-display prose-headings:text-navy">{!! $portfolio->content !!}</div>
            @endif
        </div>

        @if ($portfolio->images->isNotEmpty())
            <div class="container mt-12">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($portfolio->images as $img)
                        <figure class="overflow-hidden rounded-2xl bg-navy-100" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                            <img src="{{ Storage::url($img->image) }}" alt="{{ $img->caption }}" loading="lazy" class="w-full object-cover">
                            @if ($img->caption)<figcaption class="p-3 text-xs text-navy-400">{{ $img->caption }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    @if ($related->isNotEmpty())
        <section class="section bg-mist">
            <div class="container">
                <p class="eyebrow mb-8"><span class="rule-gold mr-3"></span>{{ $id ? 'Proyek lain' : 'More projects' }}</p>
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('portfolio.show', $r->slug) }}" class="card card-hover group overflow-hidden">
                            <div class="aspect-[4/3] overflow-hidden bg-navy-100">
                                @if ($imgUrl($r->cover_image))<img src="{{ $imgUrl($r->cover_image) }}" alt="{{ $r->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">@else<div class="absolute inset-0 aurora opacity-60"></div>@endif
                            </div>
                            <div class="p-5">
                                @if ($r->client_name)<p class="font-mono text-[10px] uppercase tracking-wider text-sky-600">{{ $r->client_name }}</p>@endif
                                <h3 class="mt-1 font-display text-lg font-semibold text-navy transition-colors group-hover:text-sky-600">{{ $r->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
