<?php

namespace App\Filament\Resources\ServiceCategoryResource\Pages;

use App\Filament\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceCategory extends EditRecord
{
    protected static string $resource = ServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
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

        foreach (['name', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
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