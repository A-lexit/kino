<?php

namespace App\Http\Controllers;

use App\Models\Producer;

class ProducerController extends Controller
{
    public function index()
    {
        $producers = Producer::paginate(20);
        return view('producers.index', compact('producers'));
    }

    public function show($slug)
    {
        $producer = Producer::where('slug', $slug)->firstOrFail();
        $films = $producer->films()
            ->with('category')
            ->latest('id')
            ->paginate(20);

        return view('producers.show', compact('producer', 'films'));
    }

}
