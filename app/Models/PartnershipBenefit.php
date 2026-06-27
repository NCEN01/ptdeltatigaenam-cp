<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PartnershipBenefit extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['title', 'description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
