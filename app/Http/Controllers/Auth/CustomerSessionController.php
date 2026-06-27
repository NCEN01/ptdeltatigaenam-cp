<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerSessionController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function store(Request $request, string $locale)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard('customer')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $customer = Auth::guard('customer')->user();
        $customer->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->intended(route('account.profile'));
    }

    public function destroy(Request $request, string $locale)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
