<?php
namespace Tests\Feature\Front;

use App\Models\Film;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_languages_page(): void
    {
        Language::factory()->count(3)->create();

        $response = $this->get(route('languages.index'));

        $response->assertOk();

        $response->assertViewIs('languages.index');

        $response->assertViewHas('languages');

        $this->assertCount(
            3,
            $response->viewData('languages')
        );
    }


    public function test_show_displays_language_films(): void
    {
        $language = Language::factory()->create([
            'slug' => 'ukrainian',
        ]);

        $film = Film::factory()->create();

        $film->languages()->attach($language);

        $response = $this->get(
            route('languages.show', $language->slug)
        );

        $response->assertOk();

        $response->assertViewIs('languages.show');

        $response->assertViewHas([
            'language',
            'films',
        ]);

        $this->assertEquals(
            $language->id,
            $response->viewData('language')->id
        );

        $this->assertCount(
            1,
            $response->viewData('films')
        );

        $this->assertEquals(
            $film->id,
            $response->viewData('films')->first()->id
        );
    }

}
