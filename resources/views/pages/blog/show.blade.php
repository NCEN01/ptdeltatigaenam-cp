@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $cover = $post->featured_image
        ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : Storage::url($post->featured_image))
        : null;
    $shareUrl = route('blog.show', $post->slug);
@endphp

<x-layout :title="$post->title" :description="$post->excerpt" :ogImage="$cover">
    <article>
        {{-- The page header already wraps this slot in a centred flex row. --}}
        <x-page-header :eyebrow="optional($post->category)->name" :title="$post->title">
            <span class="text-sm text-navy-200">{{ optional($post->published_at)->translatedFormat('d M Y') }}</span>

            <x-meta-location :value="$post->location" tone="text-gold-soft" size="h-4 w-4" class="text-sm" />

            <span class="text-white/20" aria-hidden="true">·</span>
            <span class="text-sm text-navy-200">{{ $post->readingMinutes() }} {{ $id ? 'menit baca' : 'min read' }}</span>

            @if ($post->tags->isNotEmpty())
                <span class="text-white/20" aria-hidden="true">·</span>
                @foreach ($post->tags as $tag)<span class="rounded-full border border-white/15 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-navy-200">{{ $tag->name }}</span>@endforeach
            @endif
        </x-page-header>

        <section class="section bg-white">
            <div class="container">
                {{-- No row gap below lg: the sidebar's contents are either hidden or
                     fixed there, so it collapses to zero height and the gap would show. --}}
                <div class="grid gap-10 max-lg:gap-y-0 lg:grid-cols-12 lg:gap-14">

                    {{-- Sidebar: table of contents + share. Sits above the body on mobile. --}}
                    <aside class="order-1 lg:order-2 lg:col-span-4">
                        {{-- Sticky reading companion. It needs the <aside> to keep its default
                             stretch height, so no `self-start` here. Long lists scroll inside. --}}
                        <div class="space-y-5 lg:sticky lg:top-28 lg:max-h-[calc(100dvh-8rem)] lg:overflow-y-auto lg:pr-1">
                            <x-article-toc :items="$toc" />

                            {{-- Desktop only: on mobile the end-of-article block already covers sharing. --}}
                            <div class="hidden px-6 lg:block">
                                <div class="border-t border-navy-100 pt-5">
                                    <p class="mb-4 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">
                                        {{ $id ? 'Bagikan Artikel' : 'Share this article' }}
                                    </p>
                                    <x-share-buttons :url="$shareUrl" :title="$post->title" />
                                </div>
                            </div>
                        </div>
                    </aside>

                    {{-- Article --}}
                    <div class="order-2 lg:order-1 lg:col-span-8">
                        {{-- Meta bar --}}
                        <div class="mb-10 flex flex-wrap items-center justify-between gap-4 border-b border-navy-100 pb-6">
                            <a href="{{ route('blog.index') }}" class="group inline-flex items-center gap-2 text-sm font-medium text-slate-600 transition-colors hover:text-navy">
                                <svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-x-0.5" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M13 8H3M7 4L3 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                {{ $id ? 'Kembali ke Blog' : 'Back to Blog' }}
                            </a>
                            <div class="flex flex-wrap items-center gap-3 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                                @if ($post->category)<span class="text-gold-deep">{{ $post->category->name }}</span><span class="text-navy-200" aria-hidden="true">·</span>@endif
                                <span>{{ optional($post->published_at)->translatedFormat('d M Y') }}</span>
                                <x-meta-location :value="$post->location" size="h-3.5 w-3.5" />
                            </div>
                        </div>

                        {{-- Cover --}}
                        @if ($cover)
                            <figure class="mb-10 overflow-hidden rounded-3xl shadow-card ring-1 ring-navy-100" data-aos="fade-up">
                                <img src="{{ $cover }}" alt="{{ $post->title }}" class="aspect-[16/10] w-full object-cover object-center">
                            </figure>
                        @endif

                        {{-- Lead --}}
                        @if ($post->excerpt)
                            <p class="mb-10 border-l-2 border-gold pl-5 text-lg leading-relaxed text-slate-700 text-pretty">{{ $post->excerpt }}</p>
                        @endif

                        {{-- Body — headings carry anchors injected by TableOfContents --}}
                        <div id="article-body"
                             class="prose prose-lg max-w-none [hyphens:auto]
                                    prose-headings:font-display prose-headings:font-normal prose-headings:text-navy prose-headings:tracking-tight
                                    prose-headings:scroll-mt-28
                                    prose-p:text-slate-700 prose-p:leading-[1.85]
                                    prose-p:text-left sm:prose-p:text-justify
                                    prose-li:text-slate-700 prose-li:leading-relaxed
                                    prose-a:font-medium prose-a:text-sky-700 prose-a:no-underline hover:prose-a:underline
                                    prose-strong:text-navy
                                    prose-blockquote:border-l-2 prose-blockquote:border-gold prose-blockquote:not-italic prose-blockquote:text-slate-700
                                    prose-img:mx-auto prose-img:my-8 prose-img:h-auto prose-img:max-h-[520px] prose-img:w-auto prose-img:rounded-2xl prose-img:shadow-card prose-img:ring-1 prose-img:ring-navy-100
                                    prose-figure:mx-auto prose-figure:text-center prose-figcaption:text-slate-500
                                    [&_img]:mx-auto">
                            {!! $articleHtml !!}
                        </div>

                        {{-- Share again once the reader reaches the end --}}
                        <div class="mt-12 rounded-2xl border border-navy-100 bg-neutral-50 p-6">
                            <p class="mb-4 text-sm font-semibold text-navy">
                                {{ $id ? 'Bermanfaat? Bagikan artikel ini' : 'Found this useful? Share it' }}
                            </p>
                            <x-share-buttons :url="$shareUrl" :title="$post->title" />
                        </div>

                        {{-- Footer --}}
                        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-navy-100 pt-8">
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
                </div>
            </div>
        </section>
    </article>

    {{-- Every recent article, not just the ones sharing this category --}}
    @if ($latestPosts->isNotEmpty())
        <section class="section-sm border-t border-navy-50 bg-neutral-50">
            {{-- The arrows and dots must sit in the slider's parent element — that is
                 where the shared [data-carousel] handler looks for them. --}}
            <div class="container">
                <div class="mb-10 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="kicker mb-3" data-aos="fade-up">{{ $id ? 'Terus Membaca' : 'Keep Reading' }}</p>
                        <h2 class="font-display text-3xl text-navy text-balance md:text-4xl" data-aos="fade-up">{{ $id ? 'Artikel terbaru lainnya' : 'More recent articles' }}</h2>
                    </div>

                    <div class="flex shrink-0 items-center gap-5" data-aos="fade-up">
                        <a href="{{ route('blog.index') }}" class="link-underline text-sm font-medium">
                            {{ $id ? 'Lihat semua artikel' : 'View all articles' }}
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>

                        {{-- Same arrow treatment as the home page carousels (data-hscroll). --}}
                        <div class="flex items-center gap-2">
                            <button type="button" data-carousel-prev
                                    aria-label="{{ $id ? 'Artikel sebelumnya' : 'Previous articles' }}"
                                    class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 text-white transition hover:-translate-y-0.5 hover:from-navy-600 hover:to-navy-500 active:scale-95 disabled:pointer-events-none disabled:opacity-35">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button type="button" data-carousel-next
                                    aria-label="{{ $id ? 'Artikel berikutnya' : 'Next articles' }}"
                                    class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-navy-500 to-sky-400 text-white transition hover:-translate-y-0.5 hover:from-navy-600 hover:to-navy-500 active:scale-95 disabled:pointer-events-none disabled:opacity-35">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M6 3l5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- One swipeable row. Cards keep equal height via items-stretch + h-auto. --}}
                <div class="swiper swiper-equal-height" data-carousel data-carousel-autoplay="false">
                    <div class="swiper-wrapper items-stretch">
                        @foreach ($latestPosts as $r)
                            <div class="swiper-slide h-auto">
                                <x-blog-card :post="$r" :animate="false" />
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 flex justify-center gap-2" data-carousel-pagination></div>
            </div>
        </section>
    @endif
</x-layout>
