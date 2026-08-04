<?php
namespace Tests\Unit\Models\Menu;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\TestCase;

class MenuRelationsTest extends TestCase
{
    public function test_items_relation(): void
    {
        $menu = new Menu();

        $relation = $menu->items();

        $this->assertInstanceOf(
            HasMany::class,
            $relation
        );

        $this->assertInstanceOf(
            MenuItem::class,
            $relation->getRelated()
        );
    }


    public function test_items_relation_is_ordered_by_position(): void
    {
        $menu = new Menu();

        $relation = $menu->items();

        $this->assertStringContainsString(
            'order by',
            strtolower($relation->toSql())
        );

        $this->assertStringContainsString(
            'position',
            strtolower($relation->toSql())
        );
    }

}
