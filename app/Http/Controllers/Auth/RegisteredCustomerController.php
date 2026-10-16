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
        // Dirapikan sebelum divalidasi, bukan sesudah. Spasi di ujung nama dan
        // surel datang terus-menerus dari penempelan papan klip, dan kalau
        // dibiarkan ia ikut tersimpan: " Budi" lalu berjajar di panel admin
        // sebagai nama yang berbeda dari "Budi".
        //
        // Surel diturunkan ke huruf kecil supaya yang tersimpan satu bentuk.
        // Duplikatnya sendiri sudah tertangkap tanpa ini — kolomnya bercollation
        // utf8mb4_unicode_ci yang tidak membedakan kapital, dan ada indeks unik
        // di tingkat basis data — tapi yang tersimpan akan mengikuti kapital apa
        // pun yang diketik, dan surel yang sama bisa tampil dua rupa di seluruh
        // surat keluar.
        $request->merge([
            'name' => trim((string) $request->input('name')),
            'email' => mb_strtolower(trim((string) $request->input('email'))),
            'phone' => trim((string) $request->input('phone')) ?: null,
            'company' => trim((string) $request->input('company')) ?: null,
        ]);

        $data = $request->validate([
            // min:2 menutup nama satu huruf yang hampir selalu salah ketik atau
            // isian asal-asalan.
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:customers,email'],
            // Angka, spasi, dan tanda yang lazim dipakai nomor telepon. Sengaja
            // longgar: pelanggan korporat menulis nomornya dengan bermacam cara
            // ("+62 818-834-766", "(021) 5890 5002"), dan menolak salah satunya
            // hanya menghalangi pendaftaran tanpa membuat datanya lebih bersih.
            // Yang ditolak hanya yang jelas bukan nomor.
            'phone' => ['nullable', 'string', 'min:7', 'max:50', 'regex:/^[0-9+().\- ]+$/'],
            'company' => ['nullable', 'string', 'max:200'],
            'password' => ['required', 'confirmed', Password::defaults()],
            // Persetujuan atas Kebijakan Privasi dan Syarat & Ketentuan.
            // Undang-Undang Nomor 27 Tahun 2022 menuntut persetujuan yang
            // diberikan secara tegas, dan kotak centang yang harus dicentang
            // sendiri adalah bentuk paling sederhana yang memenuhinya. Tanpa
            // ini, dasar pemrosesan yang kami tulis sendiri di Kebijakan
            // Privasi tidak pernah benar-benar dikumpulkan.
            'terms' => ['accepted'],
        ], [], [
            'terms' => app()->getLocale() === 'id' ? 'persetujuan' : 'agreement',
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

        // Dev convenience: skip email verification when enabled (see config/customer.php).
        if (config('customer.auto_verify')) {
            $customer->forceFill(['email_verified_at' => now()])->save();
            Auth::guard('customer')->login($customer);

            return redirect()->intended(route('account.profile'))
                ->with('success', app()->getLocale() === 'id' ? 'Akun berhasil dibuat. Selamat datang!' : 'Account created. Welcome!');
        }

        event(new Registered($customer));
        $customer->sendEmailVerificationNotification();

        Auth::guard('customer')->login($customer);

        return redirect()->route('verification.notice');
    }
}
