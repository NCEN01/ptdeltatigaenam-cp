<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Office extends Model
{
    use HasRecordLimit, HasTranslations;

    /**
     * Tiga kantor.
     *
     * Angkanya ditentukan tempat alamat ini tampil. Di footer, kantor berdiri
     * di kolom Kontak yang berbagi baris dengan tiga kolom lain; pada tiga
     * alamat kolom itu sudah menjadi yang paling panjang di footer. Di halaman
     * Kontak, ketiganya berjajar sebagai kartu yang dipilih untuk menggerakkan
     * satu peta sematan — kartu keempat memecah barisnya menjadi dua yang
     * tidak sama panjang.
     *
     * Mengisi dua saja berarti dua yang tampil, bukan tiga dengan satu kolom
     * kosong: pemakainya membaca jumlah baris yang ada, bukan angka tetap.
     */
    public const MAX_RECORDS = 3;

    protected $guarded = ['id'];

    public array $translatable = ['name'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Meluruskan terjemahan yang tersarang ganda dari formulir Filament.
     * Pola yang sama dipakai App\Models\Banner dan App\Models\Faq; alasannya
     * ada di kedua berkas itu.
     */
    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            $value = $model->getTranslations('name');

            if (! is_array($value)) {
                return;
            }

            $nested = collect($value)->first(fn ($v) => is_array($v));

            if ($nested === null) {
                return;
            }

            $flat = [];
            foreach ($nested as $locale => $text) {
                $flat[$locale] = is_array($text) ? (string) (reset($text) ?: '') : (string) $text;
            }

            $model->setTranslations('name', $flat);
        });
    }

    public function scopeDisplayable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
