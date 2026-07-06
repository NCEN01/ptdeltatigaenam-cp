<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Resources\TestimonialResource;
use App\Models\Testimonial;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        foreach (['author_position', 'content'] as $field) {
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

        foreach (['author_position', 'content'] as $field) {
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