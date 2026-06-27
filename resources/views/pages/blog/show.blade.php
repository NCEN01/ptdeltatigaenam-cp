@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="$post->title" :description="$post->excerpt" :ogImage="$post->featured_image ? Storage::url($post->featured_image) : null">
    <article>
        <x-page-header :eyebrow="optional($post->category)->name" :title="$post->title">
            <div class="mt-8 flex flex-wrap items-center gap-4 text-sm text-navy-200">
                @if ($post->author)<span>{{ $post->author->name }}</span><span class="text-white/20">·</span>@endif
                <span>{{ optional($post->published_at)->translatedFormat('d M Y') }}</span>
                @if ($post->tags->isNotEmpty())
                    <span class="text-white/20">·</span>
                    <span class="flex flex-wrap gap-2">
                        @foreach ($post->tags as $tag)<span class="rounded-full border border-white/15 px-3 py-1 font-mono text-[10px] uppercase tracking-wider">{{ $tag->name }}</span>@endforeach
                    </span>
                @endif
            </div>
        </x-page-header>

        @if ($post->featured_image)
            <div class="container -mt-10 md:-mt-16">
                <div class="overflow-hidden rounded-3xl shadow-lift">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full object-cover">
                </div>
            </div>
        @endif

        <section class="section">
            <div class="container max-w-3xl">
                <div class="prose prose-lg max-w-none text-navy-700 prose-headings:font-display prose-headings:text-navy prose-a:text-navy prose-img:rounded-2xl">
                    {!! $post->content !!}
                </div>
            </div>
        </section>
    </article>

    @if ($related->isNotEmpty())
        <section class="section bg-mist">
            <div class="container">
                <p class="eyebrow mb-8"><span class="rule-gold mr-3"></span>{{ $id ? 'Artikel terkait' : 'Related articles' }}</p>
                <div class="grid gap-8 md:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('blog.show', $r->slug) }}" class="group flex flex-col">
                            <div class="aspect-[16/10] overflow-hidden rounded-2xl bg-navy-100">
                                @if ($r->featured_image)<img src="{{ Storage::url($r->featured_image) }}" alt="{{ $r->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">@endif
                            </div>
                            <h3 class="mt-4 font-display text-lg font-semibold text-navy group-hover:text-navy-500">{{ $r->title }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
