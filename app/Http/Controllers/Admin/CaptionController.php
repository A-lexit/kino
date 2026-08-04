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
        $captions = Caption::paginate(20);
        return view('admin.captions.index', compact('captions'));
    }

    public function create()
    {
        return view('admin.captions.create');
    }

    public function store(TitleRequest $request)
    {
        Caption::create($request->all());
        return redirect()->route('admin.captions.index')->with('success', 'Підпис додано');
    }

    public function edit(string $id)
    {
        $caption = Caption::findOrFail($id);
        return view('admin.captions.edit', compact('caption'));
    }

    public function update(TitleRequest $request, string $id)
    {
        $caption = Caption::findOrFail($id);
        $caption->update($request->all());
        return redirect()->route('admin.captions.index')->with('success', 'Зміни збережені');
    }

    // Одиночне видалення через AJAX
    public function destroy(Caption $caption)
    {
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

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:captions,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        // Перевірка наявності зв'язків перед видаленням
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
