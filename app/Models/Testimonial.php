<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasRecordLimit, HasTranslations;

    /**
     * Lima puluh testimoni tersimpan. Alasannya ruang simpan dan waktu muat,
     * bukan tata letak: <x-testimonial-columns> menghitung sendiri lama
     * putarannya dari jumlah kartu, jadi deretannya tetap berjalan dengan
     * kecepatan yang sama berapa pun isinya — yang tidak ikut menyesuaikan
     * adalah berapa banyak foto yang harus diunduh peramban.
     */
    public const MAX_RECORDS = 50;

    /**
     * Sebanyak-banyaknya yang dirender dalam satu halaman.
     *
     * Terpisah dari MAX_RECORDS karena keduanya menjawab hal berbeda. Komponen
     * testimoninya memuat seluruh daftar di setiap kolomnya — tiga kolom — lalu
     * menggandakan isinya agar putaran -50% tidak menyisakan celah, sehingga
     * jumlah kartu yang benar-benar masuk DOM adalah enam kali jumlah
     * testimoninya. Delapan belas sudah lebih dari cukup untuk deretan yang
     * memperlihatkan kurang dari sepuluh kartu sekaligus, dan menahan DOM-nya
     * di sekitar seratus simpul alih-alih tiga ratus.
     */
    public const MAX_RENDERED = 18;

    protected $guarded = ['id'];

    public array $translatable = ['author_position', 'content'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
