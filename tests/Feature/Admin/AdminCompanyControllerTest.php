<?php
namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_index_displays_companies(): void
    {
        $this->actingAs($this->admin);
        Company::factory()->count(3)->create();

        $this->get(route('admin.companies.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.companies.index')
            ->assertViewHas('companies');
    }

    public function test_store_saves_company_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $data = ['title' => 'Warner Bros'];

        $this->post(route('admin.companies.store'), $data)
            ->assertRedirect(route('admin.companies.index'))
            ->assertSessionHas('success', 'Компанію додано');

        $this->assertDatabaseHas('companies', $data);
    }

    public function test_update_modifies_company_and_redirects(): void
    {
        $this->actingAs($this->admin);
        $company = Company::factory()->create(['title' => 'Old Studio']);
        $newData = ['title' => 'New Studio'];

        $this->put(route('admin.companies.update', $company->id), $newData)
            ->assertRedirect(route('admin.companies.index'))
            ->assertSessionHas('success', 'Зміни збережені');

        $this->assertDatabaseHas('companies', ['id' => $company->id, 'title' => 'New Studio']);
    }

    public function test_destroy_deletes_company_when_no_films_linked(): void
    {
        $this->actingAs($this->admin);
        $company = Company::factory()->create();

        $this->delete(route('admin.companies.destroy', $company))
            ->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
        ]);
    }

    public function test_destroy_prevents_deletion_if_films_linked(): void
    {
        $this->actingAs($this->admin);
        $company = Company::factory()->create();
        $film = Film::factory()->create();

        // Зв'язок belongsToMany потребує attach
        $company->films()->attach($film->id);

        $this->delete(route('admin.companies.destroy', $company))
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
        ]);

        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }

}
