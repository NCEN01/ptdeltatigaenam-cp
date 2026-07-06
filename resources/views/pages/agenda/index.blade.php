@php use Illuminate\Support\Facades\Storage; $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="$id ? 'Agenda' : 'Agenda'">
    <x-page-header
        :eyebrow="$id ? 'Agenda' : 'Agenda'"
        :title="$id ? 'Agenda & kegiatan terbaru' : 'Latest agenda & events'"
        :subtitle="$id ? 'Jadwal pelatihan, sertifikasi, seminar, dan kegiatan terbaru dari PT Delta Tiga Enam.' : 'Upcoming training, certification, seminars, and events from PT Delta Tiga Enam.'"
        placement="agenda"
        image="photo-1517048676732-d65bc937f952" />

    <section class="section">
        <div class="container">
            {{-- Section heading --}}
            <div class="mb-12 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <p class="eyebrow mb-4" data-aos="fade-up"><span class="rule-gold mr-3"></span>{{ $id ? 'Agenda Terbaru' : 'Latest Agenda' }}</p>
                    <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $id ? 'Jangan lewatkan kegiatan kami' : "Don't miss our events" }}</h2>
                </div>
                @if ($agendas->total())
                    <p class="font-mono text-sm text-navy-400" data-aos="fade-up">{{ str_pad($agendas->total(), 2, '0', STR_PAD_LEFT) }} {{ $id ? 'agenda' : 'events' }}</p>
                @endif
            </div>

            {{-- Grid --}}
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($agendas as $agenda)
                    <article data-tilt class="card-3d flex h-full flex-col" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                        <div class="relative aspect-[16/10] overflow-hidden bg-navy-100">
                            @if ($agenda->image)
                                <img src="{{ Storage::url($agenda->image) }}" alt="{{ $agenda->title }}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 aurora opacity-60"></div>
                            @endif
                            {{-- date badge --}}
                            @if ($agenda->starts_at)
                                <div class="absolute left-4 top-4 z-10 rounded-2xl bg-white/95 px-3.5 py-2 text-center shadow-card backdrop-blur">
                                    <p class="font-display text-2xl font-semibold leading-none text-navy">{{ $agenda->starts_at->format('d') }}</p>
                                    <p class="mt-0.5 font-mono text-[10px] uppercase tracking-wider text-sky-600">{{ $agenda->starts_at->translatedFormat('M Y') }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[11px] text-navy-400">
                                @if ($agenda->starts_at)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-sky-500" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $agenda->starts_at->translatedFormat('H:i') }}{{ $agenda->ends_at ? '–'.$agenda->ends_at->translatedFormat('H:i') : '' }}
                                    </span>
                                @endif
                                @if ($agenda->location)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-sky-500" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-5.2 7-11a7 7 0 10-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.5"/></svg>
                                        {{ $agenda->location }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="mt-3 line-clamp-2 font-display text-xl font-semibold leading-snug text-navy">{{ $agenda->title }}</h3>
                            @if ($agenda->excerpt)<p class="mt-2 line-clamp-3 text-sm leading-relaxed text-navy-500">{{ $agenda->excerpt }}</p>@endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-navy-200 bg-mist p-16 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-sky-400">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M4 9h16M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                        </span>
                        <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Belum ada agenda' : 'No agenda yet' }}</p>
                        <p class="mt-2 text-sm text-navy-500">{{ $id ? 'Agenda & kegiatan terbaru akan tampil di sini.' : 'Our latest agenda and events will appear here.' }}</p>
                    </div>
                @endforelse
            </div>

            @if ($agendas->hasPages())
                <div class="mt-14">{{ $agendas->links() }}</div>
            @endif
        </div>
    </section>
</x-layout>
