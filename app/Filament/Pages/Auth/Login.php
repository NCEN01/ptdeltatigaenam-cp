<?php

namespace App\Filament\Pages\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $throttleKey = $this->getThrottleKey();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::channel('single')->warning('Admin login attempt blocked due to rate limiting.', [
                'email' => $this->data['email'] ?? null,
                'ip' => request()->ip(),
                'available_in_seconds' => $seconds,
            ]);

            Notification::make()
                ->title('Terlalu banyak percobaan login')
                ->body("Silakan coba lagi dalam {$seconds} detik.")
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'data.email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        try {
            $response = parent::authenticate();

            RateLimiter::clear($throttleKey);

            return $response;
        } catch (\Throwable $e) {
            RateLimiter::hit($throttleKey, 60);

            Log::channel('single')->info('Admin login failed attempt.', [
                'email' => $this->data['email'] ?? null,
                'ip' => request()->ip(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function getThrottleKey(): string
    {
        return 'admin-login|'.strtolower($this->data['email'] ?? '').'|'.request()->ip();
    }
}
