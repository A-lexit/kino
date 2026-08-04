<?php
namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Film;
use App\Media\FilmImageResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSuggestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_empty_array_if_query_is_less_than_two_characters()
    {
        $response1 = $this->getJson(route('search.suggestions', ['q' => '']));
        $response1->assertStatus(200)->assertExactJson([]);

        $response2 = $this->getJson(route('search.suggestions', ['q' => 'a']));
        $response2->assertStatus(200)->assertExactJson([]);
    }

    public function test_it_returns_suggestions_for_matching_title()
    {
        // Мокаємо сервіс, щоб не шукати реальні файли картинок під час тесту
        $this->mock(FilmImageResolver::class, function ($mock) {
            $mock->shouldReceive('search')->andReturn('http://example.com/test-image.jpg');
        });

        $category = Category::factory()->create(['slug' => 'action']);

        // Зверніть увагу: якщо scope published() вимагає певного статусу (напр. 'status' => 'published'),
        // додайте його у масив створення фабрики або використовуйте state
        Film::factory()->create([
            'title' => 'Матриця',
            'origin_title' => 'The Matrix',
            'slug' => 'the-matrix',
            'category_id' => $category->id,
        ]);

        $response = $this->getJson(route('search.suggestions', ['q' => 'Матр']));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment([
            'title' => 'Матриця',
            'image' => 'http://example.com/test-image.jpg',
            'url' => route('single', ['category' => 'action', 'slug' => 'the-matrix']),
        ]);
    }

    public function test_it_searches_by_origin_title()
    {
        $this->mock(FilmImageResolver::class, function ($mock) {
            $mock->shouldReceive('search')->andReturn('dummy.jpg');
        });

        $category = Category::factory()->create(['slug' => 'comedy']);

        Film::factory()->create([
            'title' => 'Похмілля у Вегасі',
            'origin_title' => 'The Hangover',
            'slug' => 'the-hangover',
            'category_id' => $category->id,
        ]);

        $response = $this->getJson(route('search.suggestions', ['q' => 'Hangov']));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.title', 'Похмілля у Вегасі');
    }

    public function test_it_limits_results_to_five()
    {
        $this->mock(FilmImageResolver::class, function ($mock) {
            $mock->shouldReceive('search')->andReturn('dummy.jpg');
        });

        $category = Category::factory()->create();

        // Використовуємо sequence для генерації унікальних назв
        Film::factory()
            ->count(10)
            ->sequence(fn ($sequence) => ['title' => 'Harry Potter ' . $sequence->index])
            ->create([
                'category_id' => $category->id,
            ]);

        $response = $this->getJson(route('search.suggestions', ['q' => 'Harry']));

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

}
