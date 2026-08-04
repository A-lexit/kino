<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $films = $category->films()
            ->published()
            ->with('category')
            ->latest('id')
            ->paginate(40);

        return view('categories.show', compact('category', 'films'));
    }

}
