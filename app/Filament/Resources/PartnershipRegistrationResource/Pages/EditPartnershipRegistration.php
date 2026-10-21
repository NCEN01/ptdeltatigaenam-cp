<?php

namespace App\Filament\Resources\PartnershipRegistrationResource\Pages;

use App\Filament\Resources\PartnershipRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnershipRegistration extends EditRecord
{
    protected static string $resource = PartnershipRegistrationResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (! $this->record->is_read) {
            $this->record->update(['is_read' => true]);
            $this->data['is_read'] = true;
        }
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
