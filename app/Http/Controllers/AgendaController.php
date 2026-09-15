<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    /** Two rows of three cards per page, matching the blog index. */
    private const PER_PAGE = 6;

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Satu definisi pencarian dipakai tiga kali: untuk daftarnya dan untuk
        // kedua angka ringkasan. Closure supaya tiap pemakaian memperoleh query
        // baru, bukan query yang sudah dibebani paginator.
        $matching = function () use ($q) {
            $needle = $this->likeNeedle($q);

            return Agenda::published()->when($q !== '', fn ($query) => $query->where(
                fn ($sub) => $sub->whereRaw('LOWER(title) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(location) LIKE ?', [$needle]),
            ));
        };

        return view('pages.agenda.index', [
            'agendas' => $matching()
                // Upcoming events first (soonest-approaching at the very front),
                // then past events most-recent first.
                ->orderByRaw('starts_at >= NOW() DESC')
                ->orderByRaw('CASE WHEN starts_at >= NOW() THEN starts_at END ASC')
                ->orderByDesc('starts_at')
                ->paginate(self::PER_PAGE)->withQueryString()->fragment(self::RESULTS_ANCHOR),

            'q' => $q,

            // Dihitung atas hasil pencarian yang sama, bukan seluruh data: kalau
            // dihitung dari keseluruhan, angkanya bertentangan dengan daftar yang
            // sedang tampil saat pencarian aktif.
            'upcomingCount' => $matching()->where('starts_at', '>=', now())->count(),
            'pastCount' => $matching()->where('starts_at', '<', now())->count(),
        ]);
    }
}
