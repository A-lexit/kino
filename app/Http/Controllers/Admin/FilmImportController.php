<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\APIs\MovieApiSearchService;
use App\APIs\FilmImportService;
use App\Models\Film;

class FilmImportController extends Controller
{
    public function index()
    {
        $this->authorize('create', Film::class);

        return view('admin.films.import', [
            'movies' => [],
            'query' => '',
        ]);
    }

    public function search(Request $request)
    {
        $this->authorize('create', Film::class);

        $query = $request->input('query', '');
        $movies = [];

        if (!empty($query)) {
            $movies = app(MovieApiSearchService::class)->search($query);
        }

        return view('admin.films.import', compact('movies', 'query'));
    }

    public function store($id)
    {
        $this->authorize('create', Film::class);

        app(FilmImportService::class)->import($id);

        return redirect()->back()->with('success', 'Фільм імпортовано');
    }

    public function telegramPublish($id)
    {
        $film = Film::findOrFail($id);
        $this->authorize('update', $film);

        $film->update([
            'publish_status' => 'published',
        ]);

        return 'Опубліковано';
    }

}
