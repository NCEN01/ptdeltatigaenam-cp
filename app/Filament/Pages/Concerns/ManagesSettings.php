<?php

namespace App\Filament\Pages\Concerns;

use App\Models\Setting;
use Filament\Notifications\Notification;

/**
 * Muat dan simpan sekumpulan baris tabel settings untuk satu halaman panel.
 *
 * Dulu seluruh pengaturan berkumpul di satu halaman "Pengaturan Situs" — surel
 * kantor bersebelahan dengan naskah Visi, intro Kemitraan, dan ajakan
 * Konsultasi Program. Admin yang ingin mengganti nomor WhatsApp harus melewati
 * paragraf tentang perusahaan, dan admin yang mencari teks halaman Kemitraan
 * tidak akan menduga tempatnya di menu "Pengaturan".
 *
 * Sekarang tiap kumpulan berdiri di dekat halaman yang memakainya, dan trait
 * ini yang menjaga keempatnya membaca dan menulis dengan cara yang sama.
 *
 * Halaman yang memakainya cukup menjawab settingTypes():
 *
 *     'site_email' => ['text', 'general'],
 *     'site_hours' => ['json', 'general'],   // dwibahasa {"id": …, "en": …}
 *     'company_profile_file' => ['file', 'general'],
 *
 * Kuncinya sama persis dengan yang sudah tersimpan, jadi tidak ada data yang
 * perlu dipindahkan — hanya letak isiannya di panel yang berubah.
 */
trait ManagesSettings
{
    public ?array $data = [];

    /** @return array<string, array{0: 'text'|'json'|'file', 1: string}> */
    abstract protected function settingTypes(): array;

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('manage_settings');
    }

    public function mount(): void
    {
        $isi = [];

        foreach ($this->settingTypes() as $key => [$type]) {
            $nilai = Setting::get($key);

            $isi[$key] = match ($type) {
                // Kolom dwibahasa selalu diisi kedua kuncinya, supaya isian EN
                // yang belum pernah diisi tetap muncul sebagai kotak kosong,
                // bukan hilang.
                'json' => is_array($nilai) ? array_merge(['id' => '', 'en' => ''], $nilai) : ['id' => '', 'en' => ''],
                default => $nilai,
            };
        }

        $this->form->fill($isi);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($this->settingTypes() as $key => [$type, $group]) {
            $nilai = $data[$key] ?? null;

            $nilai = match ($type) {
                /* Terjemahan yang seluruh bahasanya kosong disimpan sebagai
                   kosong, bukan {"id":null,"en":null}. Pembacanya memang sudah
                   jatuh ke teks bawaan untuk keduanya, tetapi bentuk kedua
                   membuat kunci yang belum pernah diisi terlihat seperti sudah
                   diisi saat basis datanya diperiksa. */
                'json' => self::semuaKosong($nilai) ? null : json_encode(
                    array_map(fn ($v) => is_string($v) ? trim($v) : $v, (array) $nilai),
                    JSON_UNESCAPED_UNICODE,
                ),
                // FileUpload mengembalikan array saat berkasnya baru diunggah,
                // dan string saat tidak disentuh.
                'file' => is_array($nilai) ? (reset($nilai) ?: null) : $nilai,
                default => is_string($nilai) ? (trim($nilai) === '' ? null : trim($nilai)) : $nilai,
            };

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $nilai, 'type' => $type === 'json' ? 'json' : 'text', 'group' => $group],
            );
        }

        Notification::make()->title('Tersimpan')->success()->send();
    }

    private static function semuaKosong(mixed $nilai): bool
    {
        if (! is_array($nilai)) {
            return true;
        }

        foreach ($nilai as $v) {
            if (is_string($v) ? trim($v) !== '' : filled($v)) {
                return false;
            }
        }

        return true;
    }
}
