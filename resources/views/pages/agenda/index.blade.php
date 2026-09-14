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
            <div class="mb-12 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-2xl">
                    <h2 class="text-display-lg font-semibold text-navy text-balance" data-aos="fade-up">{{ $id ? 'Jangan lewatkan kegiatan kami' : "Don't miss our events" }}</h2>
                    <p class="mt-4 text-pretty leading-relaxed text-slate-600" data-aos="fade-up">
                        {{ $id
                            ? 'Ikuti kelas pelatihan, sertifikasi kompetensi, dan seminar terbaru dari kami. Punya pertanyaan seputar jadwal, materi, atau pendaftaran?'
                            : 'Join our latest training classes, competency certifications, and seminars. Have questions about the schedule, materials, or registration?' }}
                    </p>
                    <a href="{{ route('contact.index') }}" class="btn-blue mt-6" data-aos="fade-up">
                        {{ $id ? 'Tanya Seputar Agenda' : 'Ask About the Agenda' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                </div>

                {{-- Split the count by status: a bare total reads as "5 events you can join"
                     even when all five have already happened. --}}
                @if ($agendas->total())
                    <div class="flex shrink-0 items-center gap-5 md:flex-col md:items-end md:gap-2" data-aos="fade-up">
                        @if ($upcomingCount)
                            <span class="inline-flex items-center gap-2 font-mono text-sm text-navy">
                                <span class="relative flex h-2 w-2">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400 opacity-60"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-sky-500"></span>
                                </span>
                                {{ str_pad($upcomingCount, 2, '0', STR_PAD_LEFT) }} {{ $id ? 'akan datang' : 'upcoming' }}
                            </span>
                        @endif
                        @if ($pastCount)
                            <span class="inline-flex items-center gap-2 font-mono text-sm text-slate-500">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                {{ str_pad($pastCount, 2, '0', STR_PAD_LEFT) }} {{ $id ? 'telah berlangsung' : 'completed' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Grid --}}
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @php $nearestTaken = false; @endphp
                @forelse ($agendas as $agenda)
                    @php
                        $isPast = $agenda->starts_at && $agenda->starts_at->isPast();
                        // The single soonest upcoming event, and only on the first page.
                        $isNearest = ! $isPast && ! $nearestTaken && $agendas->onFirstPage();
                        if ($isNearest) { $nearestTaken = true; }

                        if ($isPast)          { $pill = 'bg-slate-200 text-slate-600'; $pillText = $id ? 'Selesai' : 'Ended'; }
                        elseif ($isNearest)   { $pill = 'bg-gold text-navy-950';       $pillText = $id ? 'Segera Hadir' : 'Coming Soon'; }
                        else                  { $pill = 'bg-sky-600 text-white';       $pillText = $id ? 'Akan Datang' : 'Upcoming'; }
                    @endphp

                    {{-- Plain card, not card-3d: there is no agenda detail route, so a pointer-tilt
                         would promise a click that leads nowhere. Radius matches every other card. --}}
                    <article class="card group flex h-full flex-col overflow-hidden transition-all duration-300 ease-out-soft hover:-translate-y-1 hover:shadow-lift {{ $isPast ? 'opacity-85' : '' }}"
                             data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">

                        <div class="relative aspect-[16/10] shrink-0 overflow-hidden bg-navy-100">
                            @if ($agenda->image)
                                {{-- Past events desaturate so the upcoming ones carry the colour. --}}
                                <img src="{{ Storage::url($agenda->image) }}" alt="{{ $agenda->title }}" loading="lazy"
                                     class="h-full w-full object-cover transition-all duration-700 group-hover:scale-105 {{ $isPast ? 'grayscale group-hover:grayscale-0' : '' }}">
                            @else
                                <div class="absolute inset-0 aurora opacity-60"></div>
                            @endif

                            {{-- Date badge --}}
                            @if ($agenda->starts_at)
                                <div class="absolute left-4 top-4 z-10 rounded-xl px-3.5 py-2 text-center shadow-card backdrop-blur {{ $isNearest ? 'bg-gradient-to-br from-gold to-gold-soft ring-1 ring-white/50' : 'bg-white/95' }}">
                                    <p class="font-display text-2xl font-semibold leading-none {{ $isNearest ? 'text-navy-950' : 'text-navy' }}">{{ $agenda->starts_at->format('d') }}</p>
                                    <p class="mt-0.5 font-mono text-[10px] uppercase tracking-wider {{ $isNearest ? 'text-navy-950/70' : 'text-sky-600' }}">{{ $agenda->starts_at->translatedFormat('M Y') }}</p>
                                </div>
                            @endif

                            {{-- Status: without it a finished event looks identical to one you can still join. --}}
                            <span class="absolute right-4 top-4 z-10 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider shadow-card {{ $pill }}">
                                @unless ($isPast)
                                    <span class="h-1 w-1 rounded-full bg-current"></span>
                                @endunless
                                {{ $pillText }}
                            </span>
                        </div>

                        <div class="flex flex-1 flex-col p-6">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-[11px] text-slate-500">
                                @if ($agenda->starts_at)
                                    <span class="inline-flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 {{ $isPast ? 'text-slate-400' : 'text-sky-500' }}" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $agenda->starts_at->translatedFormat('H:i') }}{{ $agenda->ends_at ? '–'.$agenda->ends_at->translatedFormat('H:i') : '' }}
                                    </span>
                                @endif
                                @if ($agenda->location)
                                    <span class="inline-flex min-w-0 items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 shrink-0 {{ $isPast ? 'text-slate-400' : 'text-sky-500' }}" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-5.2 7-11a7 7 0 10-14 0c0 5.8 7 11 7 11z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="1.5"/></svg>
                                        <span class="truncate">{{ $agenda->location }}</span>
                                    </span>
                                @endif
                            </div>

                            <h3 class="mt-3 line-clamp-2 min-h-[3.5rem] font-display text-xl font-semibold leading-snug text-navy">{{ $agenda->title }}</h3>

                            @if ($agenda->excerpt)
                                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600">{{ $agenda->excerpt }}</p>
                            @endif

                            {{-- Gives the card somewhere to lead: there is no detail page, so the next
                                 useful step is asking about this specific event. --}}
                            <a href="{{ route('contact.index') }}"
                               class="link-underline mt-auto w-fit pt-5 text-sm font-medium"
                               aria-label="{{ ($id ? 'Tanya tentang: ' : 'Ask about: ') . $agenda->title }}">
                                {{ $isPast ? ($id ? 'Tanya kegiatan serupa' : 'Ask about similar events') : ($id ? 'Tanya jadwal ini' : 'Ask about this event') }}
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-navy-200 bg-mist p-16 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-sky-400">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none"><rect x="4" y="5" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.4"/><path d="M4 9h16M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                        </span>
                        <p class="mt-5 font-display text-lg font-semibold text-navy">{{ $id ? 'Belum ada agenda' : 'No agenda yet' }}</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $id ? 'Agenda & kegiatan terbaru akan tampil di sini.' : 'Our latest agenda and events will appear here.' }}</p>
                        <a href="{{ route('contact.index') }}" class="btn-blue mt-6">{{ $id ? 'Tanya Jadwal Berikutnya' : 'Ask About Upcoming Dates' }}</a>
                    </div>
                @endforelse
            </div>

            @if ($agendas->hasPages())
                <div class="mt-16">{{ $agendas->links('pagination.brand') }}</div>
            @endif
        </div>
    </section>
</x-layout>
