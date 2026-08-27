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
        $this->authorize('viewAny', Category::class);

        $categories = Category::latest('id')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);

        return view('admin.categories.create');
    }

    public function store(TitleRequest $request)
    {
        $this->authorize('create', Category::class);

        Category::create($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Категорію додано');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        // Viewer може зайти у форму перегляду.
        $this->authorize('view', $category);

        return view('admin.categories.edit', compact('category'));
    }

    public function update(TitleRequest $request, $id)
    {
        $category = Category::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $category);

        $category->update($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Зміни збережені');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

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

    /**
     * Масове видалення через AJAX.
     */
    public function bulkAction(Request $request)
    {
        // Масове видалення доступне лише Admin.
        abort_unless(auth()->user()?->isAdmin(), 403);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
            'action' => 'required|string|in:delete',
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
