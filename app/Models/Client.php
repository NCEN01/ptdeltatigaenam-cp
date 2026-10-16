<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasRecordLimit;

    /**
     * Logo klien. Lima puluh dipilih dengan alasan ruang simpan dan waktu muat,
     * bukan tata letak: deretannya memang bergeser sendiri sehingga panjangnya
     * tidak merusak apa pun, tetapi tiap logo tetap satu berkas gambar yang
     * harus diunduh peramban pengunjung.
     */
    public const MAX_RECORDS = 50;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }
}
