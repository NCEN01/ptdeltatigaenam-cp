<?php

namespace App\Models;

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
            if (! $order->wasChanged('status') || ! $order->service_schedule_id) {
                return;
            }

            $schedule = $order->schedule;
            if (! $schedule) {
                return;
            }

            $wasPaid = $order->getOriginal('status') === 'paid';
            $isPaid = $order->status === 'paid';
            $seats = (int) $order->quantity;

            if ($isPaid && ! $wasPaid) {
                $schedule->increment('seats_taken', $seats);
            } elseif ($wasPaid && ! $isPaid) {
                $schedule->decrement('seats_taken', min((int) $schedule->seats_taken, $seats));
            }
        });
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
