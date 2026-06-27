<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact', [
            'offices' => OfficeLocation::where('is_active', true)->orderBy('sort_order')->get(),
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
