<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GenreController extends Controller
{
    public function index(): View
    {
        $genres = Genre::withCount('movies')->orderBy('name')->paginate(25);
        return view('admin.genres.index', compact('genres'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:genres,name']]);
        Genre::create($data);
        return back()->with('success', 'Жанр добавлен.');
    }

    public function update(Request $request, Genre $genre): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:genres,name,' . $genre->id]]);
        $genre->update($data);
        return back()->with('success', 'Название жанра изменено.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $genre->delete();
        return back()->with('success', 'Жанр удалён. Фильмы сохранены, связь с жанром снята.');
    }
}
