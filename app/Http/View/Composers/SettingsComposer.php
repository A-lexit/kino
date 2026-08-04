<?php

namespace App\Http\View\Composers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingsComposer
{

    public function compose(View $view): void
    {
        $view->with([
            'settings' => Cache::remember(
                'site_settings',
                now()->addDay(),
                fn () => Setting::first()
            ),

            'currentDate' => now('Europe/Kyiv'),
        ]);
    }

}
