<?php

namespace App\Filament\Resources\PartnershipPackageResource\Pages;

use App\Filament\Resources\PartnershipPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPartnershipPackage extends EditRecord
{
    use Translatable;

    protected static string $resource = PartnershipPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
