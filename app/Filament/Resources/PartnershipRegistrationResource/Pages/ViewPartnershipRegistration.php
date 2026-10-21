<?php

namespace App\Filament\Resources\PartnershipRegistrationResource\Pages;

use App\Filament\Resources\PartnershipRegistrationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPartnershipRegistration extends ViewRecord
{
    protected static string $resource = PartnershipRegistrationResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (! $this->record->is_read) {
            $this->record->update(['is_read' => true]);
        }
    }
}
