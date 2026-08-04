<?php

namespace App\Http\Controllers;

use App\Models\Film;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            's' => 'required',
        ]);

        $s = $request->s;

        $films = Film::published()
            ->where(function ($query) use ($s) {
                $query->where('title', 'LIKE', "%{$s}%")
                    ->orWhere('origin_title', 'LIKE', "%{$s}%");
            })
            ->with('category')
            ->paginate(30);

        return view('search', compact('films', 's'));
    }

}
