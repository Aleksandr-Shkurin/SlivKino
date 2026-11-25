<?php
namespace App\Http\Controllers;
use App\Models\Movie;
use Illuminate\View\View;
class MovieController extends Controller
{
    public function show(Movie $movie): View
    {
        $movie->load(['genres', 'sessions.hall.cinema']);
        return view('movies.show', compact('movie'));
    }
}
