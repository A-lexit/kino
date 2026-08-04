<?php

namespace App\Http\Controllers;

use App\Models\Language;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::paginate(20);
        return view('languages.index', compact('languages'));
    }

    public function show($slug)
    {
        $language = Language::where('slug', $slug)->firstOrFail();
        $films = $language->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('languages.show', compact('language', 'films'));
    }

}
