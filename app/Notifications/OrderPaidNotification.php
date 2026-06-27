<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;

        return (new MailMessage)
            ->subject("Pembayaran Diterima — {$o->order_number}")
            ->greeting("Halo {$o->customer_name},")
            ->line('Terima kasih. Pembayaran Anda telah kami terima.')
            ->line("Nomor Pesanan: {$o->order_number}")
            ->line('Layanan: '.($o->service?->title ?? '-'))
            ->line('Total: Rp '.number_format((float) $o->total_amount, 0, ',', '.'))
            ->line('Tim PT Delta Tiga Enam akan menghubungi Anda untuk langkah berikutnya.')
            ->salutation('Salam, PT Delta Tiga Enam');
    }
}
