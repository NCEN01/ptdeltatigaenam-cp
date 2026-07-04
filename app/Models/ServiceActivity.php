<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ServiceActivity extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['title', 'description'];

    /**
     * Guard against double-nested translations like {"id":{"id":"x","en":"y"}},
     * which the Filament Translatable concern can produce when a relationship
     * repeater already supplies a full per-locale array. Keep values flat.
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

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
