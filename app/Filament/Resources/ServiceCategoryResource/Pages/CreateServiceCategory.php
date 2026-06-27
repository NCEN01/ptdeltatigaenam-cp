<?php

namespace App\Filament\Resources\ServiceCategoryResource\Pages;

use App\Filament\Resources\ServiceCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateServiceCategory extends CreateRecord
{
    use Translatable;

    protected static string $resource = ServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make()];
    }
}
