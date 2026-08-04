<?php

namespace App\Http\Controllers;

use App\Models\Actor;

class ActorController extends Controller
{
    public function index()
    {
        $actors = Actor::paginate(20);

        return view('actors.index', compact('actors'));
    }

    public function show($slug)
    {
        $actor = Actor::where('slug', $slug)->firstOrFail();

        $films = $actor->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('actors.show', compact('actor', 'films'));
    }

}
