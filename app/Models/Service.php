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
            'discount_active' => 'boolean',
            'discount_original_price' => 'decimal:2',
            'is_purchasable' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'quota' => 'integer',
            'seats_taken' => 'integer',
        ];
    }

    /**
     * Display-only discount: `price` is the real (charged) price; when active,
     * `discount_original_price` is a higher "before" price shown struck-through.
     */
    public function hasDiscount(): bool
    {
        return $this->discount_active
            && $this->discount_original_price !== null
            && (float) $this->discount_original_price > (float) $this->price
            && (float) $this->price > 0;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        $before = (float) $this->discount_original_price;
        $now = (float) $this->price;

        return (int) round((($before - $now) / $before) * 100);
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
