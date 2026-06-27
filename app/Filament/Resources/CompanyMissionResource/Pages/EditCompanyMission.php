<?php

namespace App\Filament\Resources\CompanyMissionResource\Pages;

use App\Filament\Resources\CompanyMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditCompanyMission extends EditRecord
{
    use Translatable;

    protected static string $resource = CompanyMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
