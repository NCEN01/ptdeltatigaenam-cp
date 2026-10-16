<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class InstagramUpdate extends Model
{
    use HasRecordLimit, HasTranslations;

    /**
     * Beranda menampilkan tepat dua kartu "Kegiatan Terbaru di Instagram",
     * berdampingan dalam satu baris dua kolom. Kartu ketiga tidak punya tempat
     * untuk berdiri: ia tidak tampil, tetapi gambarnya tetap terunggah dan
     * memakan ruang simpan, dan admin yang mengisinya tidak pernah diberi tahu
     * bahwa pekerjaannya tidak terlihat.
     *
     * Dua ini bukan sekadar batas atas, melainkan jumlah yang memang dipakai —
     * keduanya tinggal disunting atau gambarnya diganti kapan saja tanpa perlu
     * menghapus lalu membuat ulang.
     */
    public const MAX_RECORDS = 2;

    protected $guarded = ['id'];

    public array $translatable = ['batch_label', 'title', 'company', 'date_range'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}