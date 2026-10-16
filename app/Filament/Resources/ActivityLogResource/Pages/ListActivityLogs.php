<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    /** Tanpa tombol aksi: catatan ini hanya dibaca, tidak pernah dibuat tangan. */
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getSubheading(): ?string
    {
        return "Setiap penambahan, perubahan, dan penghapusan data lewat panel. Hanya super admin yang bisa membukanya.";
    }
}
