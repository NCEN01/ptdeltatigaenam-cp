<?php

namespace App\Http\Controllers;

use App\Models\Agenda;

class AgendaController extends Controller
{
    public function index()
    {
        return view('pages.agenda.index', [
            'agendas' => Agenda::published()->orderByDesc('starts_at')->paginate(9),
        ]);
    }
}
