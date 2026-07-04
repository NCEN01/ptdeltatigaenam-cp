@php
    $id = app()->getLocale() === 'id';
    $oldParticipants = old('participants');
    $rows = $oldParticipants ?: [['name' => '', 'phone' => '']];
    $unit = (int) $unitPrice;
@endphp

<x-layout :title="'Checkout'">
    <x-page-header :eyebrow="$id ? 'Pemesanan' : 'Checkout'" :title="$service->title" />

    <section class="section">
        <div class="container grid gap-10 lg:grid-cols-12">
            {{-- Form --}}
            <div class="lg:col-span-7">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="service_schedule_id" value="{{ $schedule->id }}">

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
                            <x-field name="customer_name" :label="__('site.contact.name')" :value="$customer->name" required />
                            <x-field name="customer_email" type="email" :label="__('site.contact.email')" :value="$customer->email" required />
                            <x-field name="customer_phone" :label="__('site.contact.phone')" :value="$customer->phone" required />
                            <x-field name="customer_company" :label="$id ? 'Perusahaan' : 'Company'" :value="$customer->company" />
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
                                <span class="text-sm text-navy-500">{{ $id ? 'Jumlah' : 'Count' }}</span>
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
                                    <p class="mb-4 font-mono text-[11px] uppercase tracking-wider text-navy-400">
                                        {{ $id ? 'Peserta' : 'Participant' }} <span data-index>{{ $i + 1 }}</span>
                                    </p>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-navy" for="p-name-{{ $i }}">{{ $id ? 'Nama Peserta' : 'Participant Name' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                            <input data-field="name" id="p-name-{{ $i }}" type="text" name="participants[{{ $i }}][name]" value="{{ $p['name'] ?? '' }}" required
                                                   class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-navy-300 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-navy" for="p-phone-{{ $i }}">{{ $id ? 'No. Telepon' : 'Phone' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                            <input data-field="phone" id="p-phone-{{ $i }}" type="tel" name="participants[{{ $i }}][phone]" value="{{ $p['phone'] ?? '' }}" required
                                                   class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-navy-300 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('participants')<p class="mt-3 text-sm text-rose-600">{{ $message }}</p>@enderror

                        {{-- Blank row template cloned by JS --}}
                        <template data-row-template>
                            <div data-row class="rounded-2xl border border-navy-100 p-5">
                                <p class="mb-4 font-mono text-[11px] uppercase tracking-wider text-navy-400">
                                    {{ $id ? 'Peserta' : 'Participant' }} <span data-index>1</span>
                                </p>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'Nama Peserta' : 'Participant Name' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                        <input data-field="name" type="text" name="" required
                                               class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-navy-300 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-navy">{{ $id ? 'No. Telepon' : 'Phone' }}<span class="text-gold" aria-hidden="true"> *</span></label>
                                        <input data-field="phone" type="tel" name="" required
                                               class="w-full rounded-2xl border border-navy-200 bg-white px-4 py-3 text-navy shadow-sm transition-colors placeholder:text-navy-300 focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="mt-5">
                            <x-field name="notes" type="textarea" :label="$id ? 'Catatan (opsional)' : 'Notes (optional)'" />
                        </div>
                    </div>

                    <button type="submit" class="btn-gold w-full">
                        {{ $id ? 'Lanjut ke Pembayaran' : 'Continue to Payment' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </form>
            </div>

            {{-- Summary --}}
            <div class="lg:col-span-5">
                <div class="sticky top-28 rounded-3xl border border-navy-100 bg-mist p-7">
                    <p class="eyebrow mb-5">{{ $id ? 'Ringkasan' : 'Summary' }}</p>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><span class="text-navy-500">{{ $id ? 'Layanan' : 'Service' }}</span><span class="text-right font-medium text-navy">{{ $service->title }}</span></div>
                        <div class="flex justify-between"><span class="text-navy-500">{{ $id ? 'Jadwal' : 'Schedule' }}</span><span class="font-medium text-navy">{{ $schedule->start_date?->translatedFormat('d M Y') }}</span></div>
                        <div class="flex justify-between"><span class="text-navy-500">Mode</span><span class="font-medium capitalize text-navy">{{ $schedule->mode }}</span></div>
                        <div class="flex justify-between"><span class="text-navy-500">{{ $id ? 'Harga/peserta' : 'Price/person' }}</span><span class="font-medium text-navy">Rp {{ number_format((float) $unitPrice, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t border-navy-200/60 pt-3"><span class="text-navy-500">{{ $id ? 'Jumlah peserta' : 'Participants' }}</span><span data-count-mirror class="font-medium text-navy">{{ count($rows) }}</span></div>
                    </div>
                    <div class="mt-5 flex items-end justify-between border-t border-navy-200/60 pt-5">
                        <span class="font-display text-lg font-semibold text-navy">Total</span>
                        <span data-total class="font-display text-2xl font-semibold text-navy">Rp {{ number_format($unit * count($rows), 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-3 text-xs text-navy-400">{{ $id ? 'Total dihitung otomatis sesuai jumlah peserta.' : 'Total is calculated automatically by participant count.' }}</p>
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
