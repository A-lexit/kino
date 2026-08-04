<?php

namespace App\Http\Controllers;

use App\Models\Selection;

class SelectionController extends Controller
{
    public function index()
    {
        $selections = Selection::paginate(20);
        return view('selections.index', compact('selections'));
    }

    public function show($slug)
    {
        $selection = Selection::where('slug', $slug)->firstOrFail();
        $films = $selection->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('selections.show', compact('selection', 'films'));
    }

}
