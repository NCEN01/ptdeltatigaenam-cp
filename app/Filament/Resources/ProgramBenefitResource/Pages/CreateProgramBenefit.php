<?php

namespace App\Filament\Resources\ProgramBenefitResource\Pages;

use App\Filament\Resources\ProgramBenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramBenefit extends CreateRecord
{
    protected static string $resource = ProgramBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
