<?php

namespace Database\Factories;

use App\Models\Cinema;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cinema>
 */
class CinemaFactory extends Factory
{
    protected $model = Cinema::class;

    public function definition(): array
    {
        return [
            'name' => 'Кинотеатр «' . fake()->unique()->city() . '»',
            'name_en' => fake()->city(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'working_hours' => '10:00–23:00',
            'description' => fake()->paragraph(),
            'features' => fake()->randomElements([
                '3D',
                'IMAX',
                'Кафе',
                'Парковка',
                'VIP-зал',
            ], 3),
        ];
    }
}
