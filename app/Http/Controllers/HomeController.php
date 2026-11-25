<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $genreId = $request->query('genre');
        $genreId = is_numeric($genreId) ? (int) $genreId : null;

        $genres = Genre::query()->orderBy('name')->get();

        $applyFilters = function ($query) use ($search, $genreId) {
            if ($search !== '') {
                $query->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', "%{$search}%")
                        ->orWhere('title_en', 'like', "%{$search}%");
                });
            }

            if ($genreId) {
                $query->whereHas('genres', function ($genreQuery) use ($genreId) {
                    $genreQuery->where('genres.id', $genreId);
                });
            }

            return $query;
        };

        $nowPlaying = $applyFilters(
            Movie::query()->with('genres')->where('coming_soon', false)
        )->orderBy('title')->get();

        $comingSoon = $applyFilters(
            Movie::query()->with('genres')->where('coming_soon', true)
        )->orderBy('release_date')->get();

        return view('home', compact('nowPlaying', 'comingSoon', 'genres', 'search', 'genreId'));
    }
}
