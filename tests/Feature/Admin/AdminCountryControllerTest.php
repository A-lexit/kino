<?php
namespace Tests\Feature\Admin;

use App\Models\Country;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCountryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_countries(): void
    {
        $this->actingAs($this->admin);
        Country::factory()->count(3)->create();

        $this->get(route('admin.countries.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.countries.index')
            ->assertViewHas('countries');
    }

    public function test_store_saves_country_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['title' => 'Ukraine'];

        $this->post(route('admin.countries.store'), $data)
            ->assertRedirect(route('admin.countries.index'))
            ->assertSessionHas('success', 'Країну додано');

        $this->assertDatabaseHas('countries', $data);
    }

    public function test_update_modifies_country_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $country = Country::factory()->create(['title' => 'Old Title']);
        $newData = ['title' => 'New Title'];

        $this->put(route('admin.countries.update', $country->id), $newData)
            ->assertRedirect(route('admin.countries.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('countries', ['id' => $country->id, 'title' => 'New Title']);
    }

    public function test_destroy_deletes_country_when_no_films_linked(): void
    {
        $this->actingAs($this->admin);
        $country = Country::factory()->create();

        $this->delete(route('admin.countries.destroy', $country))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('countries', [
            'id' => $country->id,
        ]);

    }

    public function test_destroy_prevents_deletion_if_films_linked(): void
    {
        $this->actingAs($this->admin);
        $country = Country::factory()->create();
        $film = Film::factory()->create();

        $country->films()->attach($film->id);

        $this->delete(route('admin.countries.destroy', $country))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
        ]);

        $this->assertDatabaseHas('countries', ['id' => $country->id]);
    }

}
