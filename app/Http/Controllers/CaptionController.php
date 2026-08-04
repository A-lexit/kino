<?php

namespace App\Http\Controllers;

use App\Models\Caption;

class CaptionController extends Controller
{
    public function index()
    {
        $captions = Caption::paginate(20);
        return view('captions.index', compact('captions'));
    }

    public function show($slug)
    {
        $caption = Caption::where('slug', $slug)->firstOrFail();
        $films = $caption->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('captions.show', compact('caption', 'films'));
    }

}
