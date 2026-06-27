<?php

namespace App\Filament\Resources\PartnershipBenefitResource\Pages;

use App\Filament\Resources\PartnershipBenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;

class ListPartnershipBenefits extends ListRecords
{
    use Translatable;

    protected static string $resource = PartnershipBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
