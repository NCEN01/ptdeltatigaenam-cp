<?php

namespace App\Filament\Resources\CompanyMissionResource\Pages;

use App\Filament\Resources\CompanyMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateCompanyMission extends CreateRecord
{
    use Translatable;

    protected static string $resource = CompanyMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make(),
            ];
    }
}
