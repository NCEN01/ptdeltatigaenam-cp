<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PreventSpamHoneypot
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the honeypot field is filled, we treat it as spam.
        if ($request->filled('website_url')) {
            Log::warning('Honeypot spam check triggered.', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'input' => $request->except(['password', 'password_confirmation']),
            ]);

            abort(422, 'Spam detected.');
        }

        return $next($request);
    }
}
