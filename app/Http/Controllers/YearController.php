<?php

namespace App\Http\Controllers;

use App\Models\Year;

class YearController extends Controller
{
    public function index()
    {
        $years = Year::paginate(20);
        return view('years.index', compact('years'));
    }

    public function show($slug)
    {
        $year = Year::where('slug', $slug)->firstOrFail();
        $films = $year->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('years.show', compact('year', 'films'));
    }

}
