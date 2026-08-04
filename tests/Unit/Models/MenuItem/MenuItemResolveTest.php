<?php
namespace Tests\Unit\Models\MenuItem;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemResolveTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_returns_category_menu_item(): void
    {
        $category = Category::factory()->create([
            'title' => 'Бойовики',
            'slug' => 'boyovyky',
        ]);

        $item = new MenuItem([
            'type' => 'category',
        ]);

        $item->setRelation('category', $category);

        $result = $item->resolve();

        $this->assertSame('Бойовики', $result['name']);

        $this->assertSame(
            route('categories.show', ['slug' => 'boyovyky']),
            $result['url']
        );

        $this->assertSame(
            [
                'category/boyovyky',
                'boyovyky/*',
            ],
            $result['is_patterns']
        );
    }


    public function test_resolve_returns_null_when_category_is_missing(): void
    {
        $item = new MenuItem([
            'type' => 'category',
        ]);

        $item->setRelation('category', null);

        $this->assertNull(
            $item->resolve()
        );
    }


    public function test_resolve_returns_static_home_item(): void
    {
        $item = new MenuItem([
            'type' => 'static',
            'static_key' => 'home',
        ]);

        $result = $item->resolve();

        $this->assertSame(
            'Головна',
            $result['name']
        );

        $this->assertSame(
            route('home'),
            $result['url']
        );

        $this->assertSame(
            ['/'],
            $result['is_patterns']
        );
    }


    public function test_resolve_returns_static_page_item(): void
    {
        $item = new MenuItem([
            'type' => 'static',
            'static_key' => 'actors',
        ]);

        $result = $item->resolve();

        $this->assertSame(
            'Актори',
            $result['name']
        );

        $this->assertSame(
            route('actors.index'),
            $result['url']
        );

        $this->assertSame(
            [
                'actors',
                'actors/*',
            ],
            $result['is_patterns']
        );
    }


    public function test_resolve_returns_null_for_unknown_static_page(): void
    {
        $item = new MenuItem([
            'type' => 'static',
            'static_key' => 'unknown',
        ]);

        $this->assertNull(
            $item->resolve()
        );
    }


    public function test_resolve_returns_null_for_unknown_type(): void
    {
        $item = new MenuItem([
            'type' => 'unknown',
        ]);

        $this->assertNull(
            $item->resolve()
        );
    }

}
