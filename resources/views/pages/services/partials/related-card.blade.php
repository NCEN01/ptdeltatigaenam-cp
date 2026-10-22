{{-- Kartu "Layanan terkait". Dipakai baik di kisi (sampai tiga program)
     maupun di rel geser (lebih dari tiga), jadi ditulis sekali di sini. --}}
<a href="{{ route('services.show', $r->slug) }}" class="group flex h-full flex-col overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card transition-all duration-300 hover:-translate-y-1 hover:shadow-lift">
    <div class="relative aspect-[3/2] overflow-hidden bg-navy-100">
        @if ($r->image)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($r->image) }}" alt="{{ $r->title }}" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
        @else
            <div class="h-full w-full bg-gradient-to-br from-navy-200 to-navy-100"></div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5 sm:p-6">
        @if ($r->category)<p class="font-mono text-[10px] uppercase tracking-wider text-gold-deep">{{ $r->category->name }}</p>@endif
        <h3 class="mt-1.5 font-display text-lg leading-snug text-navy transition-colors duration-300 group-hover:text-sky-700">{{ $r->title }}</h3>
        @if ($r->short_description)<p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ $r->short_description }}</p>@endif
        <span class="mt-auto flex items-center gap-2 pt-4 text-sm font-medium text-navy">
            {{ $id ? 'Lihat detail' : 'View details' }}
            <svg class="h-4 w-4 text-gold-deep transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
    </div>
</a>
