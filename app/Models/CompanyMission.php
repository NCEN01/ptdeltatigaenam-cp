<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CompanyMission extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = ['content'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
