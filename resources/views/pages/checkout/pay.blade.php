@php
    $id = app()->getLocale() === 'id';
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<x-layout :title="$id ? 'Pembayaran' : 'Payment'">
    <x-page-header :title="$id ? 'Selesaikan pembayaran' : 'Complete your payment'" />

    <section class="section">
        <div class="container max-w-2xl">
            <a href="{{ route('account.orders') }}" class="mb-6 inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-slate-500 transition-colors hover:text-navy">
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ $id ? 'Kembali ke pesanan saya' : 'Back to my orders' }}
            </a>
            <div class="rounded-3xl border border-navy-100 p-8 text-center">
                <p class="eyebrow mb-3">{{ $id ? 'Total Tagihan' : 'Amount Due' }}</p>
                <p class="font-display text-4xl font-semibold text-navy">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                <p class="mt-2 text-slate-600">{{ $order->service?->title }} · {{ $order->quantity }} {{ $id ? 'peserta' : 'participant(s)' }}</p>

                <button id="pay-button" class="btn-blue mt-8 w-full sm:w-auto">
                    {{ $id ? 'Bayar Sekarang' : 'Pay Now' }}
                </button>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3 border-t border-dashed border-navy-100 pt-6">
                    <form method="POST" action="{{ route('checkout.retry', $order->order_number) }}">
                        @csrf
                        <button type="submit" class="btn-ghost !px-5 !py-2.5 text-sm">
                            {{ $id ? 'Ganti Metode / Ulangi Pembayaran' : 'Change Method / Retry Payment' }}
                        </button>
                    </form>
                    @if ($order->service_schedule_id)
                        <form method="POST" action="{{ route('checkout.cancel', ['order' => $order->order_number, 'recreate' => 1]) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center rounded-full border border-navy-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:border-navy hover:text-navy">
                                {{ $id ? 'Reset & Ubah Data Pesanan' : 'Reset & Edit Order Details' }}
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('checkout.cancel', $order->order_number) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-5 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50">
                            {{ $id ? 'Batalkan Pesanan' : 'Cancel Order' }}
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-xs text-slate-500">{{ $id ? 'Pembayaran diproses aman oleh Midtrans (QRIS, e-wallet, VA, kartu).' : 'Securely processed by Midtrans (QRIS, e-wallet, VA, card).' }}</p>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
        <script>
            const finishUrl = @json(route('checkout.finish', $order->order_number));
            const btn = document.getElementById('pay-button');
            function startPay() {
                if (!window.snap) { window.location = finishUrl; return; }
                window.snap.pay(@json($snapToken), {
                    onSuccess: () => window.location = finishUrl,
                    onPending: () => window.location = finishUrl,
                    onError:   () => window.location = finishUrl,
                    onClose:   () => {},
                });
            }
            btn.addEventListener('click', startPay);
            // Auto-open once the SDK is ready.
            window.addEventListener('load', () => setTimeout(startPay, 400));
        </script>
    @endpush
</x-layout>
