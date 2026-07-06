<?php

namespace App\Filament\Resources\BlogCategoryResource\Pages;

use App\Filament\Resources\BlogCategoryResource;
use App\Models\BlogCategory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlogCategory extends EditRecord
{
    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $translations = $record->getTranslations('name');
        foreach ($translations as $locale => $text) {
            if (is_string($text)) {
                $data["name.{$locale}"] = $text;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $locales = ['id', 'en'];
        $translations = [];
        foreach ($locales as $locale) {
            $key = "name.{$locale}";
            if (array_key_exists($key, $data)) {
                $translations[$locale] = $data[$key] ?? '';
                unset($data[$key]);
            }
        }
        if (! empty($translations)) {
            $data['name'] = $translations;
        }

        return $data;
    }
}