<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TitleRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(TitleRequest $request)
    {
        Category::create($request->all());
        return redirect()->route('admin.categories.index')->with('success', 'Категорію додано');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    public function update(TitleRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->all());
        return redirect()->route('admin.categories.index')->with('success', 'Зміни збережені');
    }

    public function destroy(Category $category)
    {
        if ($category->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити категорію «{$category->title}», оскільки вона використовується у фільмах!"
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Категорію успішно видалено.'
        ]);
    }

    // Масове видалення через AJAX
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
            'action' => 'required|string|in:delete'
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $category = Category::find($id);
            if ($category && $category->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Категорія «{$category->title}» пов'язана з фільмами."
                ], 422);
            }
        }

        Category::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибрані категорії успішно видалено.'
        ]);
    }
}
