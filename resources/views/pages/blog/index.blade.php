@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.blog')">
    <x-page-header
        :eyebrow="__('site.home.blog_kicker')"
        :title="__('site.home.blog_title')"
        :subtitle="$id ? 'Perspektif, riset, dan praktik terbaik seputar human capital.' : 'Perspectives, research, and best practices on human capital.'"
        image="photo-1499750310107-5fef28a66643" />

    <section class="section">
        <div class="container">
            {{-- Section heading --}}
            <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Artikel Terbaru' : 'Latest Articles' }}</p>
                    <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $id ? 'Wawasan & artikel kami' : 'Our insights & articles' }}</h2>
                </div>
                @if ($posts->total())
                    <p class="font-mono text-sm text-navy-400" data-aos="fade-up">{{ str_pad($posts->total(), 2, '0', STR_PAD_LEFT) }} {{ $id ? 'artikel' : 'articles' }}</p>
                @endif
            </div>

            {{-- Grid --}}
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="group block" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                        <article data-tilt class="card-3d flex h-full flex-col">
                            <div class="relative aspect-[16/10] overflow-hidden bg-navy-100">
                                @if ($post->featured_image)
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                @else
                                    <div class="absolute inset-0 aurora opacity-60"></div>
                                @endif
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-navy-950/45 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>
                            </div>
                            <div class="flex flex-1 flex-col p-6">
                                <p class="eyebrow-muted">{{ optional($post->category)->name }} · {{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                                <h3 class="mt-3 font-display text-xl font-semibold leading-snug text-navy transition-colors group-hover:text-sky-600">{{ $post->title }}</h3>
                                @if ($post->excerpt)<p class="mt-2 line-clamp-2 text-sm text-navy-500">{{ $post->excerpt }}</p>@endif
                                <span class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-navy">
                                    {{ $id ? 'Baca artikel' : 'Read article' }}
                                    <span class="grid h-8 w-8 place-items-center rounded-full border border-navy-200 transition-all duration-300 group-hover:border-sky-500 group-hover:bg-sky-500 group-hover:text-white">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </span>
                            </div>
                        </article>
                    </a>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-navy-200 bg-mist p-16 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-sky-400">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><path d="M6 4h9l3 3v13H6z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9 9h6M9 13h6M9 17h4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                        </span>
                        <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Belum ada artikel' : 'No articles yet' }}</p>
                        <p class="mt-2 text-sm text-navy-500">{{ $id ? 'Artikel & wawasan terbaru akan tampil di sini.' : 'Our latest articles and insights will appear here.' }}</p>
                    </div>
                @endforelse
            </div>

            @if ($posts->hasPages())
                <div class="mt-14">{{ $posts->links() }}</div>
            @endif
        </div>
    </section>
</x-layout>
