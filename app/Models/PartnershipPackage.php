<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PartnershipPackage extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    /** features stores {"id":[...],"en":[...]} — a translatable list. */
    public array $translatable = ['name', 'tagline', 'description', 'features', 'price_note'];

    /**
     * Guard against double-nested translations like {"id":{"id":"x","en":"y"}}.
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
                foreach ($nested as $loc => $val) {
                    $flat[$loc] = is_array($val) ? (string) (reset($val) ?: '') : $val;
                }
                $model->setTranslations($field, $flat);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_highlighted' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(PartnershipRegistration::class);
    }
}
