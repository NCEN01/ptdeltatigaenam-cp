<?php

namespace App\Filament\Resources\InstagramUpdateResource\Pages;

use App\Filament\Resources\InstagramUpdateResource;
use App\Models\InstagramUpdate;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstagramUpdate extends EditRecord
{
    protected static string $resource = InstagramUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        foreach (['batch_label', 'title', 'company', 'date_range'] as $field) {
            $translations = $record->getTranslations($field);
            foreach ($translations as $locale => $text) {
                if (is_string($text)) {
                    $data["{$field}.{$locale}"] = $text;
                }
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $locales = ['id', 'en'];

        foreach (['batch_label', 'title', 'company', 'date_range'] as $field) {
            $translations = [];
            foreach ($locales as $locale) {
                $key = "{$field}.{$locale}";
                if (array_key_exists($key, $data)) {
                    $translations[$locale] = $data[$key] ?? '';
                    unset($data[$key]);
                }
            }
            if (! empty($translations)) {
                $data[$field] = $translations;
            }
        }

        return $data;
    }
}