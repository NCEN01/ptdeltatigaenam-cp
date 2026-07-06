<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Banner extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['title', 'subtitle', 'button_text'];

    /**
     * Guard against double-nested translations like {"id":{"id":"x","en":"y"}},
     * which the Filament Translatable concern can produce when form data
     * already supplies a full per-locale array. Keep values flat.
     */
    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            foreach ($model->translatable as $field) {
                $value = $model->getTranslations($field);
                if (! is_array($value)) {
                    continue;
                }

                $nested = collect($value)->first(fn ($v) => is_array($v));
                if ($nested === null) {
                    continue;
                }

                $flat = [];
                foreach ($nested as $loc => $txt) {
                    $flat[$loc] = is_array($txt) ? (string) (reset($txt) ?: '') : (string) $txt;
                }
                $model->setTranslations($field, $flat);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function scopeActiveNow($query)
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now));
    }
}
