<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::paginate(20);
        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create');
    }

    public function store(TitleRequest $request)
    {
        Genre::create($request->all());
        return redirect()->route('admin.genres.index')->with('success', 'Жанр додано');
    }

    public function edit(string $id)
    {
        $genre = Genre::findOrFail($id);
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $genre = Genre::findOrFail($id);
        $genre->update($request->all());
        return redirect()->route('admin.genres.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Genre $genre)
    {
        if ($genre->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити жанр «{$genre->title}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $genre->delete();

        return response()->json([
            'success' => true,
            'message' => 'Жанр успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:genres,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $genre = Genre::find($id);
            if ($genre && $genre->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Жанр «{$genre->title}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Genre::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані жанри успішно видалено.'
        ]);
    }

}
