<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu butir "yang kamu dapatkan" di halaman program.
 *
 * service_id kosong berarti benefit bawaan yang berlaku untuk setiap program;
 * diisi berarti benefit khusus program itu. Resolusinya ada di
 * forService(): begitu sebuah program punya daftarnya sendiri, daftar
 * bawaannya tidak lagi dipakai untuk program tersebut — bukan digabung.
 *
 * Digabung sempat dipertimbangkan dan dilepas: program yang ingin
 * menghilangkan satu benefit bawaan jadi tidak punya cara melakukannya,
 * dan satu-satunya jalan keluarnya adalah menambah kolom "sembunyikan"
 * untuk tiap baris bawaan.
 */
class ProgramBenefit extends Model
{
    protected $fillable = [
        'service_id',
        'title',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Benefit yang berlaku bagi satu program: miliknya sendiri bila ada,
     * kalau tidak daftar bawaannya.
     */
    public static function forService(Service $service)
    {
        $own = static::active()
            ->where('service_id', $service->id)
            ->orderBy('sort_order')
            ->get();

        if ($own->isNotEmpty()) {
            return $own;
        }

        return static::active()
            ->whereNull('service_id')
            ->orderBy('sort_order')
            ->get();
    }
}
