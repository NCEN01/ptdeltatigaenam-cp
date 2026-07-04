<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ServiceSchedule extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'price_override' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paidOrders(): HasMany
    {
        return $this->hasMany(Order::class)->where('status', 'paid')->latest('paid_at');
    }

    /** Participants (attendees) of this batch, from paid orders only. */
    public function attendees(): HasManyThrough
    {
        return $this->hasManyThrough(
            OrderParticipant::class,
            Order::class,
            'service_schedule_id', // FK on orders -> service_schedules.id
            'order_id',            // FK on order_participants -> orders.id
            'id',
            'id',
        )->where('orders.status', 'paid');
    }

    public function getSeatsAvailableAttribute(): ?int
    {
        return $this->quota === null ? null : max(0, $this->quota - $this->seats_taken);
    }

    /** Whether the batch is upcoming, ongoing, or finished relative to today. */
    public function getTimeframeAttribute(): string
    {
        $today = today();
        $end = $this->end_date ?? $this->start_date;

        if ($this->start_date && $this->start_date->gt($today)) {
            return 'upcoming';
        }

        return $end && $end->gte($today) ? 'ongoing' : 'past';
    }

    public function effectivePrice(): float
    {
        return (float) ($this->price_override ?? $this->service?->price ?? 0);
    }
}
