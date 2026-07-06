<?php

namespace App\Filament\Resources\InstagramUpdateResource\Pages;

use App\Filament\Resources\InstagramUpdateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstagramUpdates extends ListRecords
{
    protected static string $resource = InstagramUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}