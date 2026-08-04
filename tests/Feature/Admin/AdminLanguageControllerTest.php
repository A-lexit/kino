<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Створюємо адміністратора для тестів
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_languages(): void
    {
        Language::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.languages.index'))
            ->assertStatus(200)
            ->assertViewHas('languages');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.languages.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_language_and_redirects(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.languages.store'), [
                'title' => 'Ukrainian',
                'slug'  => 'ukrainian',
            ])
            ->assertRedirect(route('admin.languages.index'))
            ->assertSessionHas('success', 'Мову додано');

        $this->assertDatabaseHas('languages', ['title' => 'Ukrainian']);
    }

    public function test_edit_returns_view_with_language(): void
    {
        $language = Language::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.languages.edit', $language->id))
            ->assertStatus(200)
            ->assertViewHas('language');
    }

    public function test_update_modifies_language_and_redirects(): void
    {
        $language = Language::factory()->create(['title' => 'Old Title']);

        $this->actingAs($this->admin)
            ->put(route('admin.languages.update', $language->id), [
                'title' => 'English',
                'slug'  => 'english',
            ])
            ->assertRedirect(route('admin.languages.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('languages', ['id' => $language->id, 'title' => 'English']);
    }

    public function test_destroy_prevents_deletion_if_films_exist(): void
    {
        $this->actingAs($this->admin);
        $language = Language::factory()->create();
        $film = Film::factory()->create(['author_id' => $this->admin->id]);

        $language->films()->attach($film->id);

        $this->delete(route('admin.languages.destroy', $language))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
        ]);
    }

    public function test_destroy_deletes_language_if_no_films_exist(): void
    {
        $language = Language::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.languages.destroy', $language))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('languages', [
            'id' => $language->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_languages(): void
    {
        $this->get(route('admin.languages.index'))
            ->assertRedirect(route('login'));
    }

}
