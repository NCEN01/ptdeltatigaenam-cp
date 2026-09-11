<?php

namespace App\Models;

use App\Filament\Resources\OrderResource;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $guarded = ['id'];

    /**
     * Keep the schedule's seats_taken in sync as an order enters/leaves the
     * "paid" state, so quota tracking stays accurate for event preparation.
     */
    protected static function booted(): void
    {
        static::updated(function (Order $order): void {
            if (! $order->wasChanged('status')) {
                return;
            }

            $wasPaid = $order->getOriginal('status') === 'paid';
            $isPaid = $order->status === 'paid';

            // Keep the schedule's seats_taken in sync on paid enter/leave.
            if ($order->service_schedule_id && ($schedule = $order->schedule)) {
                $seats = (int) $order->quantity;

                if ($isPaid && ! $wasPaid) {
                    $schedule->increment('seats_taken', $seats);
                } elseif ($wasPaid && ! $isPaid) {
                    $schedule->decrement('seats_taken', min((int) $schedule->seats_taken, $seats));
                }
            }

            // A purchase just completed → let the admins know in the panel.
            if ($isPaid && ! $wasPaid) {
                $order->notifyAdminsOfPurchase();
            }
        });
    }

    /**
     * Send an in-panel (database) notification to transaction admins when a
     * customer completes a purchase. Called from the paid transition above so
     * it covers every payment path (Snap return, sandbox sim, and webhook).
     * Wrapped defensively — a notification hiccup must never break checkout.
     */
    public function notifyAdminsOfPurchase(): void
    {
        try {
            $recipients = User::query()
                ->where('is_active', true)
                ->role(['super_admin', 'admin_transaksi'])
                ->get();

            if ($recipients->isEmpty()) {
                return;
            }

            $amount = 'Rp '.number_format((float) $this->total_amount, 0, ',', '.');
            $serviceName = $this->service?->title ?? '—';

            $notification = Notification::make()
                ->title('Pembelian baru diterima')
                ->icon('heroicon-o-banknotes')
                ->iconColor('success')
                ->body("{$this->customer_name} membeli \"{$serviceName}\" · {$amount} · {$this->order_number}");

            try {
                $notification->actions([
                    Action::make('view')
                        ->label('Lihat pesanan')
                        ->url(OrderResource::getUrl('view', ['record' => $this->getKey()]))
                        ->markAsRead(),
                ]);
            } catch (\Throwable $e) {
                // getUrl() needs a panel context; skip the button if unavailable.
            }

            // Write synchronously (notifyNow) instead of sendToDatabase(): Filament's
            // DatabaseNotification is ShouldQueue, so with QUEUE_CONNECTION=database it
            // would sit in the jobs table until a worker runs. notifyNow bypasses the
            // queue so the bell updates immediately after a purchase.
            foreach ($recipients as $recipient) {
                $recipient->notifyNow($notification->toDatabase());
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    protected function casts(): array
    {
        return [
            'participants' => 'array',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ServiceSchedule::class, 'service_schedule_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(OrderParticipant::class);
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    public static function generateNumber(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.strtoupper(bin2hex(random_bytes(3)));
    }
}
