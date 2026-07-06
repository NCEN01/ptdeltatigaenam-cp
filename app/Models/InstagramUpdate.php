<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class InstagramUpdate extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['batch_label', 'title', 'company', 'date_range'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}