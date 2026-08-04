<?php

namespace App\Http\Controllers;

use App\Models\Composer;

class ComposerController extends Controller
{
    public function index()
    {
        $composers = Composer::paginate(20);
        return view('composers.index', compact('composers'));
    }

    public function show($slug)
    {
        $composer = Composer::where('slug', $slug)->firstOrFail();
        $films = $composer->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('composers.show', compact('composer', 'films'));
    }

}
