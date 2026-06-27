<?php

namespace App\Filament\Resources\OfficeLocationResource\Pages;

use App\Filament\Resources\OfficeLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateOfficeLocation extends CreateRecord
{
    use Translatable;

    protected static string $resource = OfficeLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make(),
            ];
    }
}
