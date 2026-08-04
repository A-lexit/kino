<?php
namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminYearControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    /**
     * БЛОК 1: ТЕСТИ БЕЗПЕКИ (МАСКУВАННЯ АДМІНКИ ЧЕРЕЗ 404)
     */

    public function test_guest_cannot_access_years_routes(): void
    {
        $this->get(route('admin.years.index'))->assertRedirect(route('login'));
        $this->get(route('admin.years.create'))->assertRedirect(route('login'));
        $this->post(route('admin.years.store'))->assertRedirect(route('login'));
        $this->get(route('admin.years.edit', 1))->assertRedirect(route('login'));
        $this->put(route('admin.years.update', 1))->assertRedirect(route('login'));
        $this->delete(route('admin.years.destroy', 1))->assertRedirect(route('login'));
    }

    public function test_non_admin_user_cannot_access_years_routes(): void
    {
        $regularUser = User::factory()->create(['is_admin' => 0, 'is_banned' => 0]);
        $this->actingAs($regularUser);

        $this->get(route('admin.years.index'))->assertStatus(404);
        $this->get(route('admin.years.create'))->assertStatus(404);
        $this->post(route('admin.years.store'))->assertStatus(404);
        $this->get(route('admin.years.edit', 1))->assertStatus(404);
        $this->put(route('admin.years.update', 1))->assertStatus(404);
        $this->delete(route('admin.years.destroy', 1))->assertStatus(404);
    }

    /**
     * БЛОК 2: ОСНОВНИЙ ФУНКЦІОНАЛ (HAPPY PATHS)
     */

    public function test_index_displays_years_list_with_pagination(): void
    {
        $this->actingAs($this->admin);
        Year::factory()->count(5)->create();

        $response = $this->get(route('admin.years.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.years.index');
        $response->assertViewHas('years');
    }

    public function test_create_returns_view(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.years.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.years.create');
    }

    public function test_store_creates_year_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $yearData = ['title' => '2026'];

        $response = $this->post(route('admin.years.store'), $yearData);

        $response->assertRedirect(route('admin.years.index'));
        $response->assertSessionHas('success', 'Рік додано');
        $this->assertDatabaseHas('years', ['title' => '2026']);
    }

    public function test_edit_returns_view_for_existing_year(): void
    {
        $this->actingAs($this->admin);
        $year = Year::factory()->create();

        $response = $this->get(route('admin.years.edit', $year->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.years.edit');
        $response->assertViewHas('year');
    }

    public function test_update_modifies_year_data_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $year = Year::factory()->create(['title' => '2020']);

        $response = $this->put(route('admin.years.update', $year->id), [
            'title' => '2025'
        ]);

        $response->assertRedirect(route('admin.years.index'));
        $response->assertSessionHas('success', 'Зміни збережено');
        $this->assertDatabaseHas('years', [
            'id' => $year->id,
            'title' => '2025'
        ]);
    }

    public function test_destroy_deletes_year_if_it_has_no_films(): void
    {
        $this->actingAs($this->admin);
        $year = Year::factory()->create();

        $this->delete(route('admin.years.destroy', $year))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('years', ['id' => $year->id]);
    }

    /**
     * БЛОК 3: ВАЛІДАЦІЯ ТА ОБМЕЖЕННЯ (EDGE CASES)
     */

    public function test_store_fails_if_title_validation_rules_violated(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('admin.years.store'), ['title' => '']);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_destroy_fails_if_year_has_films(): void
    {
        $this->actingAs($this->admin);
        $year = Year::factory()->create();

        // Створюємо зв'язаний фільм (переконайтеся, що фабрика Film підтримує year_id або використовує зв'язок)
        \App\Models\Film::factory()->create(['year_id' => $year->id]);

        $this->delete(route('admin.years.destroy', $year))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('years', ['id' => $year->id]);
    }

    public function test_methods_return_404_for_non_existent_year(): void
    {
        $this->actingAs($this->admin);
        $nonExistentId = 99999;

        $this->get(route('admin.years.edit', $nonExistentId))->assertStatus(404);
        $this->put(route('admin.years.update', $nonExistentId), ['title' => '2026'])->assertStatus(404);
        $this->delete(route('admin.years.destroy', $nonExistentId))->assertStatus(404);
    }

}
