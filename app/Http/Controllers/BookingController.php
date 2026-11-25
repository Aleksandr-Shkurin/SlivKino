<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request, Session $session): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'seats' => ['required', 'array', 'min:1', 'max:20'],
            'seats.*' => ['required', 'string', 'distinct', 'regex:/^\\d{1,3}-\\d{1,3}$/'],
        ], [
            'seats.required' => 'Выберите хотя бы одно место.',
            'seats.min' => 'Выберите хотя бы одно место.',
            'seats.max' => 'За один раз можно забронировать не более 20 мест.',
            'seats.*.distinct' => 'В списке есть повторяющиеся места.',
            'customer_name.required' => 'Укажите имя.',
            'customer_email.required' => 'Укажите электронную почту.',
            'customer_email.email' => 'Введите корректный адрес электронной почты.',
        ]);

        $booking = DB::transaction(function () use ($session, $validated) {
            // Блокировка строки сеанса не позволяет двум запросам одновременно
            // успешно занять одно и то же место.
            $lockedSession = Session::query()
                ->with('hall')
                ->lockForUpdate()
                ->findOrFail($session->id);

            $hall = $lockedSession->hall;
            $requestedSeats = array_values(array_unique($validated['seats']));
            $vipSeats = $hall->vip_seats ?? [];

            foreach ($requestedSeats as $seatId) {
                [$row, $seatNumber] = array_map('intval', explode('-', $seatId));

                if (
                    $row < 1 || $row > $hall->rows_count ||
                    $seatNumber < 1 || $seatNumber > $hall->seats_per_row
                ) {
                    throw ValidationException::withMessages([
                        'seats' => "Место {$seatId} не существует в этом зале.",
                    ]);
                }

            }

            $occupiedSeats = $lockedSession->bookings()
                ->whereIn('status', ['reserved', 'confirmed', 'paid'])
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->get(['seats'])
                ->flatMap(fn ($existingBooking) => $existingBooking->seats ?? [])
                ->unique()
                ->all();

            $alreadyTaken = array_values(array_intersect($requestedSeats, $occupiedSeats));

            if ($alreadyTaken !== []) {
                throw ValidationException::withMessages([
                    'seats' => 'К сожалению, эти места уже заняты: ' . implode(', ', $alreadyTaken) . '. Обновите схему и выберите другие.',
                ]);
            }

            return Booking::create([
                'session_id' => $lockedSession->id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'seats' => $requestedSeats,
                'status' => 'reserved',
                'expires_at' => now()->addMinutes(15),
            ]);
        });

        return redirect()
            ->route('sessions.seats', $session)
            ->with('booking_reference', $booking->id)
            ->with('success', 'Места успешно забронированы на 15 минут. Номер бронирования: #' . $booking->id . '.');
    }
}
