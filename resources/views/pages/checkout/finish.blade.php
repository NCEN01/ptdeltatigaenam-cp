@php
    $id = app()->getLocale() === 'id';
    $state = match ($order->status) {
        'paid' => ['icon' => 'check', 'badge' => 'bg-emerald-50 text-emerald-600', 'title' => $id ? 'Pembayaran Berhasil' : 'Payment Successful', 'desc' => $id ? 'Terima kasih! Pembayaran Anda telah kami terima.' : 'Thank you! Your payment has been received.'],
        'pending' => ['icon' => 'clock', 'badge' => 'bg-amber-50 text-amber-600', 'title' => $id ? 'Menunggu Pembayaran' : 'Awaiting Payment', 'desc' => $id ? 'Selesaikan pembayaran sesuai instruksi yang diberikan.' : 'Please complete the payment per the instructions.'],
        default => ['icon' => 'x', 'badge' => 'bg-rose-50 text-rose-600', 'title' => $id ? 'Pembayaran Belum Selesai' : 'Payment Not Completed', 'desc' => $id ? 'Transaksi dibatalkan atau gagal. Anda dapat mencoba lagi.' : 'The transaction was cancelled or failed. You may try again.'],
    };
@endphp

<x-layout :title="$state['title']">
    <section class="section pt-36 md:pt-44">
        <div class="container max-w-xl text-center">
            <span class="mx-auto grid h-20 w-20 place-items-center rounded-full {{ $state['badge'] }}">
                @if ($state['icon'] === 'check')
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                @elseif ($state['icon'] === 'clock')
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                @else
                    <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                @endif
            </span>

            <h1 class="mt-8 font-display text-display-lg font-semibold text-navy">{{ $state['title'] }}</h1>
            <p class="mt-4 text-navy-500">{{ $state['desc'] }}</p>

            <div class="mx-auto mt-8 max-w-sm rounded-3xl border border-navy-100 p-6 text-left">
                <div class="flex justify-between text-sm"><span class="text-navy-500">{{ $id ? 'Nomor Pesanan' : 'Order Number' }}</span><span class="font-mono text-navy">{{ $order->order_number }}</span></div>
                <div class="mt-2 flex justify-between text-sm"><span class="text-navy-500">{{ $id ? 'Layanan' : 'Service' }}</span><span class="font-medium text-navy">{{ $order->service?->title }}</span></div>
                <div class="mt-2 flex justify-between text-sm"><span class="text-navy-500">Total</span><span class="font-display font-semibold text-navy">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span></div>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('account.orders') }}" class="btn-primary">{{ $id ? 'Lihat Pesanan Saya' : 'View My Orders' }}</a>
                @if ($order->status === 'pending')
                    <a href="{{ route('checkout.pay', $order->order_number) }}" class="btn-ghost">{{ $id ? 'Bayar Lagi' : 'Pay Again' }}</a>
                @elseif ($order->status !== 'paid')
                    <a href="{{ route('services.index') }}" class="btn-ghost">{{ __('site.cta.explore') }}</a>
                @endif
            </div>

            {{-- Sandbox helper: webhook can't reach localhost, so confirm manually. --}}
            @if ($order->status !== 'paid' && ! config('midtrans.is_production'))
                <div class="mx-auto mt-10 max-w-sm rounded-2xl border border-dashed border-emerald-300 bg-emerald-50/50 p-5">
                    <p class="font-mono text-[10px] uppercase tracking-label text-emerald-600">{{ $id ? 'Mode Sandbox' : 'Sandbox Mode' }}</p>
                    <p class="mt-1.5 text-sm text-navy-500">{{ $id ? 'Sudah bayar di Midtrans tapi status belum berubah? Konfirmasi manual di sini.' : 'Paid on Midtrans but status not updated? Confirm manually here.' }}</p>
                    <form method="POST" action="{{ route('checkout.simulate', $order->order_number) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            {{ $id ? 'Simulasi Pembayaran Berhasil' : 'Simulate Successful Payment' }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </section>
</x-layout>
