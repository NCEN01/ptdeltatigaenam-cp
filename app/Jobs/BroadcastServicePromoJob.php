<?php

namespace App\Jobs;

use App\Filament\Resources\ServiceResource;
use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Notifications\NewServicePromoNotification;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastServicePromoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $serviceId,
        public readonly ?string $customMessage = null,
        public readonly ?int $triggeredByUserId = null,
    ) {}

    public function handle(): void
    {
        $service = Service::with('schedules')->find($this->serviceId);
        if (! $service || ! $service->is_active) {
            return;
        }

        $sentCount = 0;

        Customer::query()
            ->where('is_active', true)
            ->whereNotNull('email_verified_at')
            ->orderBy('id')
            ->chunkById(100, function ($customers) use ($service, &$sentCount): void {
                foreach ($customers as $customer) {
                    $customer->notify(new NewServicePromoNotification($service, $this->customMessage));
                    $sentCount++;
                }
            });

        $this->notifyAdminsOfBroadcast($service, $sentCount);
    }

    private function notifyAdminsOfBroadcast(Service $service, int $sentCount): void
    {
        try {
            $recipients = User::query()
                ->where('is_active', true)
                ->when(
                    $this->triggeredByUserId,
                    fn ($q) => $q->where(fn ($sub) => $sub
                        ->whereKey($this->triggeredByUserId)
                        ->orWhereHas('roles', fn ($r) => $r->whereIn('name', ['admin_konten', 'super_admin']))
                    ),
                    fn ($q) => $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['admin_konten', 'super_admin'])),
                )
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            $titleId = $service->getTranslation('title', 'id', false) ?: $service->title;

            $notification = FilamentNotification::make()
                ->title('Broadcast Email Promo Terkirim ke Antrean')
                ->icon('heroicon-o-megaphone')
                ->iconColor('success')
                ->body("Promo layanan \"{$titleId}\" telah dijadwalkan ke {$sentCount} pelanggan aktif.");

            try {
                $notification->actions([
                    Action::make('view')
                        ->label('Lihat Layanan')
                        ->url(ServiceResource::getUrl('edit', ['record' => $service->getKey()], panel: 'admin'))
                        ->markAsRead(),
                ]);
            } catch (\Throwable $e) {
                // Skip action URL if unavailable.
            }

            foreach ($recipients as $recipient) {
                $recipient->notifyNow($notification->toDatabase());
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
