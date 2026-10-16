<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerEmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        if ($request->user('customer')?->hasVerifiedEmail()) {
            return redirect()->route('account.profile');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request, string $locale, int $id, string $hash)
    {
        $customer = Customer::findOrFail($id);

        abort_unless(hash_equals(sha1($customer->getEmailForVerification()), $hash), 403);

        if (! $customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
            event(new Verified($customer));
        }

        Auth::guard('customer')->login($customer);

        /* intended() dipakai supaya orang yang mendaftar di tengah pekerjaan
           lain kembali ke pekerjaannya, bukan selalu mendarat di halaman
           Profil. Tujuan itu dititipkan PartnershipController::store bagi tamu
           yang formulir kemitraannya belum terkirim; selama CUSTOMER_AUTO_VERIFY
           masih false, tautan verifikasi inilah satu-satunya jalannya kembali.
           Tanpa titipan apa pun, halaman Profil tetap tujuan bawaannya. */
        return redirect()->intended(route('account.profile'))
            ->with('status', __('site.contact.success'));
    }

    public function resend(Request $request, string $locale)
    {
        $customer = $request->user('customer');

        if ($customer && ! $customer->hasVerifiedEmail()) {
            $customer->sendEmailVerificationNotification();
        }

        return back()->with('status', __('auth.verification_sent'));
    }
}
