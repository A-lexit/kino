<?php

namespace App\Http\Controllers;

use App\Models\Country;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::paginate(20);
        return view('countries.index', compact('countries'));
    }

    public function show($slug)
    {
        $country = Country::where('slug', $slug)->firstOrFail();
        $films = $country->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('countries.show', compact('country', 'films'));
    }

}
