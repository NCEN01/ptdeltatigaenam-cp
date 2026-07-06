<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = [
        'title', 'short_description', 'description', 'duration',
        'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_purchasable' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'quota' => 'integer',
            'seats_taken' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ServiceActivity::class)->orderBy('sort_order');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ServiceSchedule::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getAvailableSeatsAttribute(): ?int
    {
        if ($this->quota === null) {
            return null;
        }

        return max(0, (int) $this->quota - (int) $this->seats_taken);
    }
}
