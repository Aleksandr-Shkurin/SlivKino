<?php

namespace Database\Factories;

use App\Models\Cinema;
use App\Models\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hall>
 */
class HallFactory extends Factory
{
    protected $model = Hall::class;

    public function definition(): array
    {
        return [
            'cinema_id' => Cinema::factory(),
            'name' => 'Зал ' . fake()->unique()->numberBetween(1, 10000),
            'rows_count' => fake()->numberBetween(5, 12),
            'seats_per_row' => fake()->numberBetween(8, 20),
            'vip_seats' => [],
            'disabled_seats' => [],
        ];
    }
}
