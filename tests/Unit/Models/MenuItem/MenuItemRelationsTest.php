<?php
namespace Tests\Unit\Models\MenuItem;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class MenuItemRelationsTest extends TestCase
{
    private MenuItem $menuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->menuItem = new MenuItem();
    }


    public function test_menu_relation(): void
    {
        $relation = $this->menuItem->menu();

        $this->assertInstanceOf(
            BelongsTo::class,
            $relation
        );

        $this->assertInstanceOf(
            Menu::class,
            $relation->getRelated()
        );
    }


    public function test_category_relation(): void
    {
        $relation = $this->menuItem->category();

        $this->assertInstanceOf(
            BelongsTo::class,
            $relation
        );

        $this->assertInstanceOf(
            Category::class,
            $relation->getRelated()
        );
    }

}
