<?php

namespace App\Notifications;

use App\Models\Service;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewServicePromoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Service $service,
        public readonly ?string $customMessage = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = in_array($notifiable->preferred_locale ?? null, ['id', 'en'], true)
            ? $notifiable->preferred_locale
            : 'id';
        $isId = $locale === 'id';

        $service = $this->service;
        $title = $service->getTranslation('title', $locale, false)
            ?: $service->getTranslation('title', 'id', false)
            ?: (string) $service->title;

        $shortDesc = $service->getTranslation('short_description', $locale, false)
            ?: $service->getTranslation('short_description', 'id', false)
            ?: (string) $service->short_description;

        $priceUnit = trim((string) (
            $service->getTranslation('price_label', $locale, false)
            ?: $service->getTranslation('price_label', 'id', false)
            ?: ($isId ? 'peserta' : 'person')
        ));

        $hasDiscount = $service->hasDiscount();
        $discountPct = $service->discountPercent();

        $subject = $hasDiscount
            ? ($isId
                ? "Promo Hemat {$discountPct}%: {$title}"
                : "Save {$discountPct}% Promo: {$title}")
            : ($isId
                ? "Program & Layanan Terbaru: {$title}"
                : "New Programme & Service: {$title}");

        $recipientName = $notifiable->name ?? ($isId ? 'Pelanggan' : 'Customer');

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($isId ? "Halo {$recipientName}," : "Hello {$recipientName},")
            ->line($isId
                ? "Kami menghadirkan program terbaru dari PT Delta Tiga Enam: **{$title}**."
                : "We are excited to introduce a new programme from PT Delta Tiga Enam: **{$title}**.");

        if (filled($shortDesc)) {
            $mail->line($shortDesc);
        }

        if (filled($this->customMessage)) {
            $mail->line($this->customMessage);
        }

        if ((float) $service->price > 0) {
            $formattedPrice = 'Rp '.number_format((float) $service->price, 0, ',', '.');
            if ($hasDiscount) {
                $formattedOriginal = 'Rp '.number_format((float) $service->discount_original_price, 0, ',', '.');
                $mail->line($isId
                    ? "Harga Promo: **{$formattedPrice} / {$priceUnit}** (dari {$formattedOriginal} — Hemat {$discountPct}%)"
                    : "Promo Price: **{$formattedPrice} / {$priceUnit}** (was {$formattedOriginal} — Save {$discountPct}%)");
            } else {
                $mail->line($isId
                    ? "Investasi: **{$formattedPrice} / {$priceUnit}**"
                    : "Investment: **{$formattedPrice} / {$priceUnit}**");
            }
        }

        $nextSchedule = $service->nextSchedule();
        if ($nextSchedule && $nextSchedule->start_date) {
            $dateText = $nextSchedule->start_date->translatedFormat('d M Y');
            $modeText = $service->modeLabel($locale);
            $locText = $nextSchedule->location ?: $service->location;
            $scheduleParts = array_filter([$dateText, $modeText, $locText]);

            $mail->line(($isId ? 'Jadwal Terdekat: ' : 'Next Schedule: ').implode(' · ', $scheduleParts));
        }

        $url = route('services.show', ['locale' => $locale, 'slug' => $service->slug]);

        return $mail
            ->action($isId ? 'Lihat Detail & Daftar Sekarang' : 'View Details & Register Now', $url)
            ->line($isId
                ? 'Kursi terbatas. Amankan tempat Anda atau tim perusahaan Anda sekarang.'
                : 'Seats are limited. Secure a spot for yourself or your team today.')
            ->salutation($isId ? 'Salam, PT Delta Tiga Enam' : 'Best regards, PT Delta Tiga Enam');
    }
}
