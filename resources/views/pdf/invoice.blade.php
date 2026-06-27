<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1e293b; font-size: 12px; margin: 0; }
        .wrap { padding: 36px 40px; }
        .top { width: 100%; border-bottom: 3px solid #0A2A5E; padding-bottom: 16px; }
        .top td { vertical-align: top; }
        .brand { font-size: 22px; font-weight: bold; color: #0A2A5E; letter-spacing: .5px; }
        .muted { color: #64748b; }
        .doc-title { font-size: 28px; color: #0A2A5E; font-weight: bold; text-align: right; }
        .meta { margin-top: 22px; width: 100%; }
        .meta td { vertical-align: top; width: 50%; }
        .label { text-transform: uppercase; font-size: 9px; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.items th { background: #0A2A5E; color: #fff; text-align: left; padding: 9px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
        table.items td { padding: 9px 10px; border-bottom: 1px solid #e2e8f0; }
        .right { text-align: right; }
        .totals { width: 42%; margin-left: 58%; margin-top: 16px; }
        .totals td { padding: 6px 10px; }
        .totals .grand { background: #0A2A5E; color: #fff; font-weight: bold; font-size: 14px; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 10px;
                  text-transform: uppercase; letter-spacing: .5px; border: 1px solid #C9A227; color: #8a6d12; }
        .foot { margin-top: 40px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 10px; }
    </style>
</head>
<body>
@php
    $fmt = fn ($n) => 'Rp '.number_format((float) $n, 0, ',', '.');
@endphp
<div class="wrap">
    <table class="top">
        <tr>
            <td>
                <div class="brand">PT DELTA TIGA ENAM</div>
                <div class="muted">Human Capital · Training · Certification</div>
                <div class="muted">info@deltatigaenam.com</div>
            </td>
            <td>
                <div class="doc-title">INVOICE</div>
                <div class="right muted">{{ $invoice->invoice_number }}</div>
                <div class="right" style="margin-top:6px"><span class="status">{{ strtoupper($invoice->status) }}</span></div>
            </td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td>
                <div class="label">Ditagihkan Kepada</div>
                <strong>{{ $invoice->bill_to_company }}</strong><br>
                @if ($invoice->bill_to_pic){{ $invoice->bill_to_pic }}<br>@endif
                @if ($invoice->bill_to_address){{ $invoice->bill_to_address }}<br>@endif
                @if ($invoice->bill_to_email){{ $invoice->bill_to_email }}@endif
            </td>
            <td class="right">
                <div class="label">Tanggal Terbit</div>
                {{ optional($invoice->issued_date)->format('d M Y') ?? '—' }}<br><br>
                <div class="label">Jatuh Tempo</div>
                {{ optional($invoice->due_date)->format('d M Y') ?? '—' }}
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:50%">Deskripsi</th>
                <th class="right">Qty</th>
                <th class="right">Harga Satuan</th>
                <th class="right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ $fmt($item->unit_price) }}</td>
                    <td class="right">{{ $fmt($item->amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Belum ada item.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="muted">Subtotal</td><td class="right">{{ $fmt($invoice->subtotal) }}</td></tr>
        <tr><td class="muted">Pajak</td><td class="right">{{ $fmt($invoice->tax) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="right">{{ $fmt($invoice->total) }}</td></tr>
    </table>

    @if ($invoice->notes)
        <div style="margin-top:28px"><div class="label">Catatan</div>{{ $invoice->notes }}</div>
    @endif

    <div class="foot muted">
        Invoice ini diterbitkan untuk Program Kemitraan Corporate Training PT Delta Tiga Enam.
        Pembayaran dilakukan melalui transfer sesuai instruksi yang dikirimkan terpisah.
    </div>
</div>
</body>
</html>
