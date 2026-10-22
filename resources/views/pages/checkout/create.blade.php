@php
    $id = app()->getLocale() === 'id';
    $oldParticipants = old('participants');
    $prefillParticipants = (! $oldParticipants && empty($paidOrder) && !empty($pendingOrder?->participants))
        ? $pendingOrder->participants
        : null;
    $rows = $oldParticipants ?: ($prefillParticipants ?: [['name' => '', 'phone' => '']]);
    $unit = (int) $unitPrice;
    $rupiah = fn ($n) => 'Rp '.number_format((float) $n, 0, ',', '.');

    // Kelas kolom isian peserta disamakan dengan <x-field>, supaya kolom
    // yang dibuat lewat JS tidak berbeda rupa dari kolom data pemesan.
    $inputClass = 'w-full rounded-2xl border border-navy-400 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-500 focus:border-navy focus:outline-none focus:ring-2 focus:ring-sky-700';

    $jamMulai = $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : null;
    $jamSelesai = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : null;
@endphp

<x-layout :title="'Checkout'">
    <x-page-header :title="$service->title" />

    {{-- Latar bg-paper, bukan putih: kartu-kartu putih di atasnya yang
         menjadi bentuk halaman, bukan garis tepinya saja. --}}
    <section class="section-sm bg-paper">
        <div class="container">
            {{-- Baris atas: kembali di kiri, tahapan pesanan di kanan. Tahapannya
                 memberi tahu sejak awal bahwa setelah formulir ini masih ada
                 satu langkah pembayaran — bukan langsung tertagih. --}}
            <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('services.show', $service->slug) }}" class="inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-slate-500 transition-colors hover:text-navy">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $id ? 'Kembali ke detail layanan' : 'Back to service details' }}
                </a>

                <ol class="flex items-center gap-2 text-xs font-medium" aria-label="{{ $id ? 'Tahapan pesanan' : 'Order steps' }}">
                    @foreach ($id ? ['Isi data', 'Pembayaran', 'Selesai'] : ['Details', 'Payment', 'Done'] as $i => $tahap)
                        @if ($i > 0)<li class="h-px w-6 bg-navy-200 sm:w-8" aria-hidden="true"></li>@endif
                        <li class="flex items-center gap-2 {{ $i === 0 ? 'text-navy' : 'text-slate-500' }}" @if ($i === 0) aria-current="step" @endif>
                            <span class="grid h-6 w-6 place-items-center rounded-full font-mono text-[11px] {{ $i === 0 ? 'bg-navy-600 text-white' : 'border border-navy-200 bg-white text-slate-500' }}">{{ $i + 1 }}</span>
                            {{ $tahap }}
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="grid gap-6 lg:grid-cols-12 lg:items-start lg:gap-10">
                {{-- ===================== RINGKASAN =====================
                     Di ponsel ia tampil PALING ATAS, sebelum kolom isian: layanan,
                     jadwal, dan totalnya adalah hal yang ingin dipastikan orang
                     sebelum mau mengetik apa pun. Di layar lebar ia kembali ke
                     kolom kanan dan menempel saat formulirnya digulir. --}}
                <aside class="order-first lg:order-last lg:col-span-5 lg:sticky lg:top-28">
                    <div class="overflow-hidden rounded-2xl border border-navy-100 bg-white shadow-card">
                        <div class="flex items-center gap-4 border-b border-navy-100 p-4 sm:p-5">
                            @if ($service->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($service->image) }}" alt=""
                                     class="h-16 w-20 shrink-0 rounded-xl object-cover sm:h-[4.5rem] sm:w-24" loading="lazy" decoding="async">
                            @endif
                            <div class="min-w-0">
                                <p class="font-mono text-[10px] uppercase tracking-wider text-sky-700">{{ $id ? 'Ringkasan pesanan' : 'Order summary' }}</p>
                                <p class="mt-1 line-clamp-2 font-display text-base font-semibold leading-snug text-navy">{{ $service->title }}</p>
                            </div>
                        </div>

                        <dl class="space-y-3 p-4 text-sm sm:p-5">
                            <div class="flex items-start justify-between gap-4">
                                <dt class="flex items-center gap-2 text-slate-600">
                                    <svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
                                    {{ $id ? 'Jadwal' : 'Schedule' }}
                                </dt>
                                <dd class="text-right font-medium text-navy">
                                    {{ $schedule->start_date?->translatedFormat('d M Y') }}
                                    @if ($schedule->end_date && $schedule->start_date && ! $schedule->end_date->isSameDay($schedule->start_date))
                                        – {{ $schedule->end_date->translatedFormat('d M Y') }}
                                    @endif
                                    @if ($jamMulai)
                                        <span class="block font-mono text-xs font-normal text-slate-500">{{ $jamMulai }}@if ($jamSelesai) – {{ $jamSelesai }}@endif WIB</span>
                                    @endif
                                </dd>
                            </div>
                            @if ($service->location)
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="flex items-center gap-2 text-slate-600">
                                        <svg class="h-4 w-4 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $id ? 'Lokasi' : 'Location' }}
                                    </dt>
                                    <dd class="text-right font-medium text-navy">{{ $service->location }}</dd>
                                </div>
                            @endif
                            @if ($service->modeLabel())
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="flex items-center gap-2 text-slate-600">
                                        <svg class="h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg>
                                        {{ $id ? 'Cara mengikuti' : 'Format' }}
                                    </dt>
                                    <dd class="text-right font-medium text-navy">{{ $service->modeLabel() }}</dd>
                                </div>
                            @endif
                        </dl>

                        <div class="space-y-2.5 border-t border-dashed border-navy-200 p-4 text-sm sm:p-5">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-600">{{ $id ? 'Harga / peserta' : 'Price / person' }}</span>
                                <span class="text-right">
                                    @if ($service->hasDiscount())
                                        <span class="mr-1.5 font-mono text-xs text-slate-400 line-through">{{ $rupiah($service->discount_original_price) }}</span>
                                    @endif
                                    <span class="font-medium text-navy">{{ $rupiah($unitPrice) }}</span>
                                </span>
                            </div>
                            @if ($service->hasDiscount())
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-600">{{ $id ? 'Diskon' : 'Discount' }}</span>
                                    <span class="rounded bg-gold px-1.5 py-0.5 font-mono text-[11px] font-bold text-ink">{{ $id ? 'Hemat' : 'Save' }} {{ $service->discountPercent() }}%</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-600">{{ $id ? 'Jumlah peserta' : 'Participants' }}</span>
                                <span class="font-medium text-navy">× <span data-count-mirror>{{ count($rows) }}</span></span>
                            </div>
                        </div>

                        <div class="flex items-end justify-between gap-4 bg-navy-950 px-4 py-4 text-white sm:px-5">
                            <span class="text-sm text-navy-100">Total</span>
                            <span data-total class="font-display text-2xl font-semibold tabular-nums">{{ $rupiah($unit * count($rows)) }}</span>
                        </div>
                    </div>

                    @if ($service->hasDiscount())
                        <p class="mt-3 rounded-xl bg-gold/10 px-3.5 py-2.5 text-xs leading-relaxed text-gold-deep">{{ $id ? 'Harga promo berlaku terbatas, harga akan naik setelah periode promo berakhir.' : 'Promo price for a limited time, the price will rise after the promo ends.' }}</p>
                    @endif
                </aside>

                {{-- ===================== FORMULIR ===================== --}}
                <div class="space-y-5 lg:col-span-7">
                    @if (!empty($paidOrder))
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/75 p-5 sm:p-6">
                            <div class="flex items-start gap-3.5">
                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-display text-lg font-semibold text-navy">
                                        {{ $id ? 'Pembayaran Jadwal Ini Sudah Lunas' : 'This Schedule Is Already Paid' }}
                                    </p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-700">
                                        {{ $id
                                            ? "Anda sudah memiliki pesanan lunas ({$paidOrder->order_number} · {$paidOrder->quantity} peserta). Anda dapat langsung melihat bukti pembayaran tanpa mengisi ulang formulir, atau tetap mendaftar bila ingin menambah peserta baru."
                                            : "You already have a paid order ({$paidOrder->order_number} · {$paidOrder->quantity} participant(s)). You can skip straight to your payment receipt, or continue below to register additional participants." }}
                                    </p>
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <a href="{{ route('checkout.finish', $paidOrder->order_number) }}" class="btn-blue !px-5 !py-2.5 text-sm">
                                            {{ $id ? 'Lihat Bukti Pembayaran (Lewati)' : 'View Payment Receipt (Skip)' }}
                                        </a>
                                        @if (empty($repurchase))
                                            <a href="{{ route('checkout.create', ['schedule' => $schedule->id, 'repurchase' => 1]) }}#form-checkout" class="btn-ghost !px-5 !py-2.5 text-sm">
                                                {{ $id ? 'Tetap Daftar Peserta Tambahan' : 'Register Additional Participants' }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (!empty($pendingOrder))
                        <div class="rounded-2xl border border-amber-200 bg-amber-50/75 p-5 sm:p-6">
                            <div class="flex items-start gap-3.5">
                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-amber-500 text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="font-display text-lg font-semibold text-navy">
                                        {{ $id ? 'Ada Pesanan Menunggu Pembayaran' : 'You Have a Pending Payment' }}
                                    </p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-700">
                                        {{ $id
                                            ? "Pesanan {$pendingOrder->order_number} ({$pendingOrder->quantity} peserta · Rp ".number_format((float) $pendingOrder->total_amount, 0, ',', '.').") masih berstatus menunggu pembayaran. Lanjutkan pembayaran, ganti metode bayar, atau kirim formulir di bawah untuk mereset pesanan."
                                            : "Order {$pendingOrder->order_number} ({$pendingOrder->quantity} participant(s) · Rp ".number_format((float) $pendingOrder->total_amount, 0, ',', '.').") is awaiting payment. Continue payment, change payment method, or submit the form below to reset the order." }}
                                    </p>
                                    <div class="mt-4 flex flex-wrap items-center gap-2.5">
                                        <a href="{{ route('checkout.pay', $pendingOrder->order_number) }}" class="btn-blue !px-5 !py-2.5 text-sm">
                                            {{ $id ? 'Lanjutkan Pembayaran' : 'Continue Payment' }}
                                        </a>
                                        <form method="POST" action="{{ route('checkout.retry', $pendingOrder->order_number) }}">
                                            @csrf
                                            <button type="submit" class="btn-ghost !px-5 !py-2.5 text-sm">
                                                {{ $id ? 'Ganti Metode / Ulangi Bayar' : 'Change Method / Retry' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('checkout.cancel', $pendingOrder->order_number) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-4 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50">
                                                {{ $id ? 'Batalkan Pesanan' : 'Cancel Order' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form id="form-checkout" method="POST" action="{{ route('checkout.store') }}" class="space-y-5">
                        @csrf
                        <input type="hidden" name="service_schedule_id" value="{{ $schedule->id }}">
                        @if (!empty($paidOrder))
                            <input type="hidden" name="confirm_repurchase" value="1">
                        @endif

                        @if ($errors->any())
                            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4" role="alert">
                                <p class="text-sm font-semibold text-rose-700">{{ $id ? 'Periksa kembali isian berikut:' : 'Please check the following:' }}</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-600">
                                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- 1 · Data pemesan --}}
                        <div class="rounded-2xl border border-navy-100 bg-white p-5 shadow-card sm:p-7">
                            <div class="flex items-start gap-3.5">
                                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-navy-600 font-mono text-xs font-semibold text-white" aria-hidden="true">1</span>
                                <div>
                                    <h2 class="font-display text-lg font-semibold text-navy sm:text-xl">{{ $id ? 'Data Pemesan' : 'Your Details' }}</h2>
                                    <p class="mt-0.5 text-sm text-slate-600">{{ $id ? 'Dipakai untuk invoice dan konfirmasi pesanan.' : 'Used for the invoice and order confirmation.' }}</p>
                                </div>
                            </div>
                            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                                <x-field name="customer_name" :label="__('site.contact.name')" :value="$customer->name" required maxlength="150" />
                                <x-field name="customer_email" type="email" :label="__('site.contact.email')" :value="$customer->email" required maxlength="150" />
                                <x-phone-field name="customer_phone" :label="__('site.contact.phone')" :value="$customer->phone" required />
                                <x-field name="customer_company" :label="$id ? 'Perusahaan' : 'Company'" :value="$customer->company" minlength="2" maxlength="200" />
                            </div>
                        </div>

                        {{-- 2 · Data peserta --}}
                        <div class="rounded-2xl border border-navy-100 bg-white p-5 shadow-card sm:p-7"
                             data-participants
                             data-unit="{{ $unit }}"
                             data-profile-name="{{ $customer->name }}"
                             data-profile-phone="{{ $customer->phone }}">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="flex items-start gap-3.5">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-navy-600 font-mono text-xs font-semibold text-white" aria-hidden="true">2</span>
                                    <div>
                                        <h2 class="font-display text-lg font-semibold text-navy sm:text-xl">{{ $id ? 'Data Peserta' : 'Participant Details' }}</h2>
                                        <p class="mt-0.5 text-sm text-slate-600">{{ $id ? 'Nama di sini yang tercetak di sertifikat.' : 'These names are printed on the certificates.' }}</p>
                                    </div>
                                </div>
                                {{-- Pengatur jumlah. Satu kapsul berisi kurang, angka,
                                     tambah — dibaca sebagai satu kendali, bukan dua tombol
                                     bundar yang terpisah dari angkanya. --}}
                                <div class="ml-auto inline-flex items-center rounded-full border border-navy-200 bg-white p-1 shadow-sm">
                                    <button type="button" data-remove class="grid h-9 w-9 place-items-center rounded-full text-lg text-navy transition-colors hover:bg-navy-50" aria-label="{{ $id ? 'Kurangi peserta' : 'Remove participant' }}">−</button>
                                    <span data-count class="w-9 text-center font-display text-base font-semibold tabular-nums text-navy" aria-live="polite">{{ count($rows) }}</span>
                                    <button type="button" data-add class="grid h-9 w-9 place-items-center rounded-full text-lg text-navy transition-colors hover:bg-navy-50" aria-label="{{ $id ? 'Tambah peserta' : 'Add participant' }}">+</button>
                                </div>
                            </div>

                            {{-- Toggle: fill participant 1 from profile --}}
                            <label class="mt-6 flex cursor-pointer items-center gap-3 rounded-xl bg-navy-50 px-4 py-3">
                                <input type="checkbox" data-profile-toggle class="peer sr-only">
                                <span class="relative h-6 w-11 shrink-0 rounded-full bg-navy-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-navy-600 peer-checked:after:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-sky-700"></span>
                                <span class="text-sm text-navy">{{ $id ? 'Isi Peserta 1 dengan data dari profil saya' : 'Fill Participant 1 with my profile data' }}</span>
                            </label>

                            {{-- Participant rows (server-rendered so they always submit) --}}
                            <div data-list class="mt-4 space-y-3">
                                @foreach ($rows as $i => $p)
                                    <div data-row class="rounded-xl border border-navy-100 bg-neutral-50 p-4 sm:p-5">
                                        <p class="mb-3 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                                            {{ $id ? 'Peserta' : 'Participant' }} <span data-index>{{ $i + 1 }}</span>
                                        </p>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="mb-1.5 block text-sm font-medium text-navy" for="p-name-{{ $i }}">{{ $id ? 'Nama Peserta' : 'Participant Name' }}<span class="text-sky-700" aria-hidden="true"> *</span></label>
                                                <input data-field="name" id="p-name-{{ $i }}" type="text" name="participants[{{ $i }}][name]" value="{{ $p['name'] ?? '' }}" required maxlength="150" autocomplete="off"
                                                       class="{{ $inputClass }}">
                                            </div>
                                            <div>
                                                <label class="mb-1.5 block text-sm font-medium text-navy" for="p-phone-{{ $i }}">{{ $id ? 'No. Telepon' : 'Phone' }}<span class="text-sky-700" aria-hidden="true"> *</span></label>
                                                <input data-field="phone" id="p-phone-{{ $i }}" type="tel" inputmode="tel" name="participants[{{ $i }}][phone]" value="{{ $p['phone'] ?? '' }}" required maxlength="50" pattern="\+?[0-9 \(\)\.\-]{7,50}"
                                                       class="{{ $inputClass }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('participants')<p class="mt-3 text-sm text-rose-600">{{ $message }}</p>@enderror

                            {{-- Blank row template cloned by JS --}}
                            <template data-row-template>
                                <div data-row class="rounded-xl border border-navy-100 bg-neutral-50 p-4 sm:p-5">
                                    <p class="mb-3 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                                        {{ $id ? 'Peserta' : 'Participant' }} <span data-index>1</span>
                                    </p>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Nama Peserta' : 'Participant Name' }}<span class="text-sky-700" aria-hidden="true"> *</span></label>
                                            <input data-field="name" type="text" name="" required maxlength="150" autocomplete="off"
                                                   class="{{ $inputClass }}">
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'No. Telepon' : 'Phone' }}<span class="text-sky-700" aria-hidden="true"> *</span></label>
                                            <input data-field="phone" type="tel" inputmode="tel" name="" required maxlength="50" pattern="\+?[0-9 \(\)\.\-]{7,50}"
                                                   class="{{ $inputClass }}">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="mt-5">
                                <x-field name="notes" type="textarea" :label="$id ? 'Catatan (opsional)' : 'Notes (optional)'" maxlength="1000" />
                            </div>
                        </div>

                        {{-- Kaki formulir: total diulang di samping tombolnya. Di
                             ponsel ringkasan ada jauh di atas, dan orang yang hendak
                             menekan "Lanjut" perlu melihat angkanya di tempat yang
                             sama, terutama setelah menambah peserta. --}}
                        <div class="rounded-2xl border border-navy-100 bg-white p-5 shadow-card sm:p-6">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-xs text-slate-500">{{ $id ? 'Total pembayaran' : 'Total payment' }}</p>
                                    <p data-total class="mt-0.5 font-display text-xl font-semibold tabular-nums text-navy sm:text-2xl">{{ $rupiah($unit * count($rows)) }}</p>
                                </div>
                                <p class="pb-1 text-xs text-slate-500"><span data-count-mirror>{{ count($rows) }}</span> {{ $id ? 'peserta' : 'participant(s)' }}</p>
                            </div>
                            <button type="submit" class="btn-blue mt-4 w-full justify-center">
                                {{ $id ? 'Lanjut ke Pembayaran' : 'Continue to Payment' }}
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <p class="mt-3 flex items-center justify-center gap-2 text-center text-xs text-slate-500">
                                <svg class="h-3.5 w-3.5 shrink-0 text-gold-deep" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                {{ $id ? 'Pembayaran aman lewat Midtrans: QRIS, e-wallet, VA, dan kartu.' : 'Secure payment via Midtrans: QRIS, e-wallet, VA and card.' }}
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const wrap = document.querySelector('[data-participants]');
                if (!wrap) return;

                const list = wrap.querySelector('[data-list]');
                const tpl = wrap.querySelector('[data-row-template]');
                const countEl = wrap.querySelector('[data-count]');
                // Jumlah dan total tampil di dua tempat (ringkasan dan kaki
                // formulir), jadi keduanya diperbarui bersama.
                const countMirrors = document.querySelectorAll('[data-count-mirror]');
                const totalEls = document.querySelectorAll('[data-total]');
                const unit = Number(wrap.dataset.unit || 0);
                const profile = { name: wrap.dataset.profileName || '', phone: wrap.dataset.profilePhone || '' };
                const toggle = wrap.querySelector('[data-profile-toggle]');
                const MAX = 50;

                const rows = () => Array.from(list.querySelectorAll('[data-row]'));

                function renumber() {
                    const rs = rows();
                    rs.forEach((row, i) => {
                        const idx = row.querySelector('[data-index]');
                        if (idx) idx.textContent = i + 1;
                        row.querySelectorAll('input[data-field]').forEach((inp) => {
                            const field = inp.dataset.field;
                            inp.name = 'participants[' + i + '][' + field + ']';
                            inp.id = 'p-' + field + '-' + i;
                            const lbl = inp.previousElementSibling;
                            if (lbl && lbl.tagName === 'LABEL') lbl.htmlFor = inp.id;
                        });
                    });
                    const n = rs.length;
                    if (countEl) countEl.textContent = n;
                    countMirrors.forEach((el) => { el.textContent = n; });
                    totalEls.forEach((el) => { el.textContent = 'Rp ' + (unit * n).toLocaleString('id-ID'); });
                }

                function addRow() {
                    if (rows().length >= MAX) return;
                    const node = tpl.content.firstElementChild.cloneNode(true);
                    list.appendChild(node);
                    renumber();
                }

                function removeRow() {
                    const rs = rows();
                    if (rs.length <= 1) return;
                    rs[rs.length - 1].remove();
                    renumber();
                }

                wrap.querySelector('[data-add]').addEventListener('click', addRow);
                wrap.querySelector('[data-remove]').addEventListener('click', removeRow);

                if (toggle) {
                    toggle.addEventListener('change', () => {
                        const first = rows()[0];
                        if (!first) return;
                        const nameInp = first.querySelector('[data-field="name"]');
                        const phoneInp = first.querySelector('[data-field="phone"]');
                        if (nameInp) nameInp.value = toggle.checked ? profile.name : '';
                        if (phoneInp) phoneInp.value = toggle.checked ? profile.phone : '';
                    });
                }

                renumber();
            })();
        </script>
    @endpush
</x-layout>
