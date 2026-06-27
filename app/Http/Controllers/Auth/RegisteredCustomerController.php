<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisteredCustomerController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:200'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'password' => Hash::make($data['password']),
            'preferred_locale' => app()->getLocale(),
            'is_active' => true,
        ]);

        event(new Registered($customer));
        $customer->sendEmailVerificationNotification();

        Auth::guard('customer')->login($customer);

        return redirect()->route('verification.notice');
    }
}
