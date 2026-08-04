<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\State>
 */
class StateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $views = $this->faker->numberBetween(0, 100000);

        return [
            'vviews' => $views,
            'likes' => $this->faker->numberBetween(0, max(0, intval($views * 0.1))),
            'film_id' => Film::factory(),
        ];
    }

}
