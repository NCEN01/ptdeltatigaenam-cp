<?php

namespace App\Models;

use App\Filament\Resources\ServiceResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Agenda extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['title', 'excerpt', 'content'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Agenda $agenda): void {
            if ($agenda->shouldAutoDraftToService()) {
                $agenda->draftToService();
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Slug layanan yang berpadanan dengan agenda ini (dibatasi 220 karakter sesuai kolom services.slug).
     */
    public function serviceSlug(): string
    {
        return Str::limit((string) $this->slug, 220, '');
    }

    /**
     * Layanan (draft maupun aktif) yang sudah terbentuk dari agenda ini.
     */
    public function draftedService(): ?Service
    {
        if (blank($this->slug)) {
            return null;
        }

        return Service::where('slug', $this->serviceSlug())->first();
    }

    /**
     * Apakah agenda ini memenuhi syarat auto-draft otomatis ke Layanan:
     * berstatus published dan waktu mulainya tinggal <= 7 hari dari sekarang.
     */
    public function shouldAutoDraftToService(): bool
    {
        if ($this->status !== 'published' || ! $this->starts_at) {
            return false;
        }

        return $this->starts_at->gte(now()->startOfDay())
            && $this->starts_at->lte(now()->addDays(7)->endOfDay());
    }

    /**
     * Buat draft Service (is_active = false) beserta ServiceSchedule dari agenda ini
     * dan kirim notifikasi review ke Admin Konten & Super Admin.
     * Mengembalikan Service baru jika berhasil dibuat, atau null jika sudah pernah dibuat.
     */
    public function draftToService(): ?Service
    {
        if (blank($this->slug) || $this->draftedService()) {
            return null;
        }

        $service = DB::transaction(function (): ?Service {
            $slug = $this->serviceSlug();
            if (Service::where('slug', $slug)->lockForUpdate()->exists()) {
                return null;
            }

            $category = ServiceCategory::where('is_active', true)->orderBy('sort_order')->first()
                ?? ServiceCategory::first()
                ?? ServiceCategory::create([
                    'name' => ['id' => 'Pelatihan', 'en' => 'Training'],
                    'slug' => 'pelatihan',
                    'is_active' => true,
                    'sort_order' => 1,
                ]);

            $titleId = $this->getTranslation('title', 'id', false) ?: (string) $this->title;
            $titleEn = $this->getTranslation('title', 'en', false) ?: $titleId;

            $excerptId = (string) ($this->getTranslation('excerpt', 'id', false) ?: $this->excerpt ?: '');
            $excerptEn = (string) ($this->getTranslation('excerpt', 'en', false) ?: $excerptId);

            $contentId = (string) ($this->getTranslation('content', 'id', false) ?: $this->content ?: '');
            $contentEn = (string) ($this->getTranslation('content', 'en', false) ?: $contentId);

            $days = ($this->starts_at && $this->ends_at && $this->ends_at->gte($this->starts_at))
                ? max(1, (int) $this->starts_at->copy()->startOfDay()->diffInDays($this->ends_at->copy()->startOfDay()) + 1)
                : 1;

            $duration = [
                'id' => "{$days} hari",
                'en' => $days === 1 ? '1 day' : "{$days} days",
            ];

            $locLower = mb_strtolower((string) $this->location);
            $mode = (str_contains($locLower, 'online') || str_contains($locLower, 'daring') || str_contains($locLower, 'zoom'))
                ? 'online'
                : (str_contains($locLower, 'hybrid') || str_contains($locLower, 'bauran') ? 'hybrid' : 'offline');

            $service = Service::create([
                'service_category_id' => $category->id,
                'title' => ['id' => Str::limit($titleId, 220, ''), 'en' => Str::limit($titleEn, 220, '')],
                'slug' => $slug,
                'short_description' => [
                    'id' => Str::limit($excerptId, 250, ''),
                    'en' => Str::limit($excerptEn, 250, ''),
                ],
                'description' => ['id' => $contentId, 'en' => $contentEn],
                'duration' => $duration,
                'location' => $this->location,
                'mode' => $mode,
                'image' => $this->image,
                'price' => 0,
                'is_purchasable' => true,
                'is_featured' => false,
                'is_active' => false,
            ]);

            if ($this->starts_at) {
                $service->schedules()->create([
                    'start_date' => $this->starts_at->toDateString(),
                    'end_date' => ($this->ends_at ?? $this->starts_at)->toDateString(),
                    'start_time' => $this->starts_at->format('H:i'),
                    'end_time' => $this->ends_at?->format('H:i'),
                    'location' => $this->location,
                    'mode' => $mode,
                    'is_active' => true,
                ]);
            }

            return $service;
        });

        if ($service) {
            $this->notifyAdminsForServiceReview($service);
        }

        return $service;
    }

    /**
     * Kirim notifikasi database Filament ke Admin Konten & Super Admin untuk me-review
     * draft Layanan yang otomatis dibuat dari Agenda.
     */
    public function notifyAdminsForServiceReview(Service $service): void
    {
        try {
            $recipients = User::query()
                ->where('is_active', true)
                ->whereHas('roles', fn ($q) => $q->whereIn('name', ['admin_konten', 'super_admin']))
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            $dateLabel = $this->starts_at ? $this->starts_at->translatedFormat('d M Y') : '-';
            $titleId = $service->getTranslation('title', 'id', false) ?: $service->title;

            $notification = Notification::make()
                ->title('Draft Layanan dari Agenda Siap Direview')
                ->icon('heroicon-o-calendar-days')
                ->iconColor('warning')
                ->body("Agenda \"{$titleId}\" (mulai {$dateLabel}) telah disiapkan menjadi draft layanan. Silakan lengkapi harga/kategori lalu aktifkan.");

            try {
                $notification->actions([
                    Action::make('review')
                        ->label('Review Draft Layanan')
                        ->url(ServiceResource::getUrl('edit', ['record' => $service->getKey()], panel: 'admin'))
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

    /**
     * Pindai seluruh agenda published yang akan mulai dalam <= 7 hari ke depan
     * dan buat draft Layanan bagi yang belum memilikinya.
     */
    public static function syncUpcomingAgendasToDraftServices(): int
    {
        $agendas = static::published()
            ->whereNotNull('starts_at')
            ->whereBetween('starts_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->orderBy('starts_at')
            ->get();

        $draftedCount = 0;

        foreach ($agendas as $agenda) {
            if ($agenda->draftToService()) {
                $draftedCount++;
            }
        }

        return $draftedCount;
    }
}
