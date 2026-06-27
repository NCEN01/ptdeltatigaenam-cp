<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class PortfolioImage extends Model
{
    use HasTranslations;

    public $timestamps = false;

    protected $guarded = ['id'];

    public array $translatable = ['caption'];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
