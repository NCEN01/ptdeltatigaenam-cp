<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Pengajar yang mengampu satu program atau lebih.
 *
 * Disimpan sebagai pustaka bersama, bukan sebagai isian di dalam tiap
 * program: satu mentor kerap mengajar di beberapa program sekaligus, dan
 * dengan begini fotonya cukup diunggah sekali sementara perubahan bio
 * berlaku di seluruh program yang diampunya.
 */
class Mentor extends Model
{
    protected $fillable = [
        'name',
        'photo',
        'role',
        'bio',
        'linkedin_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Huruf depan nama, dipakai bila mentornya belum punya foto. */
    public function getInitialAttribute(): string
    {
        return mb_strtoupper(mb_substr(trim((string) $this->name), 0, 1));
    }
}
