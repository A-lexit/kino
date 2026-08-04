<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Quality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQualityControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_qualities(): void
    {
        Quality::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.qualities.index'))
            ->assertStatus(200)
            ->assertViewHas('qualities');
    }

    public function test_create_returns_successful_response(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.qualities.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_quality(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.qualities.store'), [
                'title' => 'Тестова якість',
                'slug'  => 'testova-yakist',
            ])
            ->assertRedirect(route('admin.qualities.index'))
            ->assertSessionHas('success', 'Якість додано');

        $this->assertDatabaseHas('qualities', ['title' => 'Тестова якість']);
    }

    public function test_edit_returns_view_with_quality(): void
    {
        $quality = Quality::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.qualities.edit', $quality->id))
            ->assertStatus(200)
            ->assertViewHas('quality');
    }

    public function test_update_updates_quality(): void
    {
        $quality = Quality::factory()->create(['title' => 'Стара якість']);

        $this->actingAs($this->admin)
            ->put(route('admin.qualities.update', $quality->id), [
                'title' => 'Оновлена якість',
                'slug'  => 'onovlena-yakist',
            ])
            ->assertRedirect(route('admin.qualities.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('qualities', ['id' => $quality->id, 'title' => 'Оновлена якість']);
    }

    public function test_destroy_deletes_quality_without_films(): void
    {
        $this->actingAs($this->admin);
        $quality = Quality::factory()->create();

        $this->delete(route('admin.qualities.destroy', $quality))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('qualities', ['id' => $quality->id]);
    }

    public function test_destroy_fails_when_quality_has_films(): void
    {
        $this->actingAs($this->admin);
        $quality = Quality::factory()->create();

        // Зв'язуємо якість із фільмом
        Film::factory()->create([
            'author_id'  => $this->admin->id,
            'quality_id' => $quality->id,
        ]);

        $this->delete(route('admin.qualities.destroy', $quality))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_qualities(): void
    {
        $this->get(route('admin.qualities.index'))
            ->assertRedirect(route('login'));
    }

}
