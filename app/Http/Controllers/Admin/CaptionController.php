<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Caption;
use Illuminate\Http\Request;

class CaptionController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Caption::class);

        $captions = Caption::latest('id')->paginate(20);

        return view('admin.captions.index', compact('captions'));
    }

    public function create()
    {
        $this->authorize('create', Caption::class);

        return view('admin.captions.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Caption::class);

        Caption::create($request->validated());

        return redirect()
            ->route('admin.captions.index')
            ->with('success', 'Підпис додано');
    }

    public function edit(string $id)
    {
        $caption = Caption::findOrFail($id);

        // Viewer може відкрити форму для перегляду.
        $this->authorize('view', $caption);

        return view('admin.captions.edit', compact('caption'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $caption = Caption::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $caption);

        $caption->update($request->validated());

        return redirect()
            ->route('admin.captions.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Caption $caption)
    {
        $this->authorize('delete', $caption);

        if ($caption->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити субтитри «{$caption->title}», оскільки вони прив’язані до фільмів!"
            ], 422);
        }

        $caption->delete();

        return response()->json([
            'success' => true,
            'message' => 'Субтитри успішно видалено.'
        ]);
    }

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Bulk delete доступний лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:captions,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $caption = Caption::find($id);

            if ($caption && $caption->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Субтитри «{$caption->title}» використовуються у фільмах."
                ], 422);
            }
        }

        Caption::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані субтитри успішно видалено.'
        ]);
    }
}
