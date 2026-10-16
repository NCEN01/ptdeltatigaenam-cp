<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Throwable;

/**
 * Mencatat setiap perubahan data yang dilakukan admin lewat panel.
 *
 * Dipasang dengan satu pendengar berpola di AppServiceProvider, bukan dengan
 * trait di tiap model. Ada tiga puluh model yang bisa disunting admin; menaruh
 * trait di masing-masingnya berarti tiga puluh tempat yang bisa terlewat, dan
 * model yang ditambahkan nanti akan diam-diam tidak tercatat tanpa ada yang
 * menyadarinya.
 */
class ActivityRecorder
{
    /**
     * Kolom yang tidak pernah ikut dicatat.
     *
     * Kata sandi dan token jelas. remember_token dan timestamp dilewati karena
     * keduanya berubah di hampir setiap penyimpanan tanpa memberi tahu apa pun
     * tentang apa yang sebenarnya diubah admin — dicatat, ia menenggelamkan
     * perubahan yang benar-benar penting di antara derau.
     */
    private const ABAIKAN_KOLOM = [
        'password', 'remember_token', 'token', 'snap_token', 'signature_key',
        'created_at', 'updated_at',
    ];

    /**
     * Model yang tidak dicatat.
     *
     * ActivityLog sendiri wajib ada di sini: mencatat penulisan catatan akan
     * memicu penulisan catatan berikutnya tanpa henti. Sisanya tabel mesin —
     * sesi, antrean, cache — yang berubah terus-menerus tanpa campur tangan
     * siapa pun.
     */
    private const ABAIKAN_MODEL = [
        ActivityLog::class,
        \Illuminate\Notifications\DatabaseNotification::class,
    ];

    /** Kolom yang dicoba berurutan untuk menamai barisnya di daftar. */
    private const KOLOM_NAMA = [
        'title', 'name', 'question', 'participant_name', 'company_name',
        'order_number', 'invoice_number', 'email', 'slug', 'key',
    ];

    public static function listen(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            Event::listen("eloquent.{$event}: *", function (string $nama, array $data) use ($event): void {
                self::record($event, $data[0] ?? null);
            });
        }
    }

    private static function record(string $event, mixed $model): void
    {
        if (! $model instanceof Model) {
            return;
        }

        foreach (self::ABAIKAN_MODEL as $kelas) {
            if ($model instanceof $kelas) {
                return;
            }
        }

        /* Hanya perubahan yang dilakukan admin panel yang dicatat.
           Guard 'web' adalah guard panel; pelanggan memakai guard 'customer'.
           Tanpa pengguna yang masuk, perubahannya datang dari penyemai,
           perintah artisan, atau webhook Midtrans — ketiganya bukan "aktivitas
           admin", dan mencatatnya hanya memenuhi daftar dengan baris yang
           tidak bisa ditanyakan kepada siapa pun. */
        $user = Auth::guard('web')->user();

        if (! $user) {
            return;
        }

        $perubahan = $event === 'updated'
            ? self::dirtyRingkas($model)
            : null;

        // Penyimpanan yang tidak mengubah kolom apa pun tidak perlu dicatat.
        if ($event === 'updated' && empty($perubahan)) {
            return;
        }

        /* Dibungkus try/catch dengan sengaja. Pencatatan audit tidak boleh
           menjatuhkan pekerjaan yang sedang dicatatnya: kalau tabel log penuh,
           terkunci, atau kolomnya berubah, admin tetap harus bisa menyimpan
           banner-nya. Kegagalannya diam — dan itu pilihan yang disadari,
           karena melemparkan galat di sini berarti satu bug di pencatat
           melumpuhkan seluruh panel. */
        try {
            ActivityLog::create([
                'user_id' => $user->getKey(),
                'user_name' => (string) ($user->name ?? $user->email ?? 'Admin'),
                'event' => $event,
                'subject_type' => $model::class,
                'subject_id' => $model->getKey(),
                'subject_label' => self::label($model),
                'changes' => $perubahan,
                'ip' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // sengaja dibiarkan
        }
    }

    /**
     * Kolom yang berubah beserta nilai lama dan barunya, sudah dipangkas.
     *
     * @return array<string, array{dari: string, jadi: string}>
     */
    private static function dirtyRingkas(Model $model): array
    {
        $out = [];

        foreach ($model->getDirty() as $kolom => $baru) {
            if (in_array($kolom, self::ABAIKAN_KOLOM, true)) {
                continue;
            }

            $out[$kolom] = [
                'dari' => self::ringkas($model->getOriginal($kolom)),
                'jadi' => self::ringkas($baru),
            ];
        }

        return $out;
    }

    /** Nilai apa pun jadi satu baris pendek yang bisa dibaca di tabel. */
    private static function ringkas(mixed $nilai): string
    {
        if (is_bool($nilai)) {
            return $nilai ? 'ya' : 'tidak';
        }

        if ($nilai === null || $nilai === '') {
            return '—';
        }

        if (is_array($nilai)) {
            $nilai = json_encode($nilai, JSON_UNESCAPED_UNICODE);
        }

        return Str::limit(trim(preg_replace('/\s+/', ' ', (string) $nilai)), 120);
    }

    /** Nama baris yang terbaca manusia, dicari dari kolom yang paling mungkin. */
    private static function label(Model $model): ?string
    {
        foreach (self::KOLOM_NAMA as $kolom) {
            if (! array_key_exists($kolom, $model->getAttributes())) {
                continue;
            }

            // Kolom terjemahan mengembalikan array; ambil bahasa mana pun yang ada.
            $nilai = $model->getAttribute($kolom);

            if (is_array($nilai)) {
                $nilai = reset($nilai) ?: null;
            }

            if (filled($nilai)) {
                return Str::limit(trim((string) $nilai), 180);
            }
        }

        return null;
    }
}
