<?php
namespace Tests\Unit\Models\MenuItem;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemTitleTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_title_returns_category_title(): void
    {
        $category = Category::factory()->create([
            'title' => 'Бойовики',
        ]);

        $item = new MenuItem([
            'type' => 'category',
        ]);

        $item->setRelation('category', $category);

        $this->assertSame(
            'Бойовики',
            $item->getTitle()
        );
    }


    public function test_get_title_returns_deleted_category_text_when_category_is_missing(): void
    {
        $item = new MenuItem([
            'type' => 'category',
        ]);

        $item->setRelation('category', null);

        $this->assertSame(
            'Категорія видалена',
            $item->getTitle()
        );
    }


    public function test_get_title_returns_static_page_label(): void
    {
        $item = new MenuItem([
            'type' => 'static',
            'static_key' => 'home',
        ]);

        $this->assertSame(
            'Головна',
            $item->getTitle()
        );
    }


    public function test_get_title_returns_static_key_when_page_is_unknown(): void
    {
        $item = new MenuItem([
            'type' => 'static',
            'static_key' => 'unknown-page',
        ]);

        $this->assertSame(
            'unknown-page',
            $item->getTitle()
        );
    }

}
