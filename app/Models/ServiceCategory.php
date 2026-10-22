<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;
use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ServiceCategory extends Model
{
    use HasAutoSlug, HasFactory, HasRecordLimit, HasTranslations;

    /** Kolom sumber slug otomatis (App\Models\Concerns\HasAutoSlug). */
    protected string $slugFrom = 'name';

    /**
     * Kategori layanan. Dua belas ditentukan oleh menu atas, bukan oleh basis
     * data: tarikan "Layanan" di navbar menampilkan seluruh kategori aktif
     * tanpa penggal, dan daftar yang lebih panjang dari ini melewati tinggi
     * layar laptop sehingga butir terakhirnya tidak bisa diklik sama sekali.
     *
     * Footer sengaja hanya menampilkan enam teratas menurut sort_order; itu
     * ringkasan, bukan daftar lengkap, dan tidak terpengaruh batas ini.
     */
    public const MAX_RECORDS = 12;

    protected $guarded = ['id'];

    public array $translatable = [
        'name', 'short_description', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}
