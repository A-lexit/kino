<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function edit()
    {
        $this->authorize('viewAny', Menu::class);

        $allCategories = Category::all();
        $staticPages = MenuItem::STATIC_PAGES;

        $menu = Menu::with('items.category')->first();

        if (!$menu) {
            $this->authorize('create', Menu::class);

            $menu = Menu::create([
                'title' => 'Головне меню',
                'is_active' => true,
            ]);
        }

        return view(
            'admin.menu.edit',
            compact('allCategories', 'staticPages', 'menu')
        );
    }

    public function update(Request $request)
    {
        $menu = Menu::first()
            ?? Menu::create([
                'title' => 'Головне меню',
                'is_active' => true,
            ]);

        $this->authorize('update', $menu);

        $request->validate([
            'items' => 'required|array|min:1',
        ]);

        $menu->items()->delete();

        foreach ($request->input('items') as $position => $value) {
            [$type, $key] = explode(':', $value, 2);

            $menu->items()->create([
                'type' => $type,
                'category_id' => $type === 'category' ? $key : null,
                'static_key' => $type === 'static' ? $key : null,
                'position' => $position,
            ]);
        }

        if (!$menu->is_active) {
            $menu->update([
                'is_active' => true,
            ]);
        }

        Cache::forget('active_menu');

        return redirect()
            ->route('admin.menu.edit')
            ->with('success', 'Меню оновлено');
    }
}
