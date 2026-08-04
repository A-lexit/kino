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
        $languages = Language::paginate(20);
        return view('admin.languages.index', compact('languages'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(TitleRequest $request)
    {
        Language::create($request->all());
        return redirect()->route('admin.languages.index')->with('success', 'Мову додано');
    }

    public function edit(string $id)
    {
        $language = Language::findOrFail($id);
        return view('admin.languages.edit', compact('language'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $language = Language::findOrFail($id);
        $language->update($request->all());
        return redirect()->route('admin.languages.index')->with('success', 'Зміни збережені');
    }


// Одиночне видалення через AJAX
    public function destroy(Language $language)
    {
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

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:languages,id',
            'action' => 'required|string|in:delete'
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
