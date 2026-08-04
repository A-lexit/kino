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
    public function create()
    {
        $allCategories = Category::all();
        $allMenus = Menu::with('items.category')->get();
        $staticPages = MenuItem::STATIC_PAGES;

        return view('admin.menu.create', compact('allCategories', 'allMenus', 'staticPages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'items' => 'required|array|min:1',
        ]);

        $menu = Menu::create(['title' => $request->input('title'), 'is_active' => false]);

        // $request->items — масив рядків типу "category:5" або "static:actors",
        // порядок у масиві = порядок вибору в select2 = порядок у меню
        foreach ($request->input('items') as $position => $value) {
            [$type, $key] = explode(':', $value, 2);

            $menu->items()->create([
                'type' => $type,
                'category_id' => $type === 'category' ? $key : null,
                'static_key' => $type === 'static' ? $key : null,
                'position' => $position,
            ]);
        }

        return redirect()->route('admin.menu.create')->with('success', 'Меню створено');
    }

    public function activateMenu(Request $request)
    {
        $menuId = $request->input('menu_id');

        Menu::where('is_active', true)->update(['is_active' => false]);

        $menu = Menu::findOrFail($menuId);
        $menu->is_active = true;
        $menu->save();

        Cache::forget('active_menu');

        return redirect()->route('admin.menu.create')->with('success', 'Меню активовано');
    }

}
