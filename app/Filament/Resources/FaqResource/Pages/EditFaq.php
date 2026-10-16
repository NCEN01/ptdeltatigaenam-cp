<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFaq extends EditRecord
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Mengisi kolom per bahasa dari terjemahan yang tersimpan.
     *
     * Dibutuhkan karena Spatie mengembalikan satu bahasa saja lewat pembaca
     * atributnya, sedangkan formulir ini punya kolom terpisah untuk ID dan EN.
     * Tanpa ini, membuka formulir sunting menampilkan kolom EN yang kosong,
     * dan menyimpannya akan menghapus terjemahan Inggris yang sudah ada.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        foreach (['question', 'answer'] as $field) {
            foreach ($record->getTranslations($field) as $locale => $text) {
                if (is_string($text)) {
                    $data[$field][$locale] = $text;
                }
            }
        }

        return $data;
    }
}
