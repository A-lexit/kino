<?php

namespace App\Http\Controllers;

use App\Models\Rating;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::paginate(20);
        return view('ratings.index', compact('ratings'));
    }

    public function show($slug)
    {
        $rating = Rating::where('slug', $slug)->firstOrFail();
        $films = $rating->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('ratings.show', compact('rating', 'films'));
    }

}
