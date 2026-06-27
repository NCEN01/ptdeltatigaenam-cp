<?php

namespace App\Support;

use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Request;

class Locale
{
    /** @return array<int,string> */
    public static function supported(): array
    {
        return SetLocale::SUPPORTED;
    }

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function label(string $locale): string
    {
        return match ($locale) {
            'id' => 'Indonesia',
            'en' => 'English',
            default => strtoupper($locale),
        };
    }

    /**
     * Current URL rewritten to a different locale (swaps the first path segment).
     */
    public static function alternate(string $locale): string
    {
        $path = trim(Request::path(), '/');           // e.g. "id/layanan/x"
        $segments = $path === '' ? [] : explode('/', $path);

        if (! empty($segments) && in_array($segments[0], self::supported(), true)) {
            $segments[0] = $locale;
        } else {
            array_unshift($segments, $locale);
        }

        $url = url(implode('/', $segments));
        $query = Request::getQueryString();

        return $query ? "{$url}?{$query}" : $url;
    }
}
