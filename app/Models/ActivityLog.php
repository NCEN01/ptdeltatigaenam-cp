<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris catatan aktivitas panel admin.
 *
 * Hanya ditulis oleh App\Support\ActivityRecorder dan hanya dibaca oleh
 * halaman daftarnya. Tidak pernah disunting: catatan audit yang bisa diubah
 * bukan catatan audit.
 */
class ActivityLog extends Model
{
    /** Tabel ini hanya punya created_at; tidak ada yang pernah memperbaruinya. */
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Nama jenis data yang terbaca manusia, mis. "Banner" dari App\Models\Banner. */
    public function subjectName(): string
    {
        return class_basename($this->subject_type);
    }
}
