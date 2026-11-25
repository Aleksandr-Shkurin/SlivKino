<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function index(): View
    {
        $sessions = Session::with('movie', 'hall.cinema')->orderByDesc('starts_at')->paginate(20);
        return view('admin.sessions.index', compact('sessions'));
    }

    public function create(): View
    {
        $session = new Session();
        $movies = Movie::orderBy('title')->get();
        $halls = Hall::with('cinema')->orderBy('cinema_id')->orderBy('name')->get();
        return view('admin.sessions.form', compact('session', 'movies', 'halls'));
    }

    public function store(Request $request): RedirectResponse
    {
        Session::create($this->validated($request));
        return redirect()->route('admin.sessions.index')->with('success', 'Сеанс добавлен.');
    }

    public function edit(Session $session): View
    {
        $movies = Movie::orderBy('title')->get();
        $halls = Hall::with('cinema')->orderBy('cinema_id')->orderBy('name')->get();
        return view('admin.sessions.form', compact('session', 'movies', 'halls'));
    }

    public function update(Request $request, Session $session): RedirectResponse
    {
        $data = $this->validated($request);
        if ($session->bookings()->exists()) {
            foreach (['movie_id', 'hall_id', 'starts_at'] as $field) {
                $old = $field === 'starts_at' ? optional($session->starts_at)->format('Y-m-d H:i:s') : (string) $session->{$field};
                $new = $field === 'starts_at' ? date('Y-m-d H:i:s', strtotime($data[$field])) : (string) $data[$field];
                if ($old !== $new) {
                    return back()->withErrors(['session' => 'У этого сеанса уже есть бронирования. Можно изменить цену, но нельзя менять фильм, зал или время.'])->withInput();
                }
            }
        }
        $session->update($data);
        return redirect()->route('admin.sessions.index')->with('success', 'Данные сеанса сохранены.');
    }

    public function destroy(Session $session): RedirectResponse
    {
        if ($session->bookings()->exists()) {
            return back()->withErrors(['delete' => 'Нельзя удалить сеанс с бронированиями. Сначала отмените или удалите бронирования.']);
        }
        $session->delete();
        return redirect()->route('admin.sessions.index')->with('success', 'Сеанс удалён.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'movie_id' => ['required', 'integer', 'exists:movies,id'],
            'hall_id' => ['required', 'integer', 'exists:halls,id'],
            'starts_at' => ['required', 'date'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
        ]);
    }
}
