<?php
namespace Tests\Feature\Front;

use App\Models\Company;
use App\Models\Film;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_companies_list(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->get(route('companies.index'));

        $response->assertOk();
        $response->assertViewIs('companies.index');
        $response->assertViewHas('companies');

        $this->assertCount(
            3,
            $response->viewData('companies')
        );
    }


    public function test_show_displays_company_with_films(): void
    {
        $company = Company::factory()->create([
            'slug' => 'test-company',
        ]);

        Film::factory()->count(3)->create()
            ->each(fn ($film) => $film->companies()->attach($company));

        $response = $this->get(
            route('companies.show', $company->slug)
        );

        $response->assertOk();
        $response->assertViewIs('companies.show');
        $response->assertViewHasAll([
            'company',
            'films'
        ]);

        $this->assertEquals(
            $company->id,
            $response->viewData('company')->id
        );

        $this->assertCount(
            3,
            $response->viewData('films')->items()
        );
    }


    public function test_show_returns_404_for_unknown_company(): void
    {
        $response = $this->get(
            route('companies.show', 'unknown')
        );

        $response->assertNotFound();
    }


    public function test_show_paginates_films_by_20(): void
    {
        $company = Company::factory()->create([
            'slug' => 'big-company',
        ]);

        Film::factory()->count(25)->create()
            ->each(fn ($film) => $film->companies()->attach($company));

        $response = $this->get(
            route('companies.show', $company->slug)
        );

        $response->assertOk();

        $this->assertCount(
            20,
            $response->viewData('films')->items()
        );
    }

}
