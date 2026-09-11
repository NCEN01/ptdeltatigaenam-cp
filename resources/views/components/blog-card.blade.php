@props([
    'post',
    'delay' => 0,
    // Off inside carousels: slides are laid out horizontally, so a scroll-triggered
    // reveal on each card fights the slider instead of helping.
    'animate' => true,
])

@php
    use Illuminate\Support\Facades\Storage;
    $isId = app()->getLocale() === 'id';
    $img = $post->featured_image
        ? (str_starts_with($post->featured_image, 'http') ? $post->featured_image : Storage::url($post->featured_image))
        : null;
@endphp

<a href="{{ route('blog.show', $post->slug) }}" data-spotlight
   class="card card-hover group flex h-full flex-col overflow-hidden"
   @if ($animate) data-aos="fade-up" data-aos-delay="{{ $delay }}" @endif>

    {{-- Shorter crop on phones so a card doesn't eat most of the viewport. --}}
    <div class="relative aspect-[16/9] shrink-0 overflow-hidden bg-navy-900 sm:aspect-[16/10]">
        @if ($img)
            <img src="{{ $img }}" alt="{{ $post->title }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
        @else
            <div class="absolute inset-0 aurora opacity-60"></div>
        @endif

        @if ($post->category)
            <span class="absolute left-4 top-4 rounded-full bg-white/90 px-2.5 py-1 font-mono text-[9px] uppercase tracking-wider text-navy backdrop-blur">{{ $post->category->name }}</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4 sm:p-6">
        <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1 font-mono text-[11px] uppercase tracking-wider text-slate-500">
            <span>{{ optional($post->published_at)->translatedFormat('d M Y') }}</span>
            <x-meta-location :value="$post->location" />
        </div>

        {{-- The 2-line reserve only matters where cards sit side by side; on phones one
             card fills the row, so the extra blank line is wasted height. --}}
        <h3 class="mt-2 line-clamp-2 font-display text-base leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700 sm:mt-2.5 sm:min-h-[3.25rem] sm:text-lg">{{ $post->title }}</h3>

        @if ($post->excerpt)
            <p class="mt-1.5 line-clamp-2 text-pretty text-sm leading-relaxed text-slate-600 sm:mt-2 sm:line-clamp-3">{{ $post->excerpt }}</p>
        @endif

        <span class="mt-auto flex items-center gap-2 border-t border-navy-100 pt-3 text-sm font-medium text-navy sm:pt-4">
            {{ $isId ? 'Baca artikel' : 'Read article' }}
            <svg class="h-4 w-4 text-gold-deep transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
    </div>
</a>
