<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\View\View;

class CinemaController extends Controller
{
    public function index(): View
    {
        $cinemas = Cinema::query()
            ->with('halls')
            ->withCount('halls')
            ->orderBy('name')
            ->get();

        return view('cinemas.index', compact('cinemas'));
    }

    public function show(Cinema $cinema): View
    {
        $cinema->load([
            'halls' => fn ($query) => $query->orderBy('name'),
            'halls.sessions' => fn ($query) => $query->orderBy('starts_at'),
            'halls.sessions.movie.genres',
        ]);

        return view('cinemas.show', compact('cinema'));
    }
}
