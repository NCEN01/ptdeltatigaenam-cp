<?php

namespace App\Filament\Resources\AgendaResource\Pages;

use App\Filament\Resources\AgendaResource;
use App\Models\Agenda;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgenda extends EditRecord
{
    protected static string $resource = AgendaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        foreach (['title', 'excerpt', 'content'] as $field) {
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

        foreach (['title', 'excerpt', 'content'] as $field) {
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