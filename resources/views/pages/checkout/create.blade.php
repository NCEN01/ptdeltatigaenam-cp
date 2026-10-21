@php
    $id = app()->getLocale() === 'id';
    $oldParticipants = old('participants');
    $prefillParticipants = (! $oldParticipants && empty($paidOrder) && !empty($pendingOrder?->participants))
        ? $pendingOrder->participants
        : null;
    $rows = $oldParticipants ?: ($prefillParticipants ?: [['name' => '', 'phone' => '']]);
    $unit = (int) $unitPrice;
@endphp

<x-layout :title="'Checkout'">
    <x-page-header :title="$service->title" />

    <section class="section">
        <div class="container">
            <a href="{{ route('services.show', $service->slug) }}" class="mb-8 inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-slate-500 transition-colors hover:text-navy">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $id ? 'Kembali ke detail layanan' : 'Back to service details' }}
            </a>
            <div class="grid gap-8 lg:grid-cols-12 lg:items-start lg:gap-10">
                {{-- Form --}}
                <div class="lg:col-span-7 space-y-6">
                    @if (!empty($paidOrder))
                        <div class="rounded-3xl border border-emerald-200 bg-emerald-50/75 p-6">
                            <div class="flex items-start gap-3.5">
                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                        <div class="rounded-3xl border border-amber-200 bg-amber-50/75 p-6">
                            <div class="flex items-start gap-3.5">
                                <span class="mt-0.5 grid h-9 w-9 shrink-0 place-items-center rounded-full bg-amber-500 text-white">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
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

                    <form id="form-checkout" method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="service_schedule_id" value="{{ $schedule->id }}">
                    @if (!empty($paidOrder))
                        <input type="hidden" name="confirm_repurchase" value="1">
                    @endif

                    @if ($errors->any())
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                            <p class="text-sm font-semibold text-rose-700">{{ $id ? 'Periksa kembali isian berikut:' : 'Please check the following:' }}</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-600">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Buyer details --}}
                    <div class="rounded-3xl border border-navy-100 p-7">
                        <h2 class="font-display text-xl font-semibold text-navy">{{ $id ? 'Data Pemesan' : 'Your Details' }}</h2>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <x-field name="customer_name" :label="__('site.contact.name')" :value="$customer->name" required maxlength="150" />
                            <x-field name="customer_email" type="email" :label="__('site.contact.email')" :value="$customer->email" required maxlength="150" />
                            <x-phone-field name="customer_phone" :label="__('site.contact.phone')" :value="$customer->phone" required />
                            <x-field name="customer_company" :label="$id ? 'Perusahaan' : 'Company'" :value="$customer->company" minlength="2" maxlength="200" />
                        </div>
                    </div>

                    {{-- Participants --}}
                    <div class="rounded-3xl border border-navy-100 p-7"
                         data-participants
                         data-unit="{{ $unit }}"
                         data-profile-name="{{ $customer->name }}"
                         data-profile-phone="{{ $customer->phone }}">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <h2 class="font-display text-xl font-semibold text-navy">{{ $id ? 'Data Peserta' : 'Participant Details' }}</h2>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-slate-600">{{ $id ? 'Jumlah' : 'Count' }}</span>
                                <button type="button" data-remove class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 text-navy transition-colors hover:border-navy" aria-label="-">−</button>
                                <span data-count class="w-8 text-center font-display text-lg font-semibold text-navy">{{ count($rows) }}</span>
                                <button type="button" data-add class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 text-navy transition-colors hover:border-navy" aria-label="+">+</button>
                            </div>
                        </div>

                        {{-- Toggle: fill participant 1 from profile --}}
                        <label class="mt-5 flex cursor-pointer items-center gap-3 rounded-2xl bg-mist px-4 py-3">
                            <input type="checkbox" data-profile-toggle class="peer sr-only">
                            <span class="relative h-6 w-11 shrink-0 rounded-full bg-navy-200 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-sky-500 peer-checked:after:translate-x-5"></span>
                            <span class="text-sm text-navy">{{ $id ? 'Isi Peserta 1 dengan data dari profil saya' : 'Fill Participant 1 with my profile data' }}</span>
                        </label>

                        {{-- Participant rows (server-rendered so they always submit) --}}
                        <div data-list class="mt-5 space-y-4">
                            @foreach ($rows as $i => $p)
                                <div data-row class="rounded-2xl border border-navy-100 p-5">
                                    <p class="mb-4 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                                        {{ $id ? 'Peserta' : 'Participant' }} <span data-index>{{ $i + 1 }}</span>
                                    </p>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-navy" for="p-name-{{ $i }}">{{ $id ? 'Nama Peserta' : 'Participant Name' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                            <input data-field="name" id="p-name-{{ $i }}" type="text" name="participants[{{ $i }}][name]" value="{{ $p['name'] ?? '' }}" required maxlength="150" autocomplete="off"
                                                   class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-navy" for="p-phone-{{ $i }}">{{ $id ? 'No. Telepon' : 'Phone' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                            <input data-field="phone" id="p-phone-{{ $i }}" type="tel" inputmode="tel" name="participants[{{ $i }}][phone]" value="{{ $p['phone'] ?? '' }}" required maxlength="50" pattern="\+?[0-9 \(\)\.\-]{7,50}"
                                                   class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('participants')<p class="mt-3 text-sm text-rose-600">{{ $message }}</p>@enderror

                        {{-- Blank row template cloned by JS --}}
                        <template data-row-template>
                            <div data-row class="rounded-2xl border border-navy-100 p-5">
                                <p class="mb-4 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                                    {{ $id ? 'Peserta' : 'Participant' }} <span data-index>1</span>
                                </p>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Nama Peserta' : 'Participant Name' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                        <input data-field="name" type="text" name="" required maxlength="150" autocomplete="off"
                                               class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'No. Telepon' : 'Phone' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                        <input data-field="phone" type="tel" inputmode="tel" name="" required maxlength="50" pattern="\+?[0-9 \(\)\.\-]{7,50}"
                                               class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-slate-400 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="mt-5">
                            <x-field name="notes" type="textarea" :label="$id ? 'Catatan (opsional)' : 'Notes (optional)'" maxlength="1000" />
                        </div>
                    </div>

                    <button type="submit" class="btn-blue w-full">
                        {{ $id ? 'Lanjut ke Pembayaran' : 'Continue to Payment' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>
            </div>

                {{-- Summary --}}
                <div class="lg:col-span-5">
                    <div class="rounded-3xl border border-navy-100 bg-mist p-6 md:p-7 lg:sticky lg:top-28">
                        <p class="eyebrow mb-5">{{ $id ? 'Ringkasan' : 'Summary' }}</p>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between gap-4"><span class="text-slate-600">{{ $id ? 'Layanan' : 'Service' }}</span><span class="text-right font-medium text-navy">{{ $service->title }}</span></div>
                            <div class="flex justify-between gap-4"><span class="text-slate-600">{{ $id ? 'Jadwal' : 'Schedule' }}</span><span class="text-right font-medium text-navy">{{ $schedule->start_date?->translatedFormat('d M Y') }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-600">Mode</span><span class="font-medium capitalize text-navy">{{ $schedule->mode }}</span></div>
                            <div class="flex justify-between gap-4">
                                <span class="text-slate-600">{{ $id ? 'Harga/peserta' : 'Price/person' }}</span>
                                <span class="text-right">
                                    @if ($service->hasDiscount())
                                        <span class="mr-1.5 font-mono text-xs text-slate-400 line-through">Rp {{ number_format((float) $service->discount_original_price, 0, ',', '.') }}</span>
                                    @endif
                                    <span class="font-medium text-navy">Rp {{ number_format((float) $unitPrice, 0, ',', '.') }}</span>
                                </span>
                            </div>
                            @if ($service->hasDiscount())
                                <div class="flex justify-between"><span class="text-slate-600">{{ $id ? 'Diskon' : 'Discount' }}</span><span class="font-semibold text-rose-600">{{ $id ? 'Hemat' : 'Save' }} {{ $service->discountPercent() }}%</span></div>
                            @endif
                            <div class="flex justify-between border-t border-navy-200/60 pt-3"><span class="text-slate-600">{{ $id ? 'Jumlah peserta' : 'Participants' }}</span><span data-count-mirror class="font-medium text-navy">{{ count($rows) }}</span></div>
                        </div>
                        <div class="mt-5 flex items-end justify-between border-t border-navy-200/60 pt-5">
                            <span class="font-display text-lg font-semibold text-navy">Total</span>
                            <span data-total class="font-display text-2xl font-semibold text-navy">Rp {{ number_format($unit * count($rows), 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-3 text-xs text-slate-500">{{ $id ? 'Total dihitung otomatis sesuai jumlah peserta.' : 'Total is calculated automatically by participant count.' }}</p>
                        @if ($service->hasDiscount())
                            <p class="mt-3 rounded-xl bg-gold/10 px-3 py-2.5 text-xs leading-relaxed text-gold-deep">{{ $id ? 'Harga promo berlaku terbatas, harga akan naik setelah periode promo berakhir.' : 'Promo price for a limited time, the price will rise after the promo ends.' }}</p>
                        @endif
                    </div>
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
                const countMirror = document.querySelector('[data-count-mirror]');
                const totalEl = document.querySelector('[data-total]');
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
                        });
                    });
                    const n = rs.length;
                    if (countEl) countEl.textContent = n;
                    if (countMirror) countMirror.textContent = n;
                    if (totalEl) totalEl.textContent = 'Rp ' + (unit * n).toLocaleString('id-ID');
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
