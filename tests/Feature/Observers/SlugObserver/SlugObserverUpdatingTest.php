<?php
namespace Tests\Feature\Observers\SlugObserver;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugObserverUpdatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_slug_when_slug_is_null_during_update(): void
    {
        $category = Category::factory()->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        $category->update([
            'title' => 'New Title',
            'slug' => null,
        ]);

        $this->assertSame(
            'new-title',
            $category->fresh()->slug
        );
    }


    public function test_generates_slug_when_slug_is_empty_string_during_update(): void
    {
        $category = Category::factory()->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        $category->update([
            'title' => 'Avatar 2',
            'slug' => '',
        ]);

        $this->assertSame(
            'avatar-2',
            $category->fresh()->slug
        );
    }


    public function test_does_not_change_existing_slug_when_title_changes(): void
    {
        $category = Category::factory()->create([
            'title' => 'Old Title',
            'slug' => 'my-custom-slug',
        ]);

        $category->update([
            'title' => 'Completely New Title',
        ]);

        $this->assertSame(
            'my-custom-slug',
            $category->fresh()->slug
        );
    }

}
