<?php
namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Director;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectorControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Перевірка відображення списку режисерів
     */
    public function test_index_displays_paginated_directors_list(): void
    {
        Director::factory()->count(25)->create();

        $response = $this->get(route('directors.index'));

        $response->assertOk();
        $response->assertViewIs('directors.index');
        $response->assertViewHas('directors');
        $this->assertCount(20, $response->viewData('directors'));
    }

    /**
     * Перевірка сторінки конкретного режисера
     */
    public function test_show_displays_director_and_associated_films(): void
    {
        $director = Director::factory()->create(['slug' => 'christopher-nolan']);
        $category = Category::factory()->create();

        Film::factory()->count(5)->create([
            'category_id' => $category->id
        ])->each(function ($film) use ($director) {
            $director->films()->attach($film);
        });

        $response = $this->get(route('directors.show', $director->slug));

        $response->assertOk();
        $response->assertViewIs('directors.show');
        $response->assertViewHasAll(['director', 'films']);
        $this->assertEquals($director->id, $response->viewData('director')->id);
        $this->assertCount(5, $response->viewData('films'));
    }

    /**
     * Перевірка 404 помилки
     */
    public function test_show_returns_404_if_director_not_found(): void
    {
        $response = $this->get(route('directors.show', 'unknown-director'));

        $response->assertNotFound();
    }

}
