@php
    use App\Models\Setting;
    use App\Support\Locale;
    use Illuminate\Support\Str;

    $loc = Locale::current();
    $topbarActive = (bool) Setting::get('topbar_active');

    // Localized text helper (falls back to ID, then '').
    $L = fn (string $key) => trim((string) Setting::getLocalized($key, $loc, ''));

    // Build a locale-aware href from an admin-entered path (no locale prefix) or a full URL.
    $href = function (?string $path) use ($loc) {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($loc.'/'.ltrim($path, '/'));
    };

    $slides = [];

    if ($topbarActive && (bool) Setting::get('topbar_promo_active')) {
        $slides[] = [
            'type' => 'promo',
            'text' => $L('topbar_promo_text'),
            'old' => trim((string) Setting::get('topbar_promo_price_old')),
            'new' => trim((string) Setting::get('topbar_promo_price_new')),
            'note' => $L('topbar_promo_note'),
            'cta' => $L('topbar_promo_cta') ?: ($loc === 'id' ? 'Checkout Sekarang' : 'Checkout Now'),
            'href' => $href(Setting::get('topbar_promo_link')),
        ];
    }

    if ($topbarActive && (bool) Setting::get('topbar_agenda_active')) {
        $slides[] = [
            'type' => 'agenda',
            'text' => $L('topbar_agenda_text') ?: ($loc === 'id' ? 'Pelatihan & sertifikasi terbaru — lihat jadwal terdekat' : 'Latest training & certification — see the upcoming schedule'),
            'cta' => $L('topbar_agenda_cta') ?: ($loc === 'id' ? 'Lihat Agenda' : 'View Agenda'),
            'href' => $href(Setting::get('topbar_agenda_link')) ?: route('agenda.index'),
        ];
    }

    $multi = count($slides) > 1;
@endphp

@if ($topbarActive && count($slides))
    <div
        x-data="{
            i: 0,
            n: {{ count($slides) }},
            dismissed: false,
            _t: null,
            init() {
                try { this.dismissed = sessionStorage.getItem('dte_promo_x') === '1'; } catch (e) {}
                this.play();
            },
            play() { if (this.n > 1 && !this._t) { this._t = setInterval(() => { this.i = (this.i + 1) % this.n; }, 5500); } },
            pause() { clearInterval(this._t); this._t = null; },
            close() { this.dismissed = true; this.pause(); try { sessionStorage.setItem('dte_promo_x', '1'); } catch (e) {} },
        }"
        x-show="!dismissed"
        x-cloak
        @mouseenter="pause()" @mouseleave="play()"
        role="region" aria-label="{{ $loc === 'id' ? 'Pengumuman' : 'Announcement' }}"
        class="relative overflow-hidden bg-gradient-to-r from-gold via-gold-soft to-gold text-navy-950 shadow-[inset_0_1px_0_rgba(255,255,255,0.35),inset_0_-1px_0_rgba(176,135,47,0.45)]"
    >
        {{-- Gentle sheen sweeping across (decorative; draws the eye to the offer) --}}
        <div class="pointer-events-none absolute inset-0 opacity-45 [background:linear-gradient(110deg,transparent_38%,rgba(255,255,255,0.7)_50%,transparent_62%)] [background-size:220%_100%] animate-[promo-shimmer_7s_linear_infinite]"></div>

        <div class="container relative">
            {{-- Fixed-height track so rotating slides crossfade without shifting layout --}}
            <div class="relative min-h-[2.35rem]">
                @foreach ($slides as $idx => $s)
                    <div
                        @if ($multi)
                            x-show="i === {{ $idx }}"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-1.5"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1.5"
                        @endif
                        class="absolute inset-0 flex items-center justify-center gap-x-2 gap-y-0 px-7 text-[12px] leading-none sm:text-[13px]"
                    >
                        @if ($s['type'] === 'promo')
                            <span class="truncate font-semibold tracking-tight">{{ $s['text'] }}</span>
                            @if ($s['old'] !== '')
                                <span class="hidden shrink-0 text-navy-950/45 line-through decoration-navy-950/40 min-[420px]:inline">{{ $s['old'] }}</span>
                            @endif
                            @if ($s['new'] !== '')
                                <span class="shrink-0 font-bold text-rose-700">{{ $s['new'] }}</span>
                            @endif
                            @if ($s['note'] !== '')
                                <span class="hidden shrink-0 text-navy-950/65 sm:inline">{{ $s['note'] }}</span>
                            @endif
                            @if ($s['href'])
                                <a href="{{ $s['href'] }}" class="group ml-0.5 inline-flex shrink-0 items-center gap-1 rounded-full bg-navy-950 px-2.5 py-[3px] text-[11px] font-semibold text-white shadow-sm transition-colors hover:bg-navy-800">
                                    <span>{{ $s['cta'] }}</span>
                                    <svg class="h-2.5 w-2.5 transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @endif
                        @else
                            <svg class="hidden h-3.5 w-3.5 shrink-0 text-navy-950/70 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4.5" width="18" height="16" rx="2.5"/><path d="M3 9h18M8 2.5v4M16 2.5v4"/></svg>
                            <span class="truncate font-semibold tracking-tight">{{ $s['text'] }}</span>
                            @if ($s['href'])
                                <a href="{{ $s['href'] }}" class="group ml-0.5 inline-flex shrink-0 items-center gap-1 rounded-full bg-navy-950 px-2.5 py-[3px] text-[11px] font-semibold text-white shadow-sm transition-colors hover:bg-navy-800">
                                    <span>{{ $s['cta'] }}</span>
                                    <svg class="h-2.5 w-2.5 transition-transform duration-300 group-hover:translate-x-0.5" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Rotation dots (only when 2+ slides) --}}
        @if ($multi)
            <div class="pointer-events-none absolute left-3 top-1/2 hidden -translate-y-1/2 items-center gap-1.5 md:flex">
                @foreach ($slides as $idx => $s)
                    <span class="h-1.5 w-1.5 rounded-full transition-all duration-300" :class="i === {{ $idx }} ? 'bg-navy-950/80 w-3' : 'bg-navy-950/25'"></span>
                @endforeach
            </div>
        @endif

        {{-- Dismiss --}}
        <button type="button" @click="close()" aria-label="{{ $loc === 'id' ? 'Tutup pengumuman' : 'Dismiss announcement' }}"
                class="absolute right-2 top-1/2 grid h-6 w-6 -translate-y-1/2 place-items-center rounded-full text-navy-950/45 transition-colors hover:bg-black/10 hover:text-navy-950">
            <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
        </button>
    </div>
@endif
