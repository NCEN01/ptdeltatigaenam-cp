<?php

namespace App\Http\Controllers;

use App\Models\CertificateHolder;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $certificates = CertificateHolder::active()
            ->when($q !== '', function ($query) use ($q) {
                $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
                $query->where(function ($sub) use ($escaped) {
                    $sub->where('participant_name', 'like', "%{$escaped}%")
                        ->orWhere('company_name', 'like', "%{$escaped}%")
                        ->orWhere('certificate_number', 'like', "%{$escaped}%")
                        ->orWhere('ujk_number', 'like', "%{$escaped}%")
                        ->orWhere('qualification', 'like', "%{$escaped}%");
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.sertifikat.index', compact('certificates', 'q'));
    }
}
