<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // Alamat kantor mengikuti materi resmi perusahaan. Namanya sengaja tidak
        // diterjemahkan: "Head Office" dan "Operational Office" adalah sebutan
        // yang dipakai perusahaan sendiri di materi itu, jadi sama di kedua bahasa.
        $offices = [
            [
                'name' => 'Head Office',
                'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, Senayan, Kebayoran Baru, Jakarta Selatan – DKI Jakarta 12190',
                'phone' => '(021) 5890 5002',
                'maps' => 'Gedung Bursa Efek Indonesia Tower 1, Jalan Jenderal Sudirman Kav. 52-53, Senayan, Kebayoran Baru, Jakarta Selatan 12190',
            ],
            [
                'name' => 'Operational Office',
                'address' => 'Delta House, Pejaten Mas Estate Blok D3 No. 6, Pejaten, Kramatwatu, Serang – Banten 42161',
                'phone' => '0818 834 766',
                'maps' => 'Pejaten Mas Estate Blok D3 No. 6, Pejaten, Kramatwatu, Serang, Banten 42161',
            ],
        ];

        return view('pages.contact', [
            'offices' => $offices,
        ]);
    }

    public function store(Request $request, string $locale)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactMessage::create([...$data, 'locale' => app()->getLocale(), 'is_read' => false]);

        return back()->with('status', __('site.contact.success'));
    }
}
