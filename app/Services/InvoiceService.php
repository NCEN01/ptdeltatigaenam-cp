<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Recompute item amounts, subtotal and total for an invoice.
     */
    public function recalculate(Invoice $invoice): Invoice
    {
        $invoice->loadMissing('items');

        $subtotal = 0;
        foreach ($invoice->items as $item) {
            $amount = (float) $item->quantity * (float) $item->unit_price;
            if ((float) $item->amount !== $amount) {
                $item->update(['amount' => $amount]);
            }
            $subtotal += $amount;
        }

        $invoice->subtotal = $subtotal;
        $invoice->total = $subtotal + (float) $invoice->tax;
        $invoice->saveQuietly();

        return $invoice;
    }

    /**
     * Render the invoice to a PDF stored on the public disk; returns the path.
     */
    public function generatePdf(Invoice $invoice): string
    {
        $invoice->loadMissing(['items', 'registration', 'creator']);

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $invoice])->setPaper('a4');

        $path = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        $invoice->forceFill(['file_path' => $path])->saveQuietly();

        return $path;
    }
}
