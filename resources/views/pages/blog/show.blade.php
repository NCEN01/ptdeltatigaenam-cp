@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
    $cover = $imgUrl($post->featured_image);

    // Other articles across the site — lets readers jump directly to more posts.
    $morePosts = \App\Models\BlogPost::published()
        ->where('id', '!=', $post->id)
        ->latest('published_at')
        ->take(6)
        ->get();
@endphp

<x-layout :title="$post->title" :description="$post->excerpt" :ogImage="$cover">
    <article>
        <x-page-header :eyebrow="optional($post->category)->name" :title="$post->title">
            <div class="mt-8 flex flex-wrap items-center justify-center gap-4 text-sm text-navy-200">
                <span>{{ optional($post->published_at)->translatedFormat('d M Y') }}</span>
                @if ($post->tags->isNotEmpty())
                    <span class="text-white/20">·</span>
                    <span class="flex flex-wrap justify-center gap-2">
                        @foreach ($post->tags as $tag)<span class="rounded-full border border-white/15 px-3 py-1 font-mono text-[10px] uppercase tracking-wider">{{ $tag->name }}</span>@endforeach
                    </span>
                @endif
            </div>
        </x-page-header>

        <section class="section bg-white">
            <div class="container">
                @if ($cover)
                    <figure class="mb-12 overflow-hidden rounded-3xl shadow-card ring-1 ring-navy-100" data-aos="fade-up">
                        <img src="{{ $cover }}" alt="{{ $post->title }}" class="aspect-[16/9] w-full object-cover object-center">
                    </figure>
                @endif

                <div class="grid gap-10 lg:grid-cols-12 lg:gap-14">

                {{-- Article --}}
                <div class="lg:col-span-8">
                    {{-- Meta bar --}}
                    <div class="mb-10 flex flex-wrap items-center justify-between gap-4 border-b border-navy-100 pb-6">
                        <a href="{{ route('blog.index') }}" class="group inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition-colors hover:text-navy">
                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $id ? 'Kembali ke Blog' : 'Back to Blog' }}
                        </a>
                        <div class="flex items-center gap-3 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                            @if ($post->category)<span class="text-gold-deep">{{ $post->category->name }}</span><span class="text-navy-200">·</span>@endif
                            <span>{{ optional($post->published_at)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- Lead --}}
                    @if ($post->excerpt)
                        <p class="mb-10 border-l-2 border-gold pl-5 text-lg leading-relaxed text-slate-700 text-pretty">{{ $post->excerpt }}</p>
                    @endif

                    {{-- Body (comfortable reading measure & rhythm) --}}
                    <div class="prose prose-lg max-w-none [hyphens:auto]
                                prose-headings:font-display prose-headings:font-normal prose-headings:text-navy prose-headings:tracking-tight
                                prose-p:text-slate-700 prose-p:leading-[1.85] prose-p:text-justify
                                prose-li:text-slate-700 prose-li:leading-relaxed
                                prose-a:font-medium prose-a:text-sky-700 prose-a:no-underline hover:prose-a:underline
                                prose-strong:text-navy
                                prose-blockquote:border-l-2 prose-blockquote:border-gold prose-blockquote:not-italic prose-blockquote:text-slate-700
                                prose-img:mx-auto prose-img:my-8 prose-img:h-auto prose-img:max-h-[520px] prose-img:w-auto prose-img:rounded-2xl prose-img:shadow-card prose-img:ring-1 prose-img:ring-navy-100
                                prose-figure:mx-auto prose-figure:text-center prose-figcaption:text-slate-500
                                [&_img]:mx-auto">
                        {!! \App\Helpers\HtmlSanitizer::clean($post->content) !!}
                    </div>

                    {{-- Footer --}}
                    <div class="mt-12 flex flex-wrap items-center justify-between gap-4 border-t border-navy-100 pt-8">
                        @if ($post->tags->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach ($post->tags as $tag)
                                    <span class="rounded-full border border-navy-100 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-slate-600">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @else<span></span>@endif
                        <a href="{{ route('blog.index') }}" class="link-underline text-sm font-medium">{{ $id ? 'Semua artikel' : 'All articles' }}</a>
                    </div>
                </div>

                {{-- Sidebar: jump to other articles --}}
                <aside class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28">
                        <div class="rounded-3xl border border-navy-100 bg-neutral-50 p-6">
                            <p class="eyebrow mb-5"><span class="rule-gold mr-3"></span>{{ $id ? 'Artikel Lainnya' : 'More Articles' }}</p>

                            @if ($morePosts->isNotEmpty())
                                <div class="space-y-1.5">
                                    @foreach ($morePosts as $mp)
                                        <a href="{{ route('blog.show', $mp->slug) }}" class="group flex gap-3 rounded-2xl p-2 transition-colors duration-200 hover:bg-white">
                                            <span class="h-14 w-16 shrink-0 overflow-hidden rounded-xl bg-navy-900">
                                                @if ($imgUrl($mp->featured_image))
                                                    <img src="{{ $imgUrl($mp->featured_image) }}" alt="{{ $mp->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                                                @else
                                                    <span class="grid h-full w-full place-items-center text-sky-400"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg></span>
                                                @endif
                                            </span>
                                            <span class="min-w-0 flex-1">
                                                <span class="line-clamp-2 font-display text-sm leading-snug text-navy transition-colors duration-200 group-hover:text-sky-700">{{ $mp->title }}</span>
                                                <span class="mt-1 block font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ optional($mp->published_at)->translatedFormat('d M Y') }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-500">{{ $id ? 'Belum ada artikel lain.' : 'No other articles yet.' }}</p>
                            @endif

                            <a href="{{ route('blog.index') }}" class="mt-5 flex items-center justify-center gap-2 rounded-full border border-navy-200 py-2.5 text-sm font-medium text-navy transition-all duration-200 hover:border-gold hover:bg-gold hover:text-navy-950">
                                {{ $id ? 'Lihat Semua Artikel' : 'View All Articles' }}
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </div>
                </aside>
                </div>
            </div>
        </section>
    </article>

    @if ($related->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-neutral-50">
            <div class="container">
                <p class="eyebrow mb-8" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Artikel terkait' : 'Related articles' }}</p>
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($related as $r)
                        <a href="{{ route('blog.show', $r->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">
                            <div class="relative aspect-[16/10] overflow-hidden bg-navy-900">
                                @if ($imgUrl($r->featured_image))
                                    <img src="{{ $imgUrl($r->featured_image) }}" alt="{{ $r->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                                @else
                                    <div class="absolute inset-0 aurora opacity-60"></div>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <p class="font-mono text-[10px] uppercase tracking-wider text-slate-500">{{ optional($r->published_at)->translatedFormat('d M Y') }}</p>
                                <h3 class="mt-2 line-clamp-2 font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $r->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
