<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\GenreController as AdminGenreController;
use App\Http\Controllers\Admin\CinemaController as AdminCinemaController;
use App\Http\Controllers\Admin\HallController as AdminHallController;
use App\Http\Controllers\Admin\SessionController as AdminSessionController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/movies/{movie}', [MovieController::class, 'show'])
    ->name('movies.show');

Route::get('/cinemas', [CinemaController::class, 'index'])
    ->name('cinemas.index');
Route::get('/cinemas/{cinema}', [CinemaController::class, 'show'])
    ->name('cinemas.show');

Route::get('/sessions/{session}/seats', [SessionController::class, 'seats'])
    ->name('sessions.seats');
Route::post('/sessions/{session}/bookings', [BookingController::class, 'store'])
    ->name('sessions.bookings.store');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::middleware('admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('movies', AdminMovieController::class)->except(['show']);
        Route::resource('genres', AdminGenreController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('cinemas', AdminCinemaController::class)->except(['show']);
        Route::resource('halls', AdminHallController::class)->except(['show']);
        Route::resource('sessions', AdminSessionController::class)->except(['show']);
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');
    });
});

