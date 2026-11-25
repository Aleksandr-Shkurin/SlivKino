<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function index(): View
    {
        $movies = Movie::with('genres')->withCount('sessions')->orderByDesc('id')->paginate(15);
        return view('admin.movies.index', compact('movies'));
    }

    public function create(): View
    {
        $movie = new Movie();
        $genres = Genre::orderBy('name')->get();
        return view('admin.movies.form', compact('movie', 'genres'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $movie = Movie::create($data['attributes']);
        $movie->genres()->sync($data['genres']);

        return redirect()->route('admin.movies.index')->with('success', 'Фильм добавлен.');
    }

    public function edit(Movie $movie): View
    {
        $movie->load('genres');
        $genres = Genre::orderBy('name')->get();
        return view('admin.movies.form', compact('movie', 'genres'));
    }

    public function update(Request $request, Movie $movie): RedirectResponse
    {
        $data = $this->validated($request);
        $movie->update($data['attributes']);
        $movie->genres()->sync($data['genres']);

        return redirect()->route('admin.movies.index')->with('success', 'Изменения фильма сохранены.');
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        if ($movie->sessions()->exists()) {
            return back()->withErrors(['delete' => 'Нельзя удалить фильм, пока для него заведены сеансы. Сначала удалите или перенесите сеансы.']);
        }

        $movie->genres()->detach();
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Фильм удалён.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'year' => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'age_rating' => ['nullable', 'string', 'max:10'],
            'release_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:release_date'],
            'poster' => ['nullable', 'string', 'max:2048'],
            'poster_file' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'full_description' => ['nullable', 'string'],
            'director' => ['nullable', 'string', 'max:255'],
            'cast_text' => ['nullable', 'string', 'max:5000'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'coming_soon' => ['required', 'boolean'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['integer', 'exists:genres,id'],
        ]);

        $poster = $validated['poster'] ?? null;
        if ($request->hasFile('poster_file')) {
            $directory = public_path('images/posters');
            File::ensureDirectoryExists($directory);
            $file = $request->file('poster_file');
            $filename = uniqid('movie_', true) . '.' . $file->guessExtension();
            $file->move($directory, $filename);
            $poster = 'images/posters/' . $filename;
        }

        $cast = collect(preg_split('/[,;\n]+/', $validated['cast_text'] ?? ''))
            ->map(fn ($person) => trim($person))
            ->filter()->values()->all();

        $attributes = collect($validated)->except(['poster_file', 'cast_text', 'genres'])->all();
        $attributes['poster'] = $poster;
        $attributes['cast'] = $cast;
        $attributes['coming_soon'] = (bool) $validated['coming_soon'];

        return ['attributes' => $attributes, 'genres' => $validated['genres'] ?? []];
    }
}
