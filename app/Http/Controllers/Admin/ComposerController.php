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
        $this->authorize('viewAny', Composer::class);

        $composers = Composer::latest('id')->paginate(20);

        return view('admin.composers.index', compact('composers'));
    }

    public function create()
    {
        $this->authorize('create', Composer::class);

        return view('admin.composers.create');
    }

    public function store(NameRequest $request)
    {
        $this->authorize('create', Composer::class);

        Composer::create($request->validated());

        return redirect()
            ->route('admin.composers.index')
            ->with('success', 'Композитора додано');
    }

    public function edit($id)
    {
        $composer = Composer::findOrFail($id);

        // Viewer може зайти у форму для перегляду.
        $this->authorize('view', $composer);

        return view('admin.composers.edit', compact('composer'));
    }

    public function update(NameRequest $request, $id)
    {
        $composer = Composer::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $composer);

        $composer->update($request->validated());

        return redirect()
            ->route('admin.composers.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Composer $composer)
    {
        $this->authorize('delete', $composer);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:composers,id',
            'action' => 'required|string|in:delete',
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
