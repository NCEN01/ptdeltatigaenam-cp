<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function profile(Request $request)
    {
        return view('account.profile', ['customer' => $request->user('customer')]);
    }

    public function update(Request $request, string $locale)
    {
        $customer = $request->user('customer');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:200'],
            'preferred_locale' => ['required', 'in:id,en'],
        ]);

        $customer->update($data);

        return back()->with('status', __('site.contact.success'));
    }

    public function orders(Request $request)
    {
        $orders = $request->user('customer')->orders()
            ->with('service')
            ->latest()
            ->paginate(10);

        return view('account.orders', ['orders' => $orders]);
    }
}
