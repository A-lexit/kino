<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Year;
use App\Models\Season;
use App\Models\Rating;
use App\Models\Status;
use App\Models\Age;
use App\Models\Quality;
use App\Models\Duration;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::ucfirst($this->faker->unique()->words(rand(2, 5), true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'origin_title' => $this->faker->optional()->realTextBetween(10, 50),
            
            'other_actor' => $this->faker->optional()->paragraph(1),
            'note' => $this->faker->optional()->sentence(),
            'description' => $this->faker->optional()->paragraphs(3, true),

            // Зв'язки (автоматично створюють нові моделі, якщо не передані явно)
            'author_id' => User::factory(),
            'category_id' => Category::factory(),
            'year_id' => Year::factory(),
            'season_id' => Season::factory(),
            'rating_id' => Rating::factory(),
            'status_id' => Status::factory(),
            'age_id' => Age::factory(),
            'quality_id' => Quality::factory(),
            'duration_id' => Duration::factory(),

            'view' => $this->faker->numberBetween(0, 10000),
            'thumbnail' => 'defaults/fake_movie_cover.webp', // файл має лежати в storage/app/public/posts/fake_movie_cover.jpg
            'datepicker' => $this->faker->optional()->date(),

            // Фіксуємо правильний текстовий статус для всіх фейкових фільмів
            'publish_status' => 'published',
            'is_featured' => fake()->boolean(),

            // Кадри для галереї (використовуємо стабільні заглушки або унікальні номери картинок)
            'gal_image1' => 'defaults/fake_movie_cover.webp',
            'gal_image2' => 'defaults/fake_movie_cover.webp',
            'gal_image3' => 'defaults/fake_movie_cover.webp',
            'gal_image4' => 'defaults/fake_movie_cover.webp',
            'gal_image5' => 'defaults/fake_movie_cover.webp',


            'trailer_youtube_id' => fake()->boolean(90) // 90% фільмів матимуть трейлер
                ? fake()->randomElement([
                    'dQw4w9WgXcQ', // просто відео-заглушка (Rick Astley) — 100% доступне, без регіональних блоків
                    'fmL5LHemqZY',
                    'YoHD9XEInc0', // Interstellar trailer
                    'zSWdZVtXT7E', // Avengers Endgame trailer
                    '5PSNL1qE6VY', // Avengers Infinity War trailer
                    'TcMBFSGVi1c', // Avengers trailer
                    'JfVOs4VSpmA', // Spider-Man: No Way Home trailer
                    '6ZfuNTqbHE8', // Avengers: Age of Ultron trailer
                    'giXco2jaZ_4', // Interstellar (offical trailer 2)
                    'eogpIG53Cis', // Guardians of the Galaxy trailer
                ])
                : null,
            'trailer_file' => null,

        ];
    }

}
