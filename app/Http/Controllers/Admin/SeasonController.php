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
        $this->authorize('viewAny', Season::class);

        $seasons = Season::latest('id')->paginate(20);

        return view('admin.seasons.index', compact('seasons'));
    }

    public function create()
    {
        $this->authorize('create', Season::class);

        return view('admin.seasons.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Season::class);

        Season::create($request->validated());

        return redirect()
            ->route('admin.seasons.index')
            ->with('success', 'Сезон додано');
    }

    public function edit($id)
    {
        $season = Season::findOrFail($id);

        $this->authorize('view', $season);

        return view('admin.seasons.edit', compact('season'));
    }

    public function update(TitleRequest $request, $id)
    {
        $season = Season::findOrFail($id);

        $this->authorize('update', $season);

        $season->update($request->validated());

        return redirect()
            ->route('admin.seasons.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Season $season)
    {
        $this->authorize('delete', $season);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:seasons,id',
            'action' => 'required|string|in:delete',
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
