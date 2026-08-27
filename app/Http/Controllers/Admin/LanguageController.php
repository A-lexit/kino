<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Language::class);

        $languages = Language::latest('id')->paginate(20);

        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        $this->authorize('create', Language::class);

        return view('admin.languages.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Language::class);

        Language::create($request->validated());

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Мову додано');
    }

    public function edit(string $id)
    {
        $language = Language::findOrFail($id);

        // Viewer може зайти у форму для перегляду.
        $this->authorize('view', $language);

        return view('admin.languages.edit', compact('language'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $language = Language::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $language);

        $language->update($request->validated());

        return redirect()
            ->route('admin.languages.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Language $language)
    {
        $this->authorize('delete', $language);

        if ($language->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити мову «{$language->title}», оскільки вона пов'язана з фільмами!"
            ], 422);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Мову успішно видалено.'
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
            'ids.*' => 'exists:languages,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $language = Language::find($id);

            if ($language && $language->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Мова «{$language->title}» пов'язана з фільмами."
                ], 422);
            }
        }

        Language::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані мови успішно видалено.'
        ]);
    }
}
