<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Rules\PersonName;
use App\Rules\PhoneNumber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact', [
            // Sumber yang sama dengan footer, jadi alamat di halaman ini tidak
            // bisa berbeda dari alamat di kakinya. Nama kantornya sudah dipilih
            // menurut bahasa aktif di dalam Company::offices().
            'offices' => \App\Support\Company::offices(),
        ]);
    }

    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', new PersonName],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50', new PhoneNumber],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([...$data, 'locale' => app()->getLocale(), 'is_read' => false]);

        return back()->with('status', __('site.contact.success'));
    }
}
