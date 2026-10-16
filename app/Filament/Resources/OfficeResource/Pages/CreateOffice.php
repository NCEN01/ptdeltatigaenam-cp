<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOffice extends CreateRecord
{
    protected static string $resource = OfficeResource::class;

    /**
     * Tanpa mutasi data: formulirnya mengirim name sebagai array per bahasa,
     * dan Office::booted() yang meluruskannya saat menyimpan.
     */
}
