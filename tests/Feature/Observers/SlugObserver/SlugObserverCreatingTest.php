<?php
namespace Tests\Feature\Observers\SlugObserver;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugObserverCreatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_slug_from_title_when_slug_is_empty(): void
    {
        $category = Category::factory()->create([
            'title' => 'The Matrix',
            'slug' => null,
        ]);

        $this->assertSame(
            'the-matrix',
            $category->slug
        );
    }


    public function test_does_not_override_existing_slug(): void
    {
        $category = Category::factory()->create([
            'title' => 'The Matrix',
            'slug' => 'my-custom-slug',
        ]);

        $this->assertSame(
            'my-custom-slug',
            $category->slug
        );
    }


    public function test_generates_slug_when_slug_is_empty_string(): void
    {
        $category = Category::factory()->create([
            'title' => 'Avatar 2',
            'slug' => '',
        ]);

        $this->assertSame(
            'avatar-2',
            $category->slug
        );
    }

}
