<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Selection;
use Illuminate\Http\Request;

class SelectionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Selection::class);

        $selections = Selection::latest('id')->paginate(20);

        return view('admin.selections.index', compact('selections'));
    }

    public function create()
    {
        $this->authorize('create', Selection::class);

        return view('admin.selections.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Selection::class);

        Selection::create($request->validated());

        return redirect()
            ->route('admin.selections.index')
            ->with('success', 'Добірку додано');
    }

    public function edit(string $id)
    {
        $selection = Selection::findOrFail($id);

        $this->authorize('view', $selection);

        return view('admin.selections.edit', compact('selection'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $selection = Selection::findOrFail($id);

        $this->authorize('update', $selection);

        $selection->update($request->validated());

        return redirect()
            ->route('admin.selections.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Selection $selection)
    {
        $this->authorize('delete', $selection);

        if ($selection->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити добірку «{$selection->title}», оскільки вона містить фільми!"
            ], 422);
        }

        $selection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Добірку успішно видалено.'
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
            'ids.*' => 'exists:selections,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $selection = Selection::find($id);

            if ($selection && $selection->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Добірка «{$selection->title}» містить фільми."
                ], 422);
            }
        }

        Selection::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані добірки успішно видалено.'
        ]);
    }
}
