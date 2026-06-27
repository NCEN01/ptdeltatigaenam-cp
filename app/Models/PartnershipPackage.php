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
