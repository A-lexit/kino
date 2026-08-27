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
        $this->authorize('viewAny', Quality::class);

        $qualities = Quality::latest('id')->paginate(20);

        return view('admin.qualities.index', compact('qualities'));
    }

    public function create()
    {
        $this->authorize('create', Quality::class);

        return view('admin.qualities.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Quality::class);

        Quality::create($request->validated());

        return redirect()
            ->route('admin.qualities.index')
            ->with('success', 'Якість додано');
    }

    public function edit($id)
    {
        $quality = Quality::findOrFail($id);

        // Viewer може зайти у форму для перегляду.
        $this->authorize('view', $quality);

        return view('admin.qualities.edit', compact('quality'));
    }

    public function update(TitleRequest $request, $id)
    {
        $quality = Quality::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $quality);

        $quality->update($request->validated());

        return redirect()
            ->route('admin.qualities.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Quality $quality)
    {
        $this->authorize('delete', $quality);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:qualities,id',
            'action' => 'required|string|in:delete',
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
