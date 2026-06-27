<?php

namespace App\Filament\Resources\PartnershipRegistrationResource\Pages;

use App\Filament\Resources\PartnershipRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnershipRegistration extends EditRecord
{
    protected static string $resource = PartnershipRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
