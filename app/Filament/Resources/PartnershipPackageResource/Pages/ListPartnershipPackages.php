<?php

namespace App\Filament\Resources\PartnershipPackageResource\Pages;

use App\Filament\Resources\PartnershipPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartnershipPackages extends ListRecords
{
    protected static string $resource = PartnershipPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}