<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasRecordLimit, HasTranslations;

    /**
     * Logo mitra. Dua puluh lima adalah batas yang masih sehat untuk satu
     * halaman: setiap logo adalah satu permintaan gambar, dan deretan yang
     * lebih panjang dari ini mulai membebani waktu muat halaman Kemitraan
     * tanpa ada yang benar-benar membacanya sampai habis.
     *
     * Empat atau kurang, deretannya diam. Lebih dari empat, ia bergeser
     * sendiri ke kiri seperti deretan klien — lihat <x-partners-clients>.
     */
    public const MAX_RECORDS = 25;

    protected $guarded = ['id'];

    public array $translatable = ['description'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
