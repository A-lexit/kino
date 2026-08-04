<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Duration;
use Illuminate\Http\Request;

class DurationController extends Controller
{
    public function index()
    {
        $durations = Duration::paginate(20);
        return view('admin.durations.index', compact('durations'));
    }

    public function create()
    {
        return view('admin.durations.create');
    }

    public function store(TitleRequest $request)
    {
        Duration::create($request->all());
        return redirect()->route('admin.durations.index')->with('success', 'Тривалість додано');
    }

    public function edit($id)
    {
        $duration = Duration::findOrFail($id);
        return view('admin.durations.edit', compact('duration'));
    }

    public function update(TitleRequest $request, $id)
    {
        $duration = Duration::findOrFail($id);
        $duration->update($request->all());
        return redirect()->route('admin.durations.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Duration $duration)
    {
        if ($duration->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити тривалість «{$duration->title}», оскільки вона пов'язана з фільмами!"
            ], 422);
        }

        $duration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Тривалість успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:durations,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $duration = Duration::find($id);
            if ($duration && $duration->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Тривалість «{$duration->title}» пов'язана з фільмами."
                ], 422);
            }
        }

        Duration::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані інтервали тривалості успішно видалено.'
        ]);
    }
}
