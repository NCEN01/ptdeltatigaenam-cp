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
                @forelse ($orders as $order)
                    <div class="mb-4 rounded-2xl border border-navy-100 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="font-mono text-xs text-navy-400">{{ $order->order_number }}</p>
                                <h3 class="mt-1 font-display text-lg font-semibold text-navy">{{ optional($order->service)->title ?? ($id ? 'Layanan' : 'Service') }}</h3>
                                <p class="mt-1 text-sm text-navy-400">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-block rounded-full border px-3 py-1 text-xs font-medium {{ $statusColor($order->status) }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                                <p class="mt-2 font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-dashed border-navy-200 p-12 text-center">
                        <p class="font-display text-xl font-semibold text-navy">{{ $id ? 'Belum ada pesanan' : 'No orders yet' }}</p>
                        <p class="mt-2 text-navy-500">{{ $id ? 'Mulai jelajahi layanan kami.' : 'Start exploring our services.' }}</p>
                        <a href="{{ route('services.index') }}" class="btn-primary mt-6">{{ __('site.cta.explore') }}</a>
                    </div>
                @endforelse

                <div class="mt-8">{{ $orders->links() }}</div>
            </div>
        </div>
    </section>
</x-layout>
