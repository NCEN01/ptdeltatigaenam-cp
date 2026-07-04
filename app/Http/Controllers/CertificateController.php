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
                $query->where(function ($sub) use ($q) {
                    $sub->where('participant_name', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%")
                        ->orWhere('certificate_number', 'like', "%{$q}%")
                        ->orWhere('ujk_number', 'like', "%{$q}%")
                        ->orWhere('qualification', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('pages.sertifikat.index', compact('certificates', 'q'));
    }
}
