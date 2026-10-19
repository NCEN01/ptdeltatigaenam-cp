<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Memeriksa jawaban reCAPTCHA v2 ke peladen Google.
 *
 * Dipakai lewat helper-nya sendiri supaya pengontrol tidak perlu tahu apakah
 * fiturnya menyala:
 *
 *     'g-recaptcha-response' => Recaptcha::rules(),
 *
 * Ketika kuncinya belum diisi, rules() mengembalikan array kosong sehingga
 * kolomnya tidak divalidasi sama sekali — bukan diloloskan dengan aturan yang
 * tetap berjalan. Bedanya penting: tanpa aturan, tidak ada permintaan jaringan
 * dan tidak ada kolom wajib yang muncul di formulir yang tidak menampilkan
 * kotaknya.
 */
class Recaptcha implements ValidationRule
{
    /**
     * Tetap dijalankan walau kolom g-recaptcha-response kosong atau tidak
     * dikirim sama sekali, sehingga pesan "Centang dulu..." dalam bahasa
     * Indonesia maupun Inggris diatur dari satu pintu di bawah ini.
     */
    public bool $implicit = true;

    /**
     * Panjang maksimum token reCAPTCHA v2 yang wajar (biasanya ~500–1000 byte).
     * Menolak kiriman raksasa sebelum sempat diteruskan ke peladen Google.
     */
    private const MAX_TOKEN_BYTES = 4096;

    /**
     * Aturan untuk kolom g-recaptcha-response, atau array kosong bila
     * fiturnya belum dinyalakan.
     *
     * @return array<int, mixed>
     */
    public static function rules(): array
    {
        if (! config('captcha.enabled')) {
            return [];
        }

        return [new self];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! config('captcha.enabled')) {
            return;
        }

        $isId = app()->getLocale() === 'id';

        if (! is_string($value)) {
            $fail($isId
                ? 'Centang dulu kotak "Saya bukan robot" sebelum mengirim.'
                : 'Please tick the "I\'m not a robot" box before submitting.');

            return;
        }

        $jawaban = trim($value);

        if ($jawaban === '') {
            $fail($isId
                ? 'Centang dulu kotak "Saya bukan robot" sebelum mengirim.'
                : 'Please tick the "I\'m not a robot" box before submitting.');

            return;
        }

        if (strlen($jawaban) > self::MAX_TOKEN_BYTES) {
            $fail($isId
                ? 'Verifikasi "Saya bukan robot" gagal. Coba centang ulang kotaknya.'
                : 'The "I\'m not a robot" verification failed. Please tick the box again.');

            return;
        }

        try {
            $res = Http::asForm()
                ->timeout(config('captcha.timeout', 5))
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('captcha.secret_key'),
                    'response' => $jawaban,
                    'remoteip' => request()->ip(),
                ]);
        } catch (Throwable $e) {
            /* Google tidak bisa dihubungi. Apa yang terjadi berikutnya diatur
               config, dan bawaannya meloloskan: formulir di situs ini adalah
               jalan masuk calon pelanggan, dan gangguan di pihak Google tidak
               boleh menutup jalan itu — honeypot dan throttle masih berjaga.
               Dicatat ke log supaya gangguan yang berkepanjangan tetap
               terlihat, bukan hilang tanpa jejak. */
            Log::warning('reCAPTCHA tidak bisa dihubungi', ['pesan' => $e->getMessage()]);

            if (! config('captcha.allow_on_failure', true)) {
                $fail($isId
                    ? 'Verifikasi "Saya bukan robot" sedang tidak bisa dijalankan. Coba lagi sebentar lagi.'
                    : 'The "I\'m not a robot" verification is temporarily unavailable. Please try again shortly.');
            }

            return;
        }

        if (! $res->successful()) {
            Log::warning('reCAPTCHA menjawab dengan status tidak sukses', ['status' => $res->status()]);

            if (! config('captcha.allow_on_failure', true)) {
                $fail($isId
                    ? 'Verifikasi "Saya bukan robot" sedang tidak bisa dijalankan. Coba lagi sebentar lagi.'
                    : 'The "I\'m not a robot" verification is temporarily unavailable. Please try again shortly.');
            }

            return;
        }

        if ($res->json('success') === true) {
            return;
        }

        /* Jawaban yang sudah pernah dipakai atau sudah kedaluwarsa diberi
           pesan tersendiri: pengisi formulir panjang kerap membiarkan
           halamannya terbuka lebih dari dua menit, dan "centang ulang" jauh
           lebih berguna baginya daripada "verifikasi gagal". */
        $galat = (array) $res->json('error-codes', []);

        if (array_intersect($galat, ['timeout-or-duplicate'])) {
            $fail($isId
                ? 'Verifikasi "Saya bukan robot" sudah kedaluwarsa. Centang ulang kotaknya, lalu kirim lagi.'
                : 'The "I\'m not a robot" verification has expired. Please tick the box again and resubmit.');

            return;
        }

        $fail($isId
            ? 'Verifikasi "Saya bukan robot" gagal. Coba centang ulang kotaknya.'
            : 'The "I\'m not a robot" verification failed. Please tick the box again.');
    }
}

