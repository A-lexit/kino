<?php
namespace Tests\Feature\Admin;

use App\Models\Age;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAgeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Авторизація адміністратора перед кожним тестом
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_paginated_ages(): void
    {
        $this->actingAs($this->admin);
        Age::factory()->count(5)->create();

        $this->get(route('admin.ages.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.ages.index')
            ->assertViewHas('ages');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin);
        $this->get(route('admin.ages.create'))
            ->assertStatus(200)
            ->assertViewIs('admin.ages.create');
    }

    public function test_store_saves_age_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['title' => '12+'];

        $this->post(route('admin.ages.store'), $data)
            ->assertRedirect(route('admin.ages.index'))
            ->assertSessionHas('success', 'Вікову категорію додано');

        $this->assertDatabaseHas('ages', $data);
    }

    public function test_edit_returns_view_with_age(): void
    {
        $this->actingAs($this->admin);
        $age = Age::factory()->create();

        $this->get(route('admin.ages.edit', $age->id))
            ->assertStatus(200)
            ->assertViewIs('admin.ages.edit')
            ->assertViewHas('age', $age);
    }

    public function test_update_modifies_age_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $age = Age::factory()->create(['title' => '16+']);
        $newData = ['title' => '18+'];

        $this->put(route('admin.ages.update', $age->id), $newData)
            ->assertRedirect(route('admin.ages.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('ages', ['id' => $age->id, 'title' => '18+']);
    }

    public function test_destroy_deletes_age_when_no_films_are_linked(): void
    {
        $this->actingAs($this->admin);

        $age = Age::factory()->create();

        $this->delete(route('admin.ages.destroy', $age))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('ages', [
            'id' => $age->id,
        ]);
    }

    public function test_destroy_prevents_deletion_if_films_are_linked(): void
    {
        $this->actingAs($this->admin);

        $age = Age::factory()->create();

        Film::factory()->create([
            'age_id' => $age->id,
        ]);

        $this->delete(route('admin.ages.destroy', $age))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('ages', [
            'id' => $age->id,
        ]);
    }

}
