<?php
namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_find_by_slug_returns_category(): void
    {
        $category = Category::factory()->create([
            'slug' => 'films',
        ]);

        $result = Category::findBySlug('films');

        $this->assertInstanceOf(Category::class, $result);
        $this->assertEquals($category->id, $result->id);
    }


    public function test_scope_find_by_slug_throws_exception_when_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        Category::findBySlug('unknown-category');
    }


    public function test_is_series_returns_true_for_series_categories(): void
    {
        $serial = Category::factory()->create([
            'slug' => 'seriali',
        ]);

        $multserial = Category::factory()->create([
            'slug' => 'multseriali',
        ]);

        $this->assertTrue($serial->isSeries());
        $this->assertTrue($multserial->isSeries());
    }


    public function test_is_series_returns_false_for_regular_category(): void
    {
        $category = Category::factory()->create([
            'slug' => 'films',
        ]);

        $this->assertFalse($category->isSeries());
    }

}
