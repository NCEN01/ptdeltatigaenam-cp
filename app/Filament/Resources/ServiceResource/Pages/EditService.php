<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Jobs\BroadcastServicePromoJob;
use App\Models\Service;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('broadcastPromo')
                ->label('Broadcast Email Promo')
                ->icon('heroicon-o-megaphone')
                ->color('gold')
                ->requiresConfirmation()
                ->modalHeading('Broadcast Email Promo Layanan')
                ->modalDescription(fn (Service $record): string => 'Email promo untuk "' . ($record->getTranslation('title', 'id', false) ?: $record->title) . '" akan dikirim melalui antrean (queue) ke seluruh pelanggan aktif yang emailnya sudah terverifikasi.')
                ->form([
                    Forms\Components\Textarea::make('custom_message')
                        ->label('Pesan Tambahan Promo (Opsional)')
                        ->placeholder('Contoh: Kuota angkatan pertama terbatas, daftar sebelum harga promo berakhir.')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->action(function (Service $record, array $data): void {
                    if (! $record->is_active) {
                        Notification::make()
                            ->title('Layanan masih berstatus nonaktif/draft')
                            ->body('Simpan dan aktifkan layanan terlebih dahulu sebelum mengirim broadcast email promo.')
                            ->warning()
                            ->send();

                        return;
                    }

                    BroadcastServicePromoJob::dispatch(
                        $record->id,
                        $data['custom_message'] ?? null,
                        auth()->id(),
                    );

                    Notification::make()
                        ->title('Broadcast promo masuk antrean')
                        ->body('Email promo untuk "' . ($record->getTranslation('title', 'id', false) ?: $record->title) . '" sedang diproses di antrean queue.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        foreach (['title', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
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

        foreach (['title', 'short_description', 'description', 'meta_title', 'meta_description'] as $field) {
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