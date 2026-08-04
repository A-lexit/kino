<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::paginate(20);
        return view('genres.index', compact('genres'));
    }

    public function show($slug)
    {
        $genre = Genre::where('slug', $slug)->firstOrFail();
        $films = $genre->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('genres.show', compact('genre', 'films'));
    }

}
