<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Film;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->optional()->sentence(),
            'body' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([0, 1]),
            'film_id' => Film::factory(),
            'user_id' => User::factory(),
        ];
    }

}
