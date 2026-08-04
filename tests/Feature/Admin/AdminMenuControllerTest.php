<?php
namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminMenuControllerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_create_returns_view_with_data(): void
    {
        Category::factory()->count(3)->create();
        Menu::factory()->count(2)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.menu.create'))
            ->assertOk()
            ->assertViewIs('admin.menu.create')
            ->assertViewHasAll([
                'allCategories',
                'allMenus',
                'staticPages',
            ]);
    }

    public function test_store_creates_menu_with_items(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.menu.store'), [
                'title' => 'Main Navigation',
                'items' => [
                    'category:' . $category->id,
                    'static:actors',
                ],
            ])
            ->assertRedirect(route('admin.menu.create'))
            ->assertSessionHas('success', 'Меню створено');

        $menu = Menu::where('title', 'Main Navigation')->first();

        $this->assertNotNull($menu);

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'type' => 'category',
            'category_id' => $category->id,
            'position' => 0,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'type' => 'static',
            'static_key' => 'actors',
            'position' => 1,
        ]);
    }

    public function test_activate_menu_updates_status_and_clears_cache(): void
    {
        Cache::put('active_menu', 'cached');

        $activeMenu = Menu::factory()->create([
            'is_active' => true,
        ]);

        $newMenu = Menu::factory()->create([
            'is_active' => false,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.menu.activate'), [
                'menu_id' => $newMenu->id,
            ])
            ->assertRedirect(route('admin.menu.create'))
            ->assertSessionHas('success', 'Меню активовано');

        $this->assertFalse($activeMenu->fresh()->is_active);
        $this->assertTrue($newMenu->fresh()->is_active);

        $this->assertNull(Cache::get('active_menu'));
    }

    public function test_guest_cannot_access_menu_management(): void
    {
        $this->get(route('admin.menu.create'))
            ->assertRedirect(route('login'));

        $this->post(route('admin.menu.store'), [])
            ->assertRedirect(route('login'));

        $this->post(route('admin.menu.activate'), [])
            ->assertRedirect(route('login'));
    }

}
