<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class BlogCategory extends Model
{
    use HasAutoSlug, HasTranslations;

    /** Kolom sumber slug otomatis (App\Models\Concerns\HasAutoSlug). */
    protected string $slugFrom = 'name';

    protected $guarded = ['id'];

    public array $translatable = ['name'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
