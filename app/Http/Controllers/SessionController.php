<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function seats(Session $session): View
    {
        $session->load(['movie.genres', 'hall.cinema']);

        $hall = $session->hall;
        $occupiedSeats = $session->bookings()
            ->whereIn('status', ['reserved', 'confirmed', 'paid'])
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->get(['seats'])
            ->flatMap(fn ($booking) => $booking->seats ?? [])
            ->unique()
            ->values()
            ->all();

        $vipSeats = $hall->vip_seats ?? [];
        // В исходном проекте disabled_seats используются как места для кресел-колясок.
        $wheelchairSeats = $hall->disabled_seats ?? [];

        return view('sessions.seats', compact(
            'session',
            'hall',
            'occupiedSeats',
            'vipSeats',
            'wheelchairSeats'
        ));
    }
}
