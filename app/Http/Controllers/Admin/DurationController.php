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
        $this->authorize('viewAny', Duration::class);

        $durations = Duration::latest('id')->paginate(20);

        return view('admin.durations.index', compact('durations'));
    }

    public function create()
    {
        $this->authorize('create', Duration::class);

        return view('admin.durations.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Duration::class);

        Duration::create($request->validated());

        return redirect()
            ->route('admin.durations.index')
            ->with('success', 'Тривалість додано');
    }

    public function edit($id)
    {
        $duration = Duration::findOrFail($id);

        // Viewer може відкрити форму для перегляду.
        $this->authorize('view', $duration);

        return view('admin.durations.edit', compact('duration'));
    }

    public function update(TitleRequest $request, $id)
    {
        $duration = Duration::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $duration);

        $duration->update($request->validated());

        return redirect()
            ->route('admin.durations.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Duration $duration)
    {
        $this->authorize('delete', $duration);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:durations,id',
            'action' => 'required|string|in:delete',
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
