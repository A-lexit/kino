<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Models\Director;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index()
    {
        $directors = Director::paginate(20);

        return view('admin.directors.index', compact('directors'));
    }

    public function create()
    {
        return view('admin.directors.create');
    }


    public function store(NameRequest $request)
    {
        Director::create($request->all());

        return redirect()->route('admin.directors.index')->with('success', 'Тег добавлен');
    }


    public function edit(string $id)
    {
        $director = Director::findOrFail($id);

        return view('admin.directors.edit', compact('director'));
    }


    public function update(NameRequest $request, string $id)
    {
        $director = Director::findOrFail($id);
        $director->update($request->all());

        return redirect()->route('admin.directors.index')->with('success', 'Зміни збережені');
    }


// Одиночне видалення через AJAX
    public function destroy(Director $director)
    {
        if ($director->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити режисера «{$director->name}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $director->delete();

        return response()->json([
            'success' => true,
            'message' => 'Режисера успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:directors,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $director = Director::find($id);
            if ($director && $director->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Режисер «{$director->name}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Director::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибраних режисерів успішно видалено.'
        ]);
    }

}
