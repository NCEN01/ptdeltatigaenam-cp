@php
    use Illuminate\Support\Facades\Storage;
    $id = app()->getLocale() === 'id';
    $imgUrl = fn ($v) => $v ? (str_starts_with($v, 'http') ? $v : Storage::url($v)) : null;
@endphp

<x-layout :title="__('site.nav.blog')">
    <x-page-header
        :eyebrow="__('site.home.blog_kicker')"
        :title="__('site.home.blog_title')"
        :subtitle="$id ? 'Perspektif, riset, dan praktik terbaik seputar human capital.' : 'Perspectives, research, and best practices on human capital.'"
        image="photo-1499750310107-5fef28a66643" />

    <section class="section bg-white">
        <div class="container">
            {{-- Section heading --}}
            <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Artikel Terbaru' : 'Latest Articles' }}</p>
                    <h2 class="font-display text-3xl text-navy text-balance md:text-4xl" data-aos="fade-up">{{ $id ? 'Wawasan & artikel kami' : 'Our insights & articles' }}</h2>
                </div>
                @if ($posts->total())
                    <p class="font-mono text-sm text-navy-400" data-aos="fade-up">{{ str_pad($posts->total(), 2, '0', STR_PAD_LEFT) }} {{ $id ? 'artikel' : 'articles' }}</p>
                @endif
            </div>

            {{-- Uniform grid — every card the same height (auto-rows-fr), 3 across then wraps down --}}
            @if ($posts->count())
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 70 }}">
                            <div class="relative aspect-[16/10] shrink-0 overflow-hidden bg-navy-900">
                                @if ($imgUrl($post->featured_image))
                                    <img src="{{ $imgUrl($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                                @else
                                    <div class="absolute inset-0 aurora opacity-60"></div>
                                @endif
                                @if ($post->category)
                                    <span class="absolute left-4 top-4 rounded-full bg-white/90 px-2.5 py-1 font-mono text-[9px] uppercase tracking-wider text-navy backdrop-blur">{{ $post->category->name }}</span>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <p class="font-mono text-[11px] uppercase tracking-wider text-navy-400">{{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                                <h3 class="mt-2.5 line-clamp-2 min-h-[3.25rem] font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $post->title }}</h3>
                                <p class="mt-2 line-clamp-3 text-pretty text-sm leading-relaxed text-navy-500">{{ $post->excerpt }}</p>
                                <span class="mt-auto flex items-center gap-2 border-t border-navy-100 pt-4 text-sm font-medium text-navy">
                                    {{ $id ? 'Baca artikel' : 'Read article' }}
                                    <svg class="h-4 w-4 text-gold-deep transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-navy-200 bg-neutral-50 p-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-sky-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9 9h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </span>
                    <p class="mt-5 font-display text-lg text-navy">{{ $id ? 'Belum ada artikel' : 'No articles yet' }}</p>
                    <p class="mt-2 text-sm text-navy-500">{{ $id ? 'Artikel & wawasan terbaru akan tampil di sini.' : 'Our latest articles and insights will appear here.' }}</p>
                </div>
            @endif

            @if ($posts->hasPages())
                <div class="mt-14">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>
</x-layout>
