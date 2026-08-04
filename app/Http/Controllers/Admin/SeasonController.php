<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = Season::paginate(20);
        return view('admin.seasons.index', compact('seasons'));
    }

    public function create()
    {
        return view('admin.seasons.create');
    }

    public function store(TitleRequest $request)
    {
        Season::create($request->all());
        return redirect()->route('admin.seasons.index')->with('success', 'Сезон додано');
    }

    public function edit($id)
    {
        $season = Season::findOrFail($id);
        return view('admin.seasons.edit', compact('season'));
    }

    public function update(TitleRequest $request, $id)
    {
        $season = Season::findOrFail($id);
        $season->update($request->all());
        return redirect()->route('admin.seasons.index')->with('success', 'Зміни збережені');
    }


    // Одиночне видалення через AJAX
    public function destroy(Season $season)
    {
        if ($season->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити сезон «{$season->title}», оскільки він пов'язаний з фільмами чи серіями!"
            ], 422);
        }

        $season->delete();

        return response()->json([
            'success' => true,
            'message' => 'Сезон успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:seasons,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $season = Season::find($id);
            if ($season && $season->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Сезон «{$season->title}» пов'язаний з контентом."
                ], 422);
            }
        }

        Season::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані сезони успішно видалено.'
        ]);
    }

}
