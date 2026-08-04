<?php

namespace App\Http\Controllers;

use App\Models\Director;

class DirectorController extends Controller
{
    public function index()
    {
        $directors = Director::paginate(20);
        return view('directors.index', compact('directors'));
    }

    public function show($slug)
    {
        $director = Director::where('slug', $slug)->firstOrFail();
        $films = $director->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('directors.show', compact('director', 'films'));
    }

}
