<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Hall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class HallController extends Controller
{
    public function index(): View
    {
        $halls = Hall::with('cinema')->withCount('sessions')->orderBy('cinema_id')->orderBy('name')->paginate(20);
        return view('admin.halls.index', compact('halls'));
    }

    public function create(): View
    {
        $hall = new Hall();
        $cinemas = Cinema::orderBy('name')->get();
        return view('admin.halls.form', compact('hall', 'cinemas'));
    }

    public function store(Request $request): RedirectResponse
    {
        Hall::create($this->validated($request));
        return redirect()->route('admin.halls.index')->with('success', 'Зал добавлен.');
    }

    public function edit(Hall $hall): View
    {
        $cinemas = Cinema::orderBy('name')->get();
        return view('admin.halls.form', compact('hall', 'cinemas'));
    }

    public function update(Request $request, Hall $hall): RedirectResponse
    {
        $data = $this->validated($request);
        if ($hall->sessions()->whereHas('bookings')->exists()) {
            if ((int) $data['rows_count'] !== (int) $hall->rows_count || (int) $data['seats_per_row'] !== (int) $hall->seats_per_row) {
                return back()->withErrors(['rows_count' => 'Нельзя менять размер схемы зала: в одном из его сеансов уже есть бронирования.'])->withInput();
            }
        }
        $hall->update($data);
        return redirect()->route('admin.halls.index')->with('success', 'Параметры зала сохранены.');
    }

    public function destroy(Hall $hall): RedirectResponse
    {
        if ($hall->sessions()->exists()) {
            return back()->withErrors(['delete' => 'Нельзя удалить зал, пока в нём есть сеансы. Сначала удалите или перенесите сеансы.']);
        }
        $hall->delete();
        return redirect()->route('admin.halls.index')->with('success', 'Зал удалён.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'cinema_id' => ['required', 'integer', 'exists:cinemas,id'],
            'name' => ['required', 'string', 'max:255'],
            'rows_count' => ['required', 'integer', 'min:1', 'max:100'],
            'seats_per_row' => ['required', 'integer', 'min:1', 'max:100'],
            'vip_seats_text' => ['nullable', 'string', 'max:5000'],
            'disabled_seats_text' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['vip_seats'] = $this->parseSeats($data['vip_seats_text'] ?? '', (int) $data['rows_count'], (int) $data['seats_per_row'], 'vip_seats_text');
        $data['disabled_seats'] = $this->parseSeats($data['disabled_seats_text'] ?? '', (int) $data['rows_count'], (int) $data['seats_per_row'], 'disabled_seats_text');
        $overlap = array_intersect($data['vip_seats'], $data['disabled_seats']);
        if ($overlap !== []) {
            throw ValidationException::withMessages(['disabled_seats_text' => 'Одно место нельзя одновременно назначить VIP и местом для маломобильных посетителей: ' . implode(', ', $overlap) . '.']);
        }
        unset($data['vip_seats_text'], $data['disabled_seats_text']);
        return $data;
    }

    private function parseSeats(string $input, int $rows, int $seatsPerRow, string $errorField): array
    {
        $items = collect(preg_split('/[,;\s]+/', trim($input)))->filter()->unique()->values();
        $result = [];
        foreach ($items as $item) {
            if (!preg_match('/^(\d{1,3})-(\d{1,3})$/', $item, $matches)) {
                throw ValidationException::withMessages([$errorField => "Некорректный формат места «{$item}». Используйте формат ряд-место, например 7-4."]);
            }
            $row = (int) $matches[1];
            $seat = (int) $matches[2];
            if ($row < 1 || $row > $rows || $seat < 1 || $seat > $seatsPerRow) {
                throw ValidationException::withMessages([$errorField => "Место {$item} не входит в заданную схему зала."]);
            }
            $result[] = $item;
        }
        return $result;
    }
}
