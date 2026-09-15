<?php

namespace App\Http\Controllers;

use App\Models\CertificateHolder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CertificateController extends Controller
{
    private const PER_PAGE = 10;

    /**
     * Anchor tujuan setelah mencari atau berpindah halaman. Dibagi ke view lewat
     * satu tempat supaya id pada markup dan fragmen pada tautan tidak bisa
     * berbeda — kalau berbeda, fragmennya diam-diam tidak menuju ke mana pun.
     */
    public const RESULTS_ANCHOR = 'hasil';

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
        ]);
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
