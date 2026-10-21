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
    <x-page-header :title="$id ? 'Riwayat Pesanan' : 'Order History'" />

    <section class="section">
        <div class="container grid gap-10 lg:grid-cols-12">
            <aside class="lg:col-span-3"><x-account-nav /></aside>

            <div class="lg:col-span-9">
                <a href="{{ route('services.index') }}" class="mb-4 inline-flex items-center gap-2 font-mono text-xs uppercase tracking-wider text-slate-500 transition-colors hover:text-navy">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none"><path d="M10 3 5 8l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $id ? 'Kembali ke layanan' : 'Back to services' }}
                </a>
                @forelse ($orders as $order)
                    {{-- .card, kelas yang sama dengan bidang berisi lain di situs
                         ini. Sebelumnya sudut, tepi, dan bayangannya disusun
                         sendiri, jadi kartu pesanan tidak pernah sebangun dengan
                         kartu mana pun. --}}
                    <div class="card mb-4 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-mono text-xs text-slate-500">{{ $order->order_number }}</p>
                                <h3 class="mt-1 font-display text-lg font-semibold text-navy">{{ optional($order->service)->title ?? ($id ? 'Layanan' : 'Service') }}</h3>

                                {{-- Nama peserta dan perusahaan pemesan ditampilkan
                                     apa adanya dari pesanannya. Keduanya tersimpan
                                     di kolom terpisah — customer_name dan
                                     customer_company — dan menampilkannya di sini
                                     membuat pemesan bisa memeriksa sendiri bahwa
                                     yang tercatat memang yang ia maksud, tanpa
                                     perlu bertanya. --}}
                                <dl class="mt-2.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-600">
                                    <dt class="sr-only">{{ __('site.contact.name') }}</dt>
                                    <dd>{{ $order->customer_name }}</dd>
                                    @if ($order->customer_company)
                                        <span class="text-navy-200" aria-hidden="true">·</span>
                                        <dt class="sr-only">{{ $id ? 'Perusahaan' : 'Company' }}</dt>
                                        <dd class="font-medium text-navy">{{ $order->customer_company }}</dd>
                                    @endif
                                </dl>

                                <p class="mt-1.5 text-sm text-slate-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-block rounded-full border px-3 py-1 text-xs font-medium {{ $statusColor($order->status) }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                                <p class="mt-2 font-display text-lg font-semibold text-navy">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        @if (in_array($order->status, ['pending', 'expired', 'failed'], true))
                            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-dashed border-navy-100 pt-4">
                                @if ($order->status === 'pending')
                                    <a href="{{ route('checkout.pay', $order->order_number) }}" class="btn-blue !px-5 !py-2.5 text-sm">{{ $id ? 'Lanjutkan pembayaran' : 'Continue payment' }}</a>
                                @endif
                                <form method="POST" action="{{ route('checkout.retry', $order->order_number) }}">
                                    @csrf
                                    <button type="submit" class="btn-ghost !px-5 !py-2.5 text-sm">
                                        {{ $order->status === 'pending'
                                            ? ($id ? 'Ganti Metode / Ulangi Bayar' : 'Change Method / Retry')
                                            : ($id ? 'Coba Bayar Lagi' : 'Retry Payment') }}
                                    </button>
                                </form>
                                @if ($order->status === 'pending')
                                    <form method="POST" action="{{ route('checkout.cancel', $order->order_number) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center rounded-full border border-rose-200 bg-white px-5 py-2.5 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50">
                                            {{ $id ? 'Batalkan' : 'Cancel' }}
                                        </button>
                                    </form>
                                @endif
                                @if (! config('midtrans.is_production') && ! app()->environment('production'))
                                    <form method="POST" action="{{ route('checkout.simulate', $order->order_number) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            {{ $id ? 'Simulasi Bayar (Sandbox)' : 'Simulate Payment (Sandbox)' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    {{-- Ikon keadaan kosong, sama bentuknya dengan halaman daftar
                         lain: ubin navy dengan lambang emas. Tanpa itu, halaman
                         ini satu-satunya keadaan kosong yang hanya berisi tulisan. --}}
                    <div class="rounded-3xl border border-dashed border-navy-200 bg-mist p-12 text-center">
                        <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-navy text-gold" aria-hidden="true">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h9l3 3v17H6z"/><path d="M9 9h6M9 13h6M9 17h4"/></svg>
                        </span>
                        <p class="mt-5 font-display text-xl font-semibold text-navy">{{ $id ? 'Belum ada pesanan' : 'No orders yet' }}</p>
                        <p class="mt-2 text-slate-600">{{ $id ? 'Pesanan yang Anda buat akan tercatat di sini beserta status pembayarannya.' : 'Orders you place will appear here along with their payment status.' }}</p>
                        <a href="{{ route('services.index') }}" class="btn-blue mt-6">{{ __('site.cta.explore') }}</a>
                    </div>
                @endforelse

                {{-- pagination.brand, bukan tampilan bawaan Laravel. Keempat
                     halaman berhalaman lain di situs ini memakainya; hanya di
                     sini yang terlewat, sehingga halaman akun menampilkan
                     penomoran Tailwind polos yang tidak menyerupai apa pun. --}}
                @if ($orders->hasPages())
                    <div class="mt-8">{{ $orders->links('pagination.brand') }}</div>
                @endif
            </div>
        </div>
    </section>
</x-layout>
