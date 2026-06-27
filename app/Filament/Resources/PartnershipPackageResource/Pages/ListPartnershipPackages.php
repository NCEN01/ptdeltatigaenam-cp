<?php

namespace App\Filament\Resources\PartnershipPackageResource\Pages;

use App\Filament\Resources\PartnershipPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPartnershipPackages extends ListRecords
{
    use Translatable;

    protected static string $resource = PartnershipPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
