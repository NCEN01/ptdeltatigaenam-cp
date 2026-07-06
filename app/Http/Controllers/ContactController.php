<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $id = app()->getLocale() === 'id';
        $offices = [
            [
                'name' => 'Kantor Pusat (SCBD)',
                'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, SCBD Senayan, Kebayoran Baru, Jakarta Selatan',
                'phone' => '021-5890 5002',
                'maps' => 'Gedung Bursa Efek Indonesia Tower 1, SCBD, Jakarta Selatan',
            ],
            [
                'name' => $id ? 'Kantor Pemasaran' : 'Marketing Office',
                'address' => 'Cikarang Technopark, Jalan Inti I Blok C1 No. 7, Cibatu, Cikarang Selatan, Kabupaten Bekasi, Jawa Barat 17530',
                'phone' => '021-8988 1110',
                'maps' => 'Cikarang Technopark, Cikarang Selatan, Bekasi',
            ],
            [
                'name' => $id ? 'Kantor Operasional' : 'Operational Office',
                'address' => 'Taman Widya Asri Blok GG No. 18, Serang, Kota Serang, Banten 46111',
                'phone' => '0817 018 6104',
                'maps' => 'Taman Widya Asri, Serang, Banten',
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
