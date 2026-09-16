<?php

namespace App\Http\Controllers;

use App\Models\CertificateHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CertificateController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Satu definisi pencarian dipakai dua kali: untuk daftarnya dan untuk
        // menghitung yang kedaluwarsa. Dibuat sebagai closure supaya tiap
        // pemakaian memperoleh query baru, bukan query yang sudah dibebani
        // paginator.
        $matching = fn () => CertificateHolder::active()
            ->when($q !== '', function ($query) use ($q) {
                $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
                $query->where(function ($sub) use ($escaped) {
                    $sub->where('participant_name', 'like', "%{$escaped}%")
                        ->orWhere('company_name', 'like', "%{$escaped}%")
                        ->orWhere('certificate_number', 'like', "%{$escaped}%")
                        ->orWhere('ujk_number', 'like', "%{$escaped}%")
                        ->orWhere('qualification', 'like', "%{$escaped}%");
                });
            });

        // Dihitung atas seluruh hasil pencarian, bukan hanya halaman yang tampil —
        // kalau dihitung dari koleksi paginator, angkanya berubah setiap ganti
        // halaman dan berhenti berarti apa-apa.
        $expiredCount = $matching()->whereNotNull('expires_at')
            ->whereDate('expires_at', '<', Carbon::today())
            ->count();

        $certificates = $matching()
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString()
            // Setiap tautan halaman berakhir di #hasil. Pagination memuat ulang
            // halaman penuh, jadi tanpa fragmen ini browser selalu mendarat di
            // puncak dokumen dan pembaca kehilangan tempatnya.
            ->fragment(self::RESULTS_ANCHOR);

        return view('pages.sertifikat.index', [
            'certificates' => $certificates,
            'q' => $q,
            'expiredCount' => $expiredCount,
            'suggestions' => $this->topQualifications(),
            'registry' => $this->registryScope(),
        ]);
    }

    /**
     * Susunan daftar ini menurut ketiga keadaan yang sama persis dengan lencana
     * pada kolom hasil: berlaku, segera berakhir, dan kedaluwarsa. Ditambah
     * jumlah keseluruhan dan jumlah perusahaan.
     *
     * Ambangnya menyalin x-certificate-status — kedaluwarsa bila tanggalnya
     * sudah lewat, segera berakhir bila jatuh dalam 90 hari ke depan. Kalau
     * salah satu ambang bergeser tanpa yang lain, legenda di halaman akan
     * menyebut angka yang tidak cocok dengan lencana di sebelahnya.
     *
     * Sengaja TIDAK mengikuti kata kunci pencarian. Angka yang ikut menyusut
     * saat orang mencari akan berbunyi "1 dari 1" — tidak memberi tahu apa pun
     * tentang seberapa besar daftarnya, padahal justru itu yang menenangkan
     * pengunjung yang sedang memeriksa keaslian sebuah sertifikat.
     *
     * DATE() dipakai di ketiganya supaya ketiga ember itu saling lepas dan
     * jumlahnya pas dengan total; tanpa itu sertifikat yang berakhir hari ini
     * bisa lolos dari semua ember gara-gara jamnya.
     */
    private function registryScope(): ?object
    {
        $today = Carbon::today()->toDateString();
        $soonLimit = Carbon::today()->addDays(90)->toDateString();

        return CertificateHolder::active()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(DISTINCT NULLIF(company_name, '')) as companies")
            ->selectRaw('SUM(CASE WHEN expires_at IS NULL OR DATE(expires_at) > ? THEN 1 ELSE 0 END) as valid', [$soonLimit])
            ->selectRaw('SUM(CASE WHEN expires_at IS NOT NULL AND DATE(expires_at) >= ? AND DATE(expires_at) <= ? THEN 1 ELSE 0 END) as soon', [$today, $soonLimit])
            ->selectRaw('SUM(CASE WHEN expires_at IS NOT NULL AND DATE(expires_at) < ? THEN 1 ELSE 0 END) as expired', [$today])
            ->first();
    }

    /**
     * Kualifikasi yang paling banyak dipegang, untuk pintasan pencarian.
     *
     * Sengaja dihitung dari seluruh data aktif, bukan dari hasil pencarian yang
     * sedang tampil: gunanya justru menawarkan jalan keluar ketika pencarian
     * pengunjung tidak membuahkan apa-apa.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function topQualifications()
    {
        return CertificateHolder::active()
            ->whereNotNull('qualification')
            ->where('qualification', '!=', '')
            ->groupBy('qualification')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(4)
            ->pluck('qualification');
    }
}
