<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOffice extends EditRecord
{
    protected static string $resource = OfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mengisi kolom per bahasa dari terjemahan tersimpan. Tanpa ini, kolom EN
     * tampil kosong saat formulir dibuka, dan menyimpannya akan menghapus
     * terjemahan Inggris yang sudah ada.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        foreach ($this->getRecord()->getTranslations("name") as $locale => $text) {
            if (is_string($text)) {
                $data["name"][$locale] = $text;
            }
        }

        return $data;
    }
}
