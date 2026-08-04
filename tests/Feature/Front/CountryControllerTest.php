<?php
namespace Tests\Feature\Front;

use App\Models\Category;
use App\Models\Country;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Перевірка відображення списку країн з пагінацією
     */
    public function test_index_displays_paginated_countries_list(): void
    {
        Country::factory()->count(25)->create();

        $response = $this->get(route('countries.index'));

        $response->assertOk();
        $response->assertViewIs('countries.index');
        $response->assertViewHas('countries');
        $this->assertCount(20, $response->viewData('countries'));
    }

    /**
     * Перевірка відображення сторінки країни та пов'язаних фільмів
     */
    public function test_show_displays_country_and_associated_films(): void
    {
        $country = Country::factory()->create(['slug' => 'ukraine']);

        // Створюємо категорію, оскільки вона завантажується через with('category')
        $category = Category::factory()->create();

        // Створюємо фільми та прив'язуємо їх до країни
        Film::factory()->count(5)->create([
            'category_id' => $category->id
        ])->each(function ($film) use ($country) {
            $country->films()->attach($film);
        });

        $response = $this->get(route('countries.show', $country->slug));

        $response->assertOk();
        $response->assertViewIs('countries.show');
        $response->assertViewHasAll(['country', 'films']);
        $this->assertEquals($country->id, $response->viewData('country')->id);
        $this->assertCount(5, $response->viewData('films'));
    }

    /**
     * Перевірка 404 помилки, якщо країну не знайдено
     */
    public function test_show_returns_404_if_country_not_found(): void
    {
        $response = $this->get(route('countries.show', 'non-existent-slug'));

        $response->assertNotFound();
    }

}
