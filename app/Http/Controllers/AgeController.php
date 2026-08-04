<?php

namespace App\Http\Controllers;

use App\Models\Age;

class AgeController extends Controller
{
    public function index()
    {
        $ages = Age::paginate(20);
        return view('ages.index', compact('ages'));
    }

    public function show($slug)
    {
        $age = Age::where('slug', $slug)->firstOrFail();
        $films = $age->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('ages.show', compact('age', 'films'));
    }
}
