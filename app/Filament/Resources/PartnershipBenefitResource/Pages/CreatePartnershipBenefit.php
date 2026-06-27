<?php

namespace App\Filament\Resources\PartnershipBenefitResource\Pages;

use App\Filament\Resources\PartnershipBenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePartnershipBenefit extends CreateRecord
{
    use Translatable;

    protected static string $resource = PartnershipBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make(),
            ];
    }
}
