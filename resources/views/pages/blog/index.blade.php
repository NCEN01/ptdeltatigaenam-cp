@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="__('site.nav.blog')">
    <x-page-header
        :eyebrow="__('site.home.blog_kicker')"
        :title="__('site.home.blog_title')"
        :subtitle="$id ? 'Perspektif, riset, dan praktik terbaik seputar human capital.' : 'Perspectives, research, and best practices on human capital.'" />

    <section class="section">
        <div class="container">
            {{-- Featured --}}
            @if ($featured)
                <a href="{{ route('blog.show', $featured->slug) }}" class="group mb-16 grid gap-8 lg:grid-cols-2 lg:items-center" data-aos="fade-up">
                    <div class="relative aspect-[16/10] overflow-hidden rounded-3xl bg-navy-100">
                        @if ($featured->featured_image)<img src="{{ Storage::url($featured->featured_image) }}" alt="{{ $featured->title }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">@endif
                        <span class="absolute left-5 top-5 rounded-full bg-gold px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-ink">{{ __('site.common.featured') }}</span>
                    </div>
                    <div>
                        <p class="eyebrow-muted">{{ optional($featured->category)->name }} · {{ optional($featured->published_at)->translatedFormat('d M Y') }}</p>
                        <h2 class="mt-4 font-display text-display-lg font-semibold leading-tight text-navy group-hover:text-navy-500">{{ $featured->title }}</h2>
                        @if ($featured->excerpt)<p class="mt-4 text-pretty text-lg text-navy-500">{{ $featured->excerpt }}</p>@endif
                        <span class="link-underline mt-6 font-medium">{{ __('site.cta.read_more') }}</span>
                    </div>
                </a>
            @endif

            {{-- Grid --}}
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($posts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                        <div class="relative aspect-[16/10] overflow-hidden rounded-2xl bg-navy-100">
                            @if ($post->featured_image)<img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">@endif
                        </div>
                        <div class="flex flex-1 flex-col pt-5">
                            <p class="eyebrow-muted">{{ optional($post->category)->name }} · {{ optional($post->published_at)->translatedFormat('d M Y') }}</p>
                            <h3 class="mt-3 font-display text-xl font-semibold leading-snug text-navy group-hover:text-navy-500">{{ $post->title }}</h3>
                            @if ($post->excerpt)<p class="mt-2 line-clamp-2 text-sm text-navy-500">{{ $post->excerpt }}</p>@endif
                        </div>
                    </a>
                @empty
                    <p class="text-navy-400">{{ $id ? 'Belum ada artikel.' : 'No articles yet.' }}</p>
                @endforelse
            </div>

            <div class="mt-14">{{ $posts->links() }}</div>
        </div>
    </section>
</x-layout>
