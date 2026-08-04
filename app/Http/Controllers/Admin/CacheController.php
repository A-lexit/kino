<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;

class CacheController extends Controller
{
    public function clear()
    {
        Artisan::call('cache:clear');
        return redirect()->back()->with('message', 'Кеш очищено');
    }
}
