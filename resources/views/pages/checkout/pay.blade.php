@php
    $id = app()->getLocale() === 'id';
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<x-layout :title="$id ? 'Pembayaran' : 'Payment'">
    <x-page-header :eyebrow="$order->order_number" :title="$id ? 'Selesaikan pembayaran' : 'Complete your payment'" />

    <section class="section">
        <div class="container max-w-2xl">
            <div class="rounded-3xl border border-navy-100 p-8 text-center">
                <p class="eyebrow mb-3">{{ $id ? 'Total Tagihan' : 'Amount Due' }}</p>
                <p class="font-display text-4xl font-semibold text-navy">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                <p class="mt-2 text-navy-500">{{ $order->service?->title }} · {{ $order->quantity }} {{ $id ? 'peserta' : 'participant(s)' }}</p>

                <button id="pay-button" class="btn-gold mt-8 w-full sm:w-auto">
                    {{ $id ? 'Bayar Sekarang' : 'Pay Now' }}
                </button>

                <p class="mt-6 text-xs text-navy-400">{{ $id ? 'Pembayaran diproses aman oleh Midtrans (QRIS, e-wallet, VA, kartu).' : 'Securely processed by Midtrans (QRIS, e-wallet, VA, card).' }}</p>
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
