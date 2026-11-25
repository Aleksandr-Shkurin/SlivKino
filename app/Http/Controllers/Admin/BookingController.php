<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with('session.movie', 'session.hall.cinema')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        $bookings = $query->paginate(20)->withQueryString();
        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:reserved,confirmed,paid,cancelled'],
        ]);
        DB::transaction(function () use ($booking, $data) {
            $locked = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            if (in_array($data['status'], ['reserved', 'confirmed', 'paid'], true)) {
                $occupied = Booking::query()
                    ->where('session_id', $locked->session_id)
                    ->whereKeyNot($locked->id)
                    ->whereIn('status', ['reserved', 'confirmed', 'paid'])
                    ->where(function ($query) {
                        $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                    })
                    ->get(['seats'])
                    ->flatMap(fn ($item) => $item->seats ?? [])
                    ->unique()->all();
                $collision = array_values(array_intersect($locked->seats ?? [], $occupied));
                if ($collision !== []) {
                    throw ValidationException::withMessages([
                        'status' => 'Нельзя активировать бронирование: места уже заняты в другом заказе (' . implode(', ', $collision) . ').',
                    ]);
                }
            }

            $locked->status = $data['status'];
            if ($data['status'] === 'cancelled') {
                $locked->expires_at = now();
            } elseif ($data['status'] === 'reserved') {
                $locked->expires_at = now()->addMinutes(15);
            } else {
                $locked->expires_at = null;
            }
            $locked->save();
        });

        return back()->with('success', 'Статус бронирования #' . $booking->id . ' изменён.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $id = $booking->id;
        $booking->delete();
        return back()->with('success', 'Бронирование #' . $id . ' удалено, места снова доступны.');
    }
}
