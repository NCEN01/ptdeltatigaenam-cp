<?php

namespace App\Filament\Resources\PartnershipPackageResource\Pages;

use App\Filament\Resources\PartnershipPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePartnershipPackage extends CreateRecord
{
    use Translatable;

    protected static string $resource = PartnershipPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make(),
            ];
    }
}
