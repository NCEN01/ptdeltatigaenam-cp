<?php

namespace App\Filament\Resources\PartnershipPackageResource\Pages;

use App\Filament\Resources\PartnershipPackageResource;
use App\Models\PartnershipPackage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnershipPackage extends EditRecord
{
    protected static string $resource = PartnershipPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Text translatable fields: name, tagline, description, price_note
        foreach (['name', 'tagline', 'description', 'price_note'] as $field) {
            $translations = $record->getTranslations($field);
            foreach ($translations as $locale => $text) {
                if (is_string($text)) {
                    $data["{$field}.{$locale}"] = $text;
                }
            }
        }

        // Features — stored as {"id":["a","b"],"en":["x","y"]}
        $features = $record->getTranslations('features');
        foreach ($features as $locale => $items) {
            if (is_array($items)) {
                $data["features.{$locale}"] = array_map(fn ($v) => ['item' => $v], $items);
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $locales = ['id', 'en'];

        // Text translatable fields
        foreach (['name', 'tagline', 'description', 'price_note'] as $field) {
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

        // Features — transform [{"item":"a"},...] to ["a","b",...]
        $features = [];
        foreach ($locales as $locale) {
            $key = "features.{$locale}";
            if (array_key_exists($key, $data) && is_array($data[$key])) {
                $features[$locale] = array_values(array_filter(array_map(
                    fn ($v) => is_array($v) ? ($v['item'] ?? '') : (string) $v,
                    $data[$key]
                ), fn ($v) => $v !== ''));
                unset($data[$key]);
            }
        }
        if (! empty($features)) {
            $data['features'] = $features;
        }

        return $data;
    }
}