<?php

namespace App\Models;

use App\Filament\Resources\ContactMessageResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'locale',
        'is_read',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }

    /**
     * Kirim notifikasi database Filament secara sinkron (notifyNow) ke Admin Konten
     * dan Super Admin beserta tautan langsung ke detail pesan kontak.
     */
    public function notifyAdmins(): void
    {
        try {
            $recipients = User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin_konten', 'super_admin']))
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            $summary = $this->subject ?: Str::limit((string) $this->message, 60);

            $notification = Notification::make()
                ->title('Pesan Kontak Baru')
                ->icon('heroicon-o-envelope')
                ->iconColor('info')
                ->body("{$this->name} ({$this->email}) · {$summary}");

            try {
                $notification->actions([
                    Action::make('view')
                        ->label('Lihat pesan')
                        ->url(ContactMessageResource::getUrl('view', ['record' => $this->getKey()], panel: 'admin'))
                        ->markAsRead(),
                ]);
            } catch (\Throwable $e) {
                // Skip action URL if route resolution is unavailable.
            }

            foreach ($recipients as $recipient) {
                $recipient->notifyNow($notification->toDatabase());
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
