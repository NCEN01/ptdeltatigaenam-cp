<?php

namespace App\Filament\Resources\PortfolioResource\Pages;

use App\Filament\Resources\PortfolioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreatePortfolio extends CreateRecord
{
    use Translatable;

    protected static string $resource = PortfolioResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\LocaleSwitcher::make(),
            ];
    }
}
