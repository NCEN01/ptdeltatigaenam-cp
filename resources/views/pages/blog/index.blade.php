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
        placement="blog"
        image="photo-1499750310107-5fef28a66643" />

    <section class="section bg-white">
        <div class="container">
            {{-- Section heading + search --}}
            <div class="mb-12 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Artikel Terbaru' : 'Latest Articles' }}</p>
                    <h2 class="font-display text-3xl text-navy text-balance md:text-4xl" data-aos="fade-up">{{ $id ? 'Wawasan & artikel kami' : 'Our insights & articles' }}</h2>
                </div>
                <div class="flex shrink-0 flex-col items-start gap-2.5 md:items-end" data-aos="fade-up">
                    <form action="{{ route('blog.index') }}" method="GET" role="search" class="relative w-full sm:w-72">
                        <input type="search" name="q" value="{{ $q ?? '' }}" autocomplete="off"
                               placeholder="{{ $id ? 'Cari artikel…' : 'Search articles…' }}"
                               class="w-full rounded-full border border-navy-200 bg-white py-2.5 pl-5 pr-12 text-sm text-navy placeholder:text-slate-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/25">
                        <button type="submit" aria-label="{{ $id ? 'Cari artikel' : 'Search articles' }}"
                                class="absolute right-1.5 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-full bg-sky-600 text-white transition hover:bg-sky-700 active:scale-95">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        </button>
                    </form>
                    @if (($q ?? '') !== '')
                        <p class="font-mono text-xs text-slate-500">{{ $posts->total() }} {{ $id ? 'hasil untuk' : 'results for' }} “{{ $q }}” · <a href="{{ route('blog.index') }}" class="text-sky-600 hover:underline">{{ $id ? 'reset' : 'reset' }}</a></p>
                    @elseif ($posts->total())
                        <p class="font-mono text-xs text-slate-500">{{ str_pad($posts->total(), 2, '0', STR_PAD_LEFT) }} {{ $id ? 'artikel' : 'articles' }}</p>
                    @endif
                </div>
            </div>

            {{-- Uniform grid — every card the same height (auto-rows-fr), 3 across then wraps down --}}
            @if ($posts->count())
                <div class="grid auto-rows-fr gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-blog-card :post="$post" :delay="($loop->index % 3) * 70" />
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-navy-200 bg-neutral-50 p-16 text-center">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-sky-400">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9 9h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </span>
                    <p class="mt-5 font-display text-lg text-navy">{{ ($q ?? '') !== '' ? ($id ? 'Tidak ada hasil' : 'No results found') : ($id ? 'Belum ada artikel' : 'No articles yet') }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ ($q ?? '') !== '' ? ($id ? 'Coba kata kunci lain atau lihat semua artikel.' : 'Try a different keyword or view all articles.') : ($id ? 'Artikel & wawasan terbaru akan tampil di sini.' : 'Our latest articles and insights will appear here.') }}</p>
                    @if (($q ?? '') !== '')
                        <a href="{{ route('blog.index') }}" class="btn-blue mt-6">{{ $id ? 'Lihat Semua Artikel' : 'View All Articles' }}</a>
                    @endif
                </div>
            @endif

            @if ($posts->hasPages())
                <div class="mt-16">{{ $posts->links('pagination.brand') }}</div>
            @endif
        </div>
    </section>
</x-layout>
