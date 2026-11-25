<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        $releaseDate = fake()->dateTimeBetween('-6 months', '+2 months');
        $comingSoon = fake()->boolean(20);

        return [
            'title' => fake()->unique()->sentence(3),
            'title_en' => fake()->words(3, true),
            'country' => fake()->randomElement([
                'США',
                'Россия',
                'Великобритания',
                'Франция',
                'Япония',
            ]),
            'year' => (int) $releaseDate->format('Y'),
            'age_rating' => fake()->randomElement([
                '0+', '6+', '12+', '16+', '18+'
            ]),
            'release_date' => $releaseDate,
            'end_date' => fake()->dateTimeBetween(
                $releaseDate,
                (clone $releaseDate)->modify('+6 months')
            ),
            'poster' => null,
            'description' => fake()->paragraph(),
            'full_description' => fake()->paragraphs(3, true),
            'director' => fake()->name(),
            'cast' => fake()->randomElements([
                'Актёр 1',
                'Актёр 2',
                'Актёр 3',
                'Актёр 4',
                'Актёр 5',
            ], 3),
            'duration_minutes' => fake()->numberBetween(80, 200),
            'coming_soon' => $comingSoon,
        ];
    }
}
