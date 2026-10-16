<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Faq extends Model
{
    use HasRecordLimit, HasTranslations;

    /**
     * Enam tanya-jawab. Angkanya ditentukan tata letak seksinya di beranda:
     * daftarnya berdiri di kolom kanan, bersebelahan dengan kolom kiri yang
     * tingginya tetap (label, judul, kalimat pendukung, dan satu kartu
     * ajakan). Pada enam butir, kedua kolom berakhir hampir rata; butir
     * ketujuh membuat kolom kanan menjulang melewati kolom kiri dan
     * menyisakan bidang kosong yang panjang di sebelahnya.
     *
     * Batas ini juga menjaga seksinya tetap bisa dibaca. Tanya-jawab bukan
     * daftar yang dibaca sampai habis; ia dibaca sampai pertanyaan yang
     * dicari ketemu. Daftar yang panjang menurunkan kemungkinan itu, bukan
     * menaikkannya.
     */
    public const MAX_RECORDS = 6;

    protected $guarded = ['id'];

    public array $translatable = ['question', 'answer'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Meluruskan terjemahan yang tersarang ganda, misalnya
     * {"id":{"id":"x","en":"y"}}.
     *
     * Bentuk itu muncul dari formulir Filament: kolomnya ditulis sebagai
     * question.id dan question.en, sehingga yang terkirim sudah berupa array
     * per bahasa — lalu Spatie memperlakukan array itu sebagai nilai untuk
     * satu bahasa dan menyarangkannya sekali lagi. Pelurusan ini ada di model,
     * bukan di halaman Create dan Edit, supaya satu-satunya jalan penyimpanan
     * yang perlu dipercaya hanya satu. Pola yang sama dipakai App\Models\Banner.
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
                foreach ($nested as $locale => $text) {
                    $flat[$locale] = is_array($text) ? (string) (reset($text) ?: '') : (string) $text;
                }

                $model->setTranslations($field, $flat);
            }
        });
    }

    /** Urutan tampil: yang aktif saja, mengikuti sort_order pilihan admin. */
    public function scopeDisplayable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
