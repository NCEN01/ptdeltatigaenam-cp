<?php

namespace App\Filament\Resources\CertificateHolderResource\Pages;

use App\Filament\Resources\CertificateHolderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCertificateHolders extends ListRecords
{
    protected static string $resource = CertificateHolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
