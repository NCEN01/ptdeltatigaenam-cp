<?php

namespace App\Filament\Resources\OfficeLocationResource\Pages;

use App\Filament\Resources\OfficeLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditOfficeLocation extends EditRecord
{
    use Translatable;

    protected static string $resource = OfficeLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
