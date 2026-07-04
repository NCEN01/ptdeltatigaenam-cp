<?php

namespace App\Filament\Resources\CertificateHolderResource\Pages;

use App\Filament\Resources\CertificateHolderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCertificateHolder extends EditRecord
{
    protected static string $resource = CertificateHolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
