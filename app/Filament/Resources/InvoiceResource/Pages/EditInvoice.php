<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function afterSave(): void
    {
        app(InvoiceService::class)->recalculate($this->record);

        // Keep the linked registration in sync when marked paid.
        if ($this->record->status === 'lunas' && $reg = $this->record->registration) {
            $reg->update(['status' => 'lunas']);
        }
    }
}
