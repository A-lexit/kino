<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminMenuControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = \App\Models\User::factory()->admin()->create();
    }

    public function test_admin_can_open_menu_edit_page(): void
    {
        Category::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.menu.edit'))
            ->assertOk()
            ->assertViewIs('admin.menu.edit')
            ->assertViewHasAll([
                'allCategories',
                'staticPages',
                'menu',
            ]);
    }

    public function test_edit_creates_menu_if_it_does_not_exist(): void
    {
        $this->assertDatabaseCount('menus', 0);

        $this->actingAs($this->admin)
            ->get(route('admin.menu.edit'))
            ->assertOk();

        $this->assertDatabaseHas('menus', [
            'title' => 'Головне меню',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_menu_items(): void
    {
        $category = Category::factory()->create();

        $menu = Menu::factory()->create([
            'title' => 'Головне меню',
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.menu.update'), [
                'items' => [
                    'category:' . $category->id,
                    'static:actors',
                ],
            ])
            ->assertRedirect(route('admin.menu.edit'))
            ->assertSessionHas('success', 'Меню оновлено');

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

    public function test_update_replaces_existing_menu_items(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $menu = Menu::factory()->create([
            'is_active' => true,
        ]);

        $oldItem = $menu->items()->create([
            'type' => 'category',
            'category_id' => $category1->id,
            'position' => 0,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.menu.update'), [
                'items' => [
                    'category:' . $category2->id,
                ],
            ])
            ->assertRedirect(route('admin.menu.edit'));

        $this->assertDatabaseMissing('menu_items', [
            'id' => $oldItem->id,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'type' => 'category',
            'category_id' => $category2->id,
            'position' => 0,
        ]);
    }

    public function test_update_activates_inactive_menu(): void
    {
        $menu = Menu::factory()->create([
            'is_active' => false,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.menu.update'), [
                'items' => [
                    'static:actors',
                ],
            ])
            ->assertRedirect(route('admin.menu.edit'));

        $this->assertTrue($menu->fresh()->is_active);
    }

    public function test_update_clears_active_menu_cache(): void
    {
        Cache::put('active_menu', 'cached');

        $menu = Menu::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.menu.update'), [
                'items' => [
                    'static:actors',
                ],
            ])
            ->assertRedirect(route('admin.menu.edit'));

        $this->assertNull(Cache::get('active_menu'));
    }

    public function test_update_requires_items(): void
    {
        Menu::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.menu.update'), [])
            ->assertSessionHasErrors('items');
    }

    public function test_guest_cannot_access_menu_edit_page(): void
    {
        $this->get(route('admin.menu.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_cannot_update_menu(): void
    {
        $this->put(route('admin.menu.update'), [
            'items' => [
                'static:actors',
            ],
        ])
            ->assertRedirect(route('login'));
    }
}
