<?php

namespace Database\Factories;

use App\Models\Hall;
use App\Models\Movie;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'hall_id' => Hall::factory(),
            'starts_at' => fake()->dateTimeBetween(
                '+1 day',
                '+30 days'
            ),
            'price' => fake()->randomFloat(2, 300, 1500),
        ];
    }
}
