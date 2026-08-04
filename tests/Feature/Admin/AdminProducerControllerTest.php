<?php
namespace Tests\Feature\Admin;

use App\Models\Film;
use App\Models\Producer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProducerControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Створюємо адміністратора за єдиним шаблоном
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_returns_view_with_producers(): void
    {
        Producer::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.producers.index'))
            ->assertStatus(200)
            ->assertViewHas('producers');
    }

    public function test_create_returns_successful_response(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.producers.create'))
            ->assertStatus(200);
    }

    public function test_store_creates_producer(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.producers.store'), [
                'name' => 'Тестовий Продюсер',
                'slug' => 'testovyi-producier',
            ])
            ->assertRedirect(route('admin.producers.index'))
            ->assertSessionHas('success', 'Продюсера додано');

        $this->assertDatabaseHas('producers', ['name' => 'Тестовий Продюсер']);
    }

    public function test_edit_returns_view_with_producer(): void
    {
        $producer = Producer::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.producers.edit', $producer->id))
            ->assertStatus(200)
            ->assertViewHas('producer');
    }

    public function test_update_updates_producer(): void
    {
        $producer = Producer::factory()->create(['name' => 'Старий Продюсер']);

        $this->actingAs($this->admin)
            ->put(route('admin.producers.update', $producer->id), [
                'name' => 'Оновлений Продюсер',
                'slug' => 'onovlenyi-producier',
            ])
            ->assertRedirect(route('admin.producers.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('producers', ['id' => $producer->id, 'name' => 'Оновлений Продюсер']);
    }

    public function test_destroy_deletes_producer_without_films(): void
    {
        $this->actingAs($this->admin);
        $producer = Producer::factory()->create();

        $this->delete(route('admin.producers.destroy', $producer))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('producers', ['id' => $producer->id]);
    }

    public function test_destroy_fails_when_producer_has_films(): void
    {
        $this->actingAs($this->admin);
        $producer = Producer::factory()->create();
        $film = Film::factory()->create(['author_id' => $this->admin->id]);

        // Надійно зв'язуємо продюсера з фільмом перед видаленням
        $producer->films()->attach($film->id);

        $this->delete(route('admin.producers.destroy', $producer))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_unauthenticated_user_cannot_access_producers(): void
    {
        $this->get(route('admin.producers.index'))
            ->assertRedirect(route('login'));
    }

}
