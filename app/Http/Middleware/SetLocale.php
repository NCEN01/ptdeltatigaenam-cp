<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['id', 'en'];

    public const DEFAULT = 'id';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Remember a logged-in customer's preferred locale.
        if ($customer = $request->user('customer')) {
            if ($customer->preferred_locale !== $locale) {
                $customer->forceFill(['preferred_locale' => $locale])->saveQuietly();
            }
        }

        return $next($request);
    }
}
