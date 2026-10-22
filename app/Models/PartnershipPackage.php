<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;
use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PartnershipPackage extends Model
{
    use HasAutoSlug, HasRecordLimit, HasTranslations;

    /** Kolom sumber slug otomatis (App\Models\Concerns\HasAutoSlug). */
    protected string $slugFrom = 'name';

    /**
     * Empat tingkat kemitraan. Halaman Kemitraan menyusunnya dalam satu kisi
     * empat kolom di layar besar, dan formulir pendaftarannya menampilkan
     * keempatnya sebagai pilihan paket — kolom kelima akan memecah kisinya
     * menjadi dua baris yang tidak sama panjang.
     *
     * Angka "4 Tingkat kemitraan" di halaman itu dihitung dari jumlah baris
     * tabel ini, jadi batas ini sekaligus menjaga angka tersebut tetap benar.
     */
    public const MAX_RECORDS = 4;

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
