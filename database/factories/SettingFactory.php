<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => Str::ucfirst($this->faker->unique()->optional()->words(rand(1, 2), true)),
            'description' => $this->faker->optional()->sentence(12),
            'favicon' => $this->faker->optional()->imageUrl(32, 32, 'business'),
        ];
    }

}
