<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Menu;

class MenuComposer
{
    public function compose(View $view)
    {
        $menu = Cache::remember('active_menu', 1800, function () {
            return Menu::with('items.category')->where('is_active', true)->first();
        });

        $menuItems = $menu ? $menu->resolvedItems() : [];

        $view->with('menuItems', $menuItems);
    }

}
