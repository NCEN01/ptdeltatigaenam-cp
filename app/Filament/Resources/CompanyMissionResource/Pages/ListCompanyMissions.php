<?php

namespace App\Filament\Resources\CompanyMissionResource\Pages;

use App\Filament\Resources\CompanyMissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListCompanyMissions extends ListRecords
{
    use Translatable;

    protected static string $resource = CompanyMissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
