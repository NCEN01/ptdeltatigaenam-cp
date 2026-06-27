<?php

namespace App\Filament\Resources\OfficeLocationResource\Pages;

use App\Filament\Resources\OfficeLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListOfficeLocations extends ListRecords
{
    use Translatable;

    protected static string $resource = OfficeLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
