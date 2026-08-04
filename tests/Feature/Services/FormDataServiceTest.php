<?php
namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\Year;
use App\Models\Rating;
use App\Models\Duration;
use App\Models\Status;
use App\Models\Age;
use App\Models\Quality;
use App\Models\Season;
use App\Models\Composer;
use App\Models\Company;
use App\Models\Director;
use App\Models\Actor;
use App\Models\Producer;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Country;
use App\Models\Caption;
use App\Models\Selection;
use App\Services\FormDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FormDataServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FormDataService $formDataService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formDataService = new FormDataService();

        Cache::tags(['form_data'])->flush();
    }


    protected function tearDown(): void
    {
        Cache::tags(['form_data'])->flush();

        parent::tearDown();
    }


    public function test_get_form_data_returns_correct_structure_with_plucked_values(): void
    {
        $category = Category::create([
            'title' => 'Action',
            'slug' => 'action'
        ]);

        $year = Year::create([
            'title' => '2026',
            'slug' => '2026'
        ]);

        $rating = Rating::create([
            'title' => 'PG-13'
        ]);

        $duration = Duration::create([
            'title' => '120 min'
        ]);

        $status = Status::create([
            'title' => 'Released'
        ]);

        $age = Age::create([
            'title' => '16+'
        ]);

        $quality = Quality::create([
            'title' => 'FullHD'
        ]);

        $season = Season::create([
            'title' => 'Season 1'
        ]);

        $composer = Composer::create([
            'name' => 'Hans Zimmer'
        ]);

        $company = Company::create([
            'title' => 'Warner Bros'
        ]);

        $director = Director::create([
            'name' => 'Christopher Nolan'
        ]);

        $actor = Actor::create([
            'name' => 'Leonardo DiCaprio'
        ]);

        $producer = Producer::create([
            'name' => 'Emma Thomas'
        ]);

        $genre = Genre::create([
            'title' => 'Sci-Fi',
            'slug' => 'sci-fi'
        ]);

        $language = Language::create([
            'title' => 'English'
        ]);

        $country = Country::create([
            'title' => 'USA',
            'slug' => 'usa'
        ]);

        $caption = Caption::create([
            'title' => 'Ukrainian'
        ]);

        $selection = Selection::create([
            'title' => 'Top 100',
            'slug' => 'top-100'
        ]);

        $result = $this->formDataService->getFormData();

        $this->assertIsArray($result);

        $keys = [
            'categories',
            'years',
            'ratings',
            'durations',
            'statuses',
            'ages',
            'qualities',
            'seasons',
            'composers',
            'companies',
            'directors',
            'actors',
            'producers',
            'genres',
            'languages',
            'countries',
            'captions',
            'selections',
        ];

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $result);
        }

        $this->assertEquals(
            'Action',
            $result['categories'][$category->id]
        );

        $this->assertEquals(
            'Sci-Fi',
            $result['genres'][$genre->id]
        );

        $this->assertEquals(
            'Hans Zimmer',
            $result['composers'][$composer->id]
        );

        $this->assertEquals(
            'Christopher Nolan',
            $result['directors'][$director->id]
        );
    }


    public function test_get_form_data_caches_the_results_correctly(): void
    {
        $this->assertFalse(
            Cache::tags(['form_data'])->has('film_form_lists')
        );

        $this->formDataService->getFormData();

        $this->assertTrue(
            Cache::tags(['form_data'])->has('film_form_lists')
        );

        Category::create([
            'title' => 'New Category After Cache',
            'slug' => 'new-category-after-cache'
        ]);

        $secondCallResult = $this->formDataService->getFormData();

        $this->assertNotContains(
            'New Category After Cache',
            $secondCallResult['categories']
        );
    }

}
