<?php

namespace App\Http\Controllers;

use App\Models\Agenda;

class AgendaController extends Controller
{
    public function index()
    {
        return view('pages.agenda.index', [
            'agendas' => Agenda::published()
                // Upcoming events first (soonest-approaching at the very front),
                // then past events most-recent first.
                ->orderByRaw('starts_at >= NOW() DESC')
                ->orderByRaw('CASE WHEN starts_at >= NOW() THEN starts_at END ASC')
                ->orderByDesc('starts_at')
                ->paginate(9),
        ]);
    }
}
