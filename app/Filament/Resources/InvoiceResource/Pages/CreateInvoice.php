<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\PartnershipRegistration;
use App\Services\InvoiceService;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(): void
    {
        parent::mount();

        // Prefill when arriving from a registration's "Buat Invoice" action.
        if ($registrationId = request()->query('registration')) {
            if ($reg = PartnershipRegistration::find($registrationId)) {
                $this->form->fill([
                    'partnership_registration_id' => $reg->id,
                    'bill_to_company' => $reg->company_name,
                    'bill_to_address' => $reg->company_address,
                    'bill_to_pic' => $reg->pic_name,
                    'bill_to_email' => $reg->email,
                    'status' => 'draft',
                    'issued_date' => now(),
                ]);
            }
        }
    }

    protected function afterCreate(): void
    {
        app(InvoiceService::class)->recalculate($this->record);

        if ($reg = $this->record->registration) {
            if (in_array($reg->status, ['baru', 'dihubungi', 'meeting_dijadwalkan', 'penawaran_dikirim'], true)) {
                $reg->update(['status' => 'invoice_diterbitkan']);
            }
        }
    }
}
