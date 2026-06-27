@php $id = app()->getLocale() === 'id'; @endphp

<x-layout :title="'Checkout'">
    <x-page-header :eyebrow="$id ? 'Pemesanan' : 'Checkout'" :title="$service->title" />

    <section class="section">
        <div class="container grid gap-10 lg:grid-cols-12">
            {{-- Form --}}
            <div class="lg:col-span-7">
                <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="service_schedule_id" value="{{ $schedule->id }}">

                    <div class="rounded-3xl border border-navy-100 p-7">
                        <h2 class="font-display text-xl font-semibold text-navy">{{ $id ? 'Data Pemesan' : 'Your Details' }}</h2>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <x-field name="customer_name" :label="__('site.contact.name')" :value="$customer->name" required />
                            <x-field name="customer_email" type="email" :label="__('site.contact.email')" :value="$customer->email" required />
                            <x-field name="customer_phone" :label="__('site.contact.phone')" :value="$customer->phone" required />
                            <x-field name="customer_company" :label="$id ? 'Perusahaan' : 'Company'" :value="$customer->company" />
                        </div>
                    </div>

                    <div class="rounded-3xl border border-navy-100 p-7">
                        <h2 class="font-display text-xl font-semibold text-navy">{{ $id ? 'Jumlah Peserta' : 'Participants' }}</h2>
                        <div class="mt-5 flex items-center gap-4" x-data="{ qty: 1 }">
                            <button type="button" @click="qty = Math.max(1, qty - 1)" class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 text-navy hover:border-navy" aria-label="-">−</button>
                            <input type="number" name="quantity" x-model="qty" min="1" max="50" class="w-20 rounded-2xl border border-navy-200 px-4 py-2.5 text-center text-navy focus:border-navy focus:outline-none focus:ring-2 focus:ring-gold">
                            <button type="button" @click="qty = Math.min(50, qty + 1)" class="grid h-11 w-11 place-items-center rounded-full border border-navy-200 text-navy hover:border-navy" aria-label="+">+</button>
                        </div>
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
                        <div class="flex justify-between border-t border-navy-200/60 pt-3"><span class="text-navy-500">{{ $id ? 'Harga/peserta' : 'Price/person' }}</span><span class="font-medium text-navy">Rp {{ number_format((float) $unitPrice, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="mt-5 flex items-end justify-between border-t border-navy-200/60 pt-5">
                        <span class="font-display text-lg font-semibold text-navy">{{ $id ? 'Mulai dari' : 'From' }}</span>
                        <span class="font-display text-2xl font-semibold text-navy">Rp {{ number_format((float) $unitPrice, 0, ',', '.') }}</span>
                    </div>
                    <p class="mt-3 text-xs text-navy-400">{{ $id ? 'Total final dihitung sesuai jumlah peserta saat pembayaran.' : 'Final total is computed by participant count at payment.' }}</p>
                </div>
            </div>
        </div>
    </section>
</x-layout>
