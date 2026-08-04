<?php
namespace Tests\Unit\View\Composers;

use App\Http\View\Composers\MenuComposer;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class MenuComposerTest extends TestCase
{
    use RefreshDatabase;

    public function test_compose_binds_menu_items_to_view(): void
    {
        Cache::flush();

        $menu = Menu::factory()->create([
            'is_active' => true,
        ]);

        $expectedItems = [
            ['title' => 'Films'],
            ['title' => 'Series'],
        ];

        $menu = Mockery::mock($menu)->makePartial();

        $menu->shouldReceive('resolvedItems')
            ->once()
            ->andReturn($expectedItems);

        Cache::put('active_menu', $menu, 1800);

        $view = Mockery::mock(View::class);

        $view->shouldReceive('with')
            ->once()
            ->with('menuItems', $expectedItems);

        (new MenuComposer())->compose($view);

        $this->assertTrue(true);
    }


    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

}
