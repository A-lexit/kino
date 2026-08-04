<?php
namespace Tests\Feature\Admin;

use App\Models\Duration;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDurationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_durations(): void
    {
        $this->actingAs($this->admin);
        Duration::factory()->count(3)->create();

        $this->get(route('admin.durations.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.durations.index')
            ->assertViewHas('durations');
    }

    public function test_store_saves_duration_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['title' => '100 хв.'];

        $this->post(route('admin.durations.store'), $data)
            ->assertRedirect(route('admin.durations.index'))
            ->assertSessionHas('success', 'Тривалість додано');

        $this->assertDatabaseHas('durations', $data);
    }

    public function test_update_modifies_duration_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $duration = Duration::factory()->create(['title' => '80 хв.']);
        $newData = ['title' => '85 хв.'];

        $this->put(route('admin.durations.update', $duration->id), $newData)
            ->assertRedirect(route('admin.durations.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('durations', ['id' => $duration->id, 'title' => '85 хв.']);
    }

    public function test_destroy_deletes_duration_when_no_films_linked(): void
    {
        $this->actingAs($this->admin);
        $duration = Duration::factory()->create();

        $this->delete(route('admin.durations.destroy', $duration))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('durations', [
            'id' => $duration->id,
        ]);
    }

    public function test_destroy_prevents_deletion_if_films_linked(): void
    {
        $this->actingAs($this->admin);
        $duration = Duration::factory()->create();
        // Створюємо фільм, що прив'язаний до тривалості
        Film::factory()->create(['duration_id' => $duration->id]);

        $this->delete(route('admin.durations.destroy', $duration))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('durations', [
            'id' => $duration->id,
        ]);

        $this->assertDatabaseHas('durations', ['id' => $duration->id]);
    }

}
