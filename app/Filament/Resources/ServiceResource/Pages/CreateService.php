<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use App\Jobs\BroadcastServicePromoJob;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function afterCreate(): void
    {
        $rawState = $this->form->getRawState();
        if (! empty($rawState['broadcast_promo_on_create']) && $this->record?->is_active) {
            BroadcastServicePromoJob::dispatch(
                $this->record->id,
                $rawState['broadcast_promo_message'] ?? null,
                auth()->id(),
            );

            Notification::make()
                ->title('Broadcast promo dijadwalkan ke antrean')
                ->body('Email promo untuk layanan baru ini telah dimasukkan ke antrean (queue) untuk seluruh pelanggan aktif.')
                ->success()
                ->send();
        }
    }
}