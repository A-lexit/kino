<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Selection>
 */
class SelectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::ucfirst($this->faker->unique()->words(rand(2, 4), true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
        ];
    }

}
