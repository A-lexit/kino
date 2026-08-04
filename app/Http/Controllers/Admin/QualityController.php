<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Quality;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index()
    {
        $qualities = Quality::paginate(20);
        return view('admin.qualities.index', compact('qualities'));
    }

    public function create()
    {
        return view('admin.qualities.create');
    }

    public function store(TitleRequest $request)
    {
        Quality::create($request->all());
        return redirect()->route('admin.qualities.index')->with('success', 'Якість додано');
    }

    public function edit($id)
    {
        $quality = Quality::findOrFail($id);
        return view('admin.qualities.edit', compact('quality'));
    }

    public function update(TitleRequest $request, $id)
    {
        $quality = Quality::findOrFail($id);
        $quality->update($request->all());
        return redirect()->route('admin.qualities.index')->with('success', 'Зміни збережені');
    }


// Одиночне видалення через AJAX
    public function destroy(Quality $quality)
    {
        if ($quality->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити якість «{$quality->title}», оскільки вона пов'язана з фільмами!"
            ], 422);
        }

        $quality->delete();

        return response()->json([
            'success' => true,
            'message' => 'Якість відео успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:qualities,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $quality = Quality::find($id);
            if ($quality && $quality->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Якість «{$quality->title}» пов'язана з фільмами."
                ], 422);
            }
        }

        Quality::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані якості відео успішно видалено.'
        ]);
    }

}
