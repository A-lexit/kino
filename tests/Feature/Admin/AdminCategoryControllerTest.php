<?php
namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Використовуємо фабрику користувача для авторизації
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_categories(): void
    {
        $this->actingAs($this->admin);
        Category::factory()->count(3)->create();

        $this->get(route('admin.categories.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.categories.index')
            ->assertViewHas('categories');
    }

    public function test_store_saves_category_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['title' => 'Adventure'];

        $this->post(route('admin.categories.store'), $data)
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', 'Категорію додано');

        $this->assertDatabaseHas('categories', $data);
    }

    public function test_update_modifies_category_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $category = Category::factory()->create(['title' => 'Old Title']);
        $newData = ['title' => 'New Title'];

        $this->put(route('admin.categories.update', $category->id), $newData)
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'title' => 'New Title']);
    }

    public function test_destroy_deletes_category_if_no_films_linked(): void
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();

        $this->delete(route('admin.categories.destroy', $category))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_destroy_prevents_deletion_if_films_exist(): void
    {
        $this->actingAs($this->admin);

        $category = Category::factory()->create();

        Film::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->delete(route('admin.categories.destroy', $category))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

}
