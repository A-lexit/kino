<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NameRequest;
use App\Models\Director;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Director::class);

        $directors = Director::latest('id')->paginate(20);

        return view('admin.directors.index', compact('directors'));
    }

    public function create()
    {
        $this->authorize('create', Director::class);

        return view('admin.directors.create');
    }

    public function store(NameRequest $request)
    {
        $this->authorize('create', Director::class);

        Director::create($request->validated());

        return redirect()
            ->route('admin.directors.index')
            ->with('success', 'Режисера додано');
    }

    public function edit(string $id)
    {
        $director = Director::findOrFail($id);

        // Viewer може зайти у форму для перегляду.
        $this->authorize('view', $director);

        return view('admin.directors.edit', compact('director'));
    }

    public function update(NameRequest $request, string $id)
    {
        $director = Director::findOrFail($id);

        // Admin та Editor можуть зберігати зміни.
        $this->authorize('update', $director);

        $director->update($request->validated());

        return redirect()
            ->route('admin.directors.index')
            ->with('success', 'Зміни збережені');
    }

    /**
     * Одиночне видалення через AJAX.
     */
    public function destroy(Director $director)
    {
        $this->authorize('delete', $director);

        if ($director->films()->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Неможливо видалити режисера «{$director->name}», оскільки він пов'язаний з фільмами!"
            ], 422);
        }

        $director->delete();

        return response()->json([
            'success' => true,
            'message' => 'Режисера успішно видалено.'
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
            'ids.*' => 'exists:directors,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $director = Director::find($id);

            if ($director && $director->films()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "Масове видалення перервано. Режисер «{$director->name}» пов'язаний з фільмами."
                ], 422);
            }
        }

        Director::destroy($ids);

        return response()->json([
            'success' => true,
            'message' => 'Вибраних режисерів успішно видалено.'
        ]);
    }
}
