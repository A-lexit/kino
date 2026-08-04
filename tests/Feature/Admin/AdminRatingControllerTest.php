<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRatingControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_ratings(): void
    {
        Rating::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.ratings.index'))
            ->assertStatus(200)
            ->assertViewHas('ratings');
    }

    public function test_create_returns_successful_response(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.ratings.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_rating(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.ratings.store'), [
                'title' => '95', // Передаємо як рядок для TitleRequest
                'slug'  => '95',
            ])
            ->assertRedirect(route('admin.ratings.index'))
            ->assertSessionHas('success', 'Рейтинг додано');

        $this->assertDatabaseHas('ratings', ['title' => 95]);
    }

    public function test_edit_returns_view_with_rating(): void
    {
        $rating = Rating::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.ratings.edit', $rating->id))
            ->assertStatus(200)
            ->assertViewHas('rating');
    }

    public function test_update_updates_rating(): void
    {
        $rating = Rating::factory()->create(['title' => 80]);

        $this->actingAs($this->admin)
            ->put(route('admin.ratings.update', $rating->id), [
                'title' => '99', // Передаємо як рядок для TitleRequest
                'slug'  => '99',
            ])
            ->assertRedirect(route('admin.ratings.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('ratings', ['id' => $rating->id, 'title' => 99]);
    }

    public function test_destroy_deletes_rating_without_films(): void
    {
        $this->actingAs($this->admin);
        $rating = Rating::factory()->create();

        $this->delete(route('admin.ratings.destroy', $rating))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('ratings', ['id' => $rating->id]);
    }

    public function test_destroy_fails_when_rating_has_films(): void
    {
        $this->actingAs($this->admin);
        $rating = Rating::factory()->create();

        Film::factory()->create([
            'author_id' => $this->admin->id,
            'rating_id' => $rating->id,
        ]);

        $this->delete(route('admin.ratings.destroy', $rating))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_ratings(): void
    {
        $this->get(route('admin.ratings.index'))
            ->assertRedirect(route('login'));
    }

}
