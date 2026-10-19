<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetLocale;
use App\Models\Customer;
use App\Rules\SafeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class GoogleAuthController extends Controller
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const USERINFO_URL = 'https://www.googleapis.com/oauth2/v3/userinfo';

    private const SESSION_STATE = 'google_oauth_state';
    private const SESSION_VERIFIER = 'google_oauth_code_verifier';
    private const SESSION_LOCALE = 'google_oauth_locale';

    /**
     * Arahkan pengunjung ke halaman persetujuan Google OAuth 2.0
     * lengkap dengan proteksi CSRF (state) dan PKCE (S256).
     */
    public function redirect(Request $request, string $locale): RedirectResponse
    {
        abort_unless(config('services.google.enabled'), 404);

        $state = Str::random(40);
        $codeVerifier = Str::random(64);
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $request->session()->put([
            self::SESSION_STATE => $state,
            self::SESSION_VERIFIER => $codeVerifier,
            self::SESSION_LOCALE => in_array($locale, SetLocale::SUPPORTED, true) ? $locale : SetLocale::DEFAULT,
        ]);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $this->redirectUri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            'prompt' => 'select_account',
        ], '', '&', PHP_QUERY_RFC3986);

        return redirect()->away(self::AUTH_URL.'?'.$query);
    }

    /**
     * Tangani kembalian dari Google, verifikasi state + PKCE + email_verified,
     * lalu tautkan atau buat akun Customer.
     */
    public function callback(Request $request): RedirectResponse
    {
        abort_unless(config('services.google.enabled'), 404);

        $locale = $request->session()->pull(self::SESSION_LOCALE, SetLocale::DEFAULT);
        if (! in_array($locale, SetLocale::SUPPORTED, true)) {
            $locale = SetLocale::DEFAULT;
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        $expectedState = (string) $request->session()->pull(self::SESSION_STATE, '');
        $codeVerifier = (string) $request->session()->pull(self::SESSION_VERIFIER, '');
        $incomingState = (string) $request->query('state', '');

        if ($request->filled('error')) {
            return $this->failRedirect($locale, $locale === 'id'
                ? 'Masuk dengan Google dibatalkan.'
                : 'Google sign-in was cancelled.');
        }

        if ($expectedState === '' || $codeVerifier === '' || $incomingState === '' || ! hash_equals($expectedState, $incomingState)) {
            return $this->failRedirect($locale, $locale === 'id'
                ? 'Sesi autentikasi kedaluwarsa atau tidak sah. Silakan coba lagi.'
                : 'Authentication session expired or invalid. Please try again.');
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            return $this->failRedirect($locale);
        }

        $timeout = max(3, (int) config('services.google.timeout', 8));

        try {
            $tokenResponse = Http::asForm()
                ->timeout($timeout)
                ->post(self::TOKEN_URL, [
                    'code' => $code,
                    'client_id' => config('services.google.client_id'),
                    'client_secret' => config('services.google.client_secret'),
                    'redirect_uri' => $this->redirectUri(),
                    'grant_type' => 'authorization_code',
                    'code_verifier' => $codeVerifier,
                ]);

            if (! $tokenResponse->successful()) {
                return $this->failRedirect($locale);
            }

            $accessToken = (string) $tokenResponse->json('access_token', '');
            if ($accessToken === '') {
                return $this->failRedirect($locale);
            }

            $userResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout($timeout)
                ->get(self::USERINFO_URL);

            if (! $userResponse->successful()) {
                return $this->failRedirect($locale);
            }

            $profile = $userResponse->json();
        } catch (Throwable) {
            return $this->failRedirect($locale);
        }

        if (! is_array($profile) || ($profile['email_verified'] ?? false) !== true) {
            return $this->failRedirect($locale, $locale === 'id'
                ? 'Alamat surel Google Anda belum terverifikasi.'
                : 'Your Google email address is not verified.');
        }

        $email = mb_strtolower(trim((string) ($profile['email'] ?? '')));
        $emailCheck = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'string', 'email:rfc', 'max:255', new SafeEmail]]
        );

        if ($emailCheck->fails()) {
            return $this->failRedirect($locale);
        }

        $avatar = $this->sanitizeAvatar($profile['picture'] ?? null);
        $customer = Customer::where('email', $email)->first();

        if ($customer) {
            if (! $customer->is_active) {
                return $this->failRedirect($locale, $locale === 'id'
                    ? 'Akun Anda sedang dinonaktifkan. Silakan hubungi tim kami.'
                    : 'Your account is currently deactivated. Please contact our team.');
            }

            $updates = ['last_login_at' => now()];
            if ($customer->email_verified_at === null) {
                $updates['email_verified_at'] = now();
            }
            if (blank($customer->avatar) && $avatar !== null) {
                $updates['avatar'] = $avatar;
            }

            $customer->forceFill($updates)->saveQuietly();
        } else {
            $rawName = trim((string) ($profile['name'] ?? ''));
            $fallbackName = Str::before($email, '@') ?: 'Pelanggan';
            $name = Str::limit($rawName !== '' ? $rawName : $fallbackName, 255, '');

            $customer = Customer::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(64)),
                'avatar' => $avatar,
                'preferred_locale' => $locale,
                'is_active' => true,
            ]);

            $customer->forceFill([
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ])->saveQuietly();

            event(new Registered($customer));
        }

        Auth::guard('customer')->login($customer, false);
        $request->session()->regenerate();

        return redirect()->intended(route('account.profile', ['locale' => $locale]));
    }

    private function redirectUri(): string
    {
        $configured = trim((string) config('services.google.redirect', ''));

        return $configured !== '' ? $configured : route('auth.google.callback');
    }

    private function sanitizeAvatar(mixed $picture): ?string
    {
        if (! is_string($picture)) {
            return null;
        }

        $url = trim($picture);
        if ($url === '' || strlen($url) > 255 || ! str_starts_with($url, 'https://')) {
            return null;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private function failRedirect(string $locale, ?string $message = null): RedirectResponse
    {
        return redirect()
            ->route('login', ['locale' => $locale])
            ->withErrors([
                'oauth' => $message ?? ($locale === 'id'
                    ? 'Tidak dapat masuk dengan Google saat ini. Silakan coba lagi atau masuk dengan surel.'
                    : 'Unable to sign in with Google right now. Please try again or use your email.'),
            ]);
    }
}
