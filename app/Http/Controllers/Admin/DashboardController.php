<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cinema;
use App\Models\Genre;
use App\Models\Hall;
use App\Models\Movie;
use App\Models\Session;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $counts = [
            'movies' => Movie::count(),
            'genres' => Genre::count(),
            'cinemas' => Cinema::count(),
            'halls' => Hall::count(),
            'sessions' => Session::count(),
            'bookings' => Booking::count(),
        ];

        $latestBookings = Booking::with('session.movie', 'session.hall.cinema')
            ->latest()->take(8)->get();

        return view('admin.dashboard', compact('counts', 'latestBookings'));
    }
}
