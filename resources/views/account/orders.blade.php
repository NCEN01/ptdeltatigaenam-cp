@php
    $id = app()->getLocale() === 'id';
    $statusLabels = $id
        ? ['pending' => 'Menunggu', 'paid' => 'Lunas', 'expired' => 'Kedaluwarsa', 'cancelled' => 'Dibatalkan', 'failed' => 'Gagal', 'refunded' => 'Dikembalikan']
        : ['pending' => 'Pending', 'paid' => 'Paid', 'expired' => 'Expired', 'cancelled' => 'Cancelled', 'failed' => 'Failed', 'refunded' => 'Refunded'];
    $statusColor = fn ($s) => match ($s) {
        'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        default => 'bg-rose-50 text-rose-700 border-rose-200',
    };
@endphp

<x-layout :title="$id ? 'Riwayat Pesanan' : 'Order History'">
    <x-page-header :eyebrow="__('site.nav.account')" :title="$id ? 'Riwayat Pesanan' : 'Order History'" />

    <section class="section">
        <div class="container grid gap-10 lg:grid-cols-12">
            <aside class="lg:col-span-3"><x-account-nav /></aside>

            <div class="lg:col-span-9">
                <a href="{{ route('services.index') }}" class="mb-4 inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-slate-500 transition-colors hover:text-navy">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $id ? 'Kembali ke layanan' : 'Back to services' }}
                </a>
                @forelse ($orders as $order)
                    <div class="mb-4 rounded-2xl border border-navy-100 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="font-mono text-xs text-slate-500">{{ $order->order_number }}</p>
                                <h3 class="mt-1 font-display text-lg font-semibold text-navy">{{ optional($order->service)->title ?? ($id ? 'Layanan' : 'Service') }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block rounded-full border px-3 py-1 text-xs font-medium {{ $statusColor($order->status) }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                                <p class="mt-2 font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if ($order->status !== 'paid' && ! config('midtrans.is_production'))
                            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-dashed border-navy-100 pt-4">
                                <a href="{{ route('checkout.pay', $order->order_number) }}" class="btn-ghost !px-5 !py-2.5 text-sm">{{ $id ? 'Lanjutkan pembayaran' : 'Continue payment' }}</a>
                                <form method="POST" action="{{ route('checkout.simulate', $order->order_number) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $id ? 'Simulasi Bayar (Sandbox)' : 'Simulate Payment (Sandbox)' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-navy-200 p-12 text-center">
                        <p class="font-display text-xl font-semibold text-navy">{{ $id ? 'Belum ada pesanan' : 'No orders yet' }}</p>
                        <p class="mt-2 text-slate-600">{{ $id ? 'Mulai jelajahi layanan kami.' : 'Start exploring our services.' }}</p>
                        <a href="{{ route('services.index') }}" class="btn-primary mt-6">{{ __('site.cta.explore') }}</a>
                    </div>
                @endforelse

                <div class="mt-8">{{ $orders->links() }}</div>
            </div>
        </div>
    </section>
</x-layout>
