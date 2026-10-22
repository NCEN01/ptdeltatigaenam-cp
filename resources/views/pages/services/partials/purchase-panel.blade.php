{{-- Panel pembelian program: kartu jadwal lalu kartu bagikan.
     Dirender dua kali oleh services/show — di kolom kanan untuk layar lebar,
     dan di bawah "Rangkaian materi" untuk ponsel — jadi isinya ditulis
     sekali di sini. Variabel yang dipakai ($service, $scheduleCount,
     $priceUnit, $id) diwarisi dari halaman pemanggilnya. --}}
<div class="overflow-hidden rounded-3xl border border-navy-100 bg-white shadow-lift">
    {{-- Header — same aurora/blue-gradient treatment as the hero above --}}
    <div class="relative overflow-hidden border-b border-navy-100 bg-navy-950 p-5 text-white">
        <div class="pointer-events-none absolute inset-0 aurora animate-aurora-drift opacity-60"></div>
        <div class="pointer-events-none absolute inset-0 grain opacity-40"></div>
        <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-gold-soft/45 to-transparent"></div>
        {{-- Layanan per penugasan hanya membuka satu batch, jadi tidak ada
             yang bisa "dipilih". Teksnya menyesuaikan agar tidak menjanjikan
             pilihan yang tidak ada. --}}
        <div class="relative">
            <p class="font-mono text-[11px] uppercase tracking-normal text-gold-soft">{{ $id ? 'Amankan Kursi Anda' : 'Secure Your Seat' }}</p>
            <h3 class="mt-2 font-display text-xl">
                @if ($scheduleCount > 1)
                    {{ $id ? 'Pilih jadwal di bawah' : 'Pick a schedule below' }}
                @else
                    {{ $id ? 'Jadwal terdekat' : 'Next available schedule' }}
                @endif
            </h3>
        </div>
    </div>

    {{-- Jadwal --}}
    <div class="space-y-2.5 p-4">
        {{-- Sisa kursi dihitung PER angkatan, di dalam perulangan.
             Dulu dihitung sekali di luarnya dari kuota layanan,
             sehingga tiga angkatan dengan keterisian berbeda
             menunjukkan angka yang sama persis. --}}
        @forelse ($service->schedules as $schedule)
            @php
                $left = $schedule->seats_available;
                $full = $left !== null && $left <= 0;
                $low = $left !== null && $left > 0 && $left <= 5;
            @endphp
            <div class="rounded-2xl border {{ $low ? 'border-amber-200' : 'border-navy-100' }} bg-white p-4 transition-shadow duration-300 hover:shadow-card">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-display text-base text-navy">{{ $schedule->start_date?->translatedFormat('d M Y') }}</p>
                        @if ($schedule->start_time)
                            <p class="mt-1 font-mono text-xs text-slate-600">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                @if ($schedule->end_time) - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }} WIB @endif
                            </p>
                        @endif
                        @if ($service->location)<p class="mt-0.5 text-[13px] text-slate-500">{{ $service->location }}</p>@endif
                    </div>
                    @if ($service->modeLabel())
                        <span class="shrink-0 rounded-full bg-navy-50 px-3 py-1 font-mono text-[10px] uppercase tracking-wider text-slate-600">{{ $service->modeLabel() }}</span>
                    @endif
                </div>

                @if ($left !== null)
                    <div class="mt-2.5 flex items-center gap-3">
                        {{-- Quota bar --}}
                        <div class="flex-1 h-1.5 rounded-full bg-navy-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all {{ $full ? 'bg-rose-500' : ($low ? 'bg-amber-500' : 'bg-emerald-500') }}"
                                 style="width: {{ $schedule->quota > 0 ? min(100, round(($schedule->seats_taken / $schedule->quota) * 100)) : 0 }}%"></div>
                        </div>
                        <span class="shrink-0 font-mono text-[10px] uppercase tracking-wider {{ $full ? 'text-rose-600' : ($low ? 'text-amber-700' : 'text-emerald-700') }}">
                            {{ $full ? ($id ? 'Penuh' : 'Full') : $left.'/'.$schedule->quota }}
                        </span>
                    </div>
                @endif

                @if ($schedule->effectivePrice() > 0)
                    <p class="mt-2.5 font-display text-lg text-navy">Rp {{ number_format($schedule->effectivePrice(), 0, ',', '.') }}<span class="text-sm font-normal text-slate-500"> / {{ $priceUnit }}</span></p>
                @endif

                @if ($service->is_purchasable)
                    @if ($full)
                        <button disabled class="mt-3 w-full justify-center !py-2 text-sm rounded-full border border-navy-200 bg-navy-50 text-slate-500 cursor-not-allowed font-medium">
                            {{ $id ? 'Kuota Penuh' : 'Fully Booked' }}
                        </button>
                    @elseif (Route::has('checkout.create'))
                        <a href="{{ route('checkout.create', $schedule->id) }}" class="btn-blue mt-3 w-full justify-center !py-2 text-sm">{{ $id ? 'Daftar / Beli' : 'Register / Buy' }}</a>
                    @else
                        <a href="{{ route('contact.index') }}" class="btn-ghost mt-3 w-full justify-center !py-2 text-sm">{{ __('site.cta.consult') }}</a>
                    @endif
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-navy-200 bg-white p-6 text-center">
                <p class="text-sm text-slate-600">{{ $id ? 'Belum ada jadwal terbuka. Hubungi kami untuk jadwal khusus atau in-house training.' : 'No open schedules yet. Contact us for a custom or in-house batch.' }}</p>
                <a href="{{ route('contact.index') }}" class="btn-blue mt-5 w-full justify-center">{{ __('site.cta.consult') }}</a>
            </div>
        @endforelse
    </div>

    @if ($service->is_purchasable && $service->schedules->isNotEmpty())
        <div class="space-y-1.5 border-t border-navy-100 bg-neutral-50 px-5 py-4 text-xs text-slate-600">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                {{ $id ? 'Pembayaran aman: QRIS, e-wallet, VA, & kartu (Midtrans).' : 'Secure payment: QRIS, e-wallet, VA & card (Midtrans).' }}
            </div>
            <p class="flex items-center gap-2">
                <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.5 5 5.5.8-4 3.9 1 5.5L12 15.5 7 18.2l1-5.5-4-3.9 5.5-.8z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>
                {{ $id ? 'Sertifikat resmi untuk setiap peserta yang lulus.' : 'Official certificate for every participant who passes.' }}
            </p>
        </div>
    @endif
</div>

{{-- Bagikan program ini. Bentuknya sama dengan kartu bagikan di
     halaman artikel — kotak abu-abu bergaris rambut, satu baris
     ajakan, lalu deretan lambang jaringan — dan memakai komponen
     yang sama persis, jadi daftar jaringannya tidak bisa
     menyimpang antara kedua halaman.

     Di bawah panel jadwal, bukan di dasar halaman: orang yang
     membagikan program biasanya sedang meneruskannya ke atasan
     atau rekan yang akan ikut, dan keputusan itu muncul saat ia
     sedang menimbang jadwal dan harganya. --}}
<div class="rounded-2xl border border-navy-100 bg-neutral-50 p-6">
    <p class="mb-4 text-sm font-semibold text-navy">
        {{ $id ? 'Bagikan program ini' : 'Share this programme' }}
    </p>
    <x-share-buttons :url="route('services.show', $service->slug)" :title="$service->title" />
</div>
