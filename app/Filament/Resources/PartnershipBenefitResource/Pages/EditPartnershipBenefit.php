<?php

namespace App\Filament\Resources\PartnershipBenefitResource\Pages;

use App\Filament\Resources\PartnershipBenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\EditRecord\Concerns\Translatable;

class EditPartnershipBenefit extends EditRecord
{
    use Translatable;

    protected static string $resource = PartnershipBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
