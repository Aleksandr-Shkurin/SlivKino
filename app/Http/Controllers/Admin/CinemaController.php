<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CinemaController extends Controller
{
    public function index(): View
    {
        $cinemas = Cinema::withCount('halls')->orderBy('name')->paginate(15);
        return view('admin.cinemas.index', compact('cinemas'));
    }

    public function create(): View
    {
        $cinema = new Cinema();
        return view('admin.cinemas.form', compact('cinema'));
    }

    public function store(Request $request): RedirectResponse
    {
        Cinema::create($this->validated($request));
        return redirect()->route('admin.cinemas.index')->with('success', 'Кинотеатр добавлен.');
    }

    public function edit(Cinema $cinema): View
    {
        return view('admin.cinemas.form', compact('cinema'));
    }

    public function update(Request $request, Cinema $cinema): RedirectResponse
    {
        $cinema->update($this->validated($request));
        return redirect()->route('admin.cinemas.index')->with('success', 'Данные кинотеатра сохранены.');
    }

    public function destroy(Cinema $cinema): RedirectResponse
    {
        if ($cinema->halls()->exists()) {
            return back()->withErrors(['delete' => 'Сначала удалите залы этого кинотеатра или перенесите их в другой кинотеатр.']);
        }
        $cinema->delete();
        return redirect()->route('admin.cinemas.index')->with('success', 'Кинотеатр удалён.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'working_hours' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'features_text' => ['nullable', 'string', 'max:3000'],
        ]);
        $data['features'] = collect(preg_split('/[,;\n]+/', $data['features_text'] ?? ''))
            ->map(fn ($item) => trim($item))->filter()->values()->all();
        unset($data['features_text']);
        return $data;
    }
}
