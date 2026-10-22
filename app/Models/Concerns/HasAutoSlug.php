<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Slug dibuat sendiri dari judul atau nama, sehingga admin tidak perlu
 * mengisinya di panel.
 *
 * Kolom slug tetap ada dan tetap dipakai — ia bagian dari alamat halaman,
 * misalnya /id/layanan/sertifikasi-bnsp. Yang dilepas hanya kotak isiannya.
 * Selama ini admin diminta mengetik sesuatu yang tidak ia pahami gunanya, dan
 * salah ketik di sana langsung mengubah alamat halaman yang mungkin sudah
 * tersebar di WhatsApp dan terindeks Google.
 *
 * Dua aturan yang disengaja:
 *
 *  - Hanya dibuat SEKALI, saat slug masih kosong. Mengganti judul artikel
 *    tidak mengganti alamatnya. Alamat yang berubah setiap kali judul
 *    disunting memutus setiap tautan yang pernah dibagikan, dan memulai ulang
 *    peringkat halaman itu di mesin pencari.
 *
 *  - Selalu unik di tabelnya. Dua artikel berjudul sama mendapat
 *    "judul-artikel" dan "judul-artikel-2", bukan galat indeks unik yang
 *    menggagalkan penyimpanan dan menghapus isian admin.
 *
 * Sumbernya diambil dari versi Indonesia lebih dulu, karena itu bahasa utama
 * situs ini dan bahasa yang selalu diisi; versi Inggris boleh kosong.
 */
trait HasAutoSlug
{
    protected static function bootHasAutoSlug(): void
    {
        static::saving(function (self $model): void {
            if (filled($model->getAttribute('slug'))) {
                return;
            }

            $model->setAttribute('slug', $model->generateUniqueSlug());
        });
    }

    /** Kolom sumber. Model boleh menimpanya lewat properti $slugFrom. */
    protected function slugSourceField(): string
    {
        if (property_exists($this, 'slugFrom')) {
            return $this->slugFrom;
        }

        return array_key_exists('title', $this->getAttributes()) ? 'title' : 'name';
    }

    protected function slugSourceText(): string
    {
        $field = $this->slugSourceField();

        $value = method_exists($this, 'isTranslatableAttribute') && $this->isTranslatableAttribute($field)
            ? ($this->getTranslation($field, 'id', false) ?: $this->getTranslation($field, 'en', false))
            : $this->getAttribute($field);

        /* Formulir Filament kadang mengirim terjemahan yang tersarang ganda
           ({"id":{"id":"…"}}) sebelum model meluruskannya di hook saving-nya
           sendiri — yang berjalan SETELAH hook ini. Ambil teks pertama yang
           ditemukan supaya slugnya tidak menjadi "array". */
        while (is_array($value)) {
            $value = reset($value);
        }

        return trim((string) $value);
    }

    protected function generateUniqueSlug(): string
    {
        $max = $this->slugMaxLength();

        // Enam karakter disisakan untuk akhiran "-9999" bila judulnya kembar.
        $base = Str::limit(Str::slug($this->slugSourceText()), $max - 6, '');
        $base = rtrim($base, '-');

        if ($base === '') {
            $base = Str::slug(class_basename($this)).'-'.now()->format('YmdHis');
        }

        $slug = $base;
        $n = 2;

        while ($this->slugTaken($slug)) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }

    protected function slugTaken(string $slug): bool
    {
        return static::query()
            ->where('slug', $slug)
            ->when($this->exists, fn ($q) => $q->whereKeyNot($this->getKey()))
            ->exists();
    }

    /** Panjang kolom slug di basis data, dibaca sekali per kelas. */
    protected function slugMaxLength(): int
    {
        static $cache = [];

        $table = $this->getTable();

        if (! isset($cache[$table])) {
            $kolom = collect(Schema::getColumns($table))->firstWhere('name', 'slug');
            preg_match('/\((\d+)\)/', (string) ($kolom['type'] ?? ''), $m);
            $cache[$table] = (int) ($m[1] ?? 160);
        }

        return $cache[$table];
    }
}
