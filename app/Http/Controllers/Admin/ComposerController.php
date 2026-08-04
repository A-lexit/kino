<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Models\Composer;
use Illuminate\Http\Request;

class ComposerController extends Controller
{
    public function index()
    {
        $composers = Composer::paginate(20);
        return view('admin.composers.index', compact('composers'));
    }

    public function create()
    {
        return view('admin.composers.create');
    }

    public function store(NameRequest $request)
    {
        Composer::create($request->all());
        return redirect()->route('admin.composers.index')->with('success', 'Композитора додано');
    }

    public function edit($id)
    {
        $composer = Composer::findOrFail($id);
        return view('admin.composers.edit', compact('composer'));
    }

    public function update(NameRequest $request, $id)
    {
        $composer = Composer::findOrFail($id);
        $composer->update($request->all());
        return redirect()->route('admin.composers.index')->with('success', 'Зміни збережені');
    }

// Одиночне видалення через AJAX
    public function destroy(Composer $composer)
    {
        if ($composer->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити композитора «{$composer->name}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $composer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Композитора успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:composers,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $composer = Composer::find($id);
            if ($composer && $composer->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Композитор «{$composer->name}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Composer::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибраних композиторів успішно видалено.'
        ]);
    }

}
