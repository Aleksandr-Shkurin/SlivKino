<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'session_id' => Session::factory(),
            'user_id' => fake()->boolean(70)
                ? User::factory()
                : null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'seats' => [
                'A1',
                'A2',
            ],
            'status' => fake()->randomElement([
                'reserved',
                'confirmed',
                'cancelled',
            ]),
            'expires_at' => now()->addMinutes(15),
        ];
    }
}
