<?php

namespace App\Filament\Resources\ProgramBenefitResource\Pages;

use App\Filament\Resources\ProgramBenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgramBenefits extends ListRecords
{
    protected static string $resource = ProgramBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
