<?php

namespace App\Http\Controllers;

use App\Models\Agenda;

class AgendaController extends Controller
{
    /** Two rows of three cards per page, matching the blog index. */
    private const PER_PAGE = 6;

    public function index()
    {
        return view('pages.agenda.index', [
            'agendas' => Agenda::published()
                // Upcoming events first (soonest-approaching at the very front),
                // then past events most-recent first.
                ->orderByRaw('starts_at >= NOW() DESC')
                ->orderByRaw('CASE WHEN starts_at >= NOW() THEN starts_at END ASC')
                ->orderByDesc('starts_at')
                ->paginate(self::PER_PAGE)->fragment(self::RESULTS_ANCHOR),

            // Counted across the whole set, not just the current page, so the summary
            // stays honest when every published event has already happened.
            'upcomingCount' => Agenda::published()->where('starts_at', '>=', now())->count(),
            'pastCount' => Agenda::published()->where('starts_at', '<', now())->count(),
        ]);
    }
}
