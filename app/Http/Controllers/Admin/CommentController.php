<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('film')->latest('id')->paginate(20);
        return view('admin.comments.index', compact('comments'));
    }

    public function toggle($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->toggleStatus(); // Метод, який змінює статус (наприклад, з 0 на 1 і навпаки)

        return response()->json([
            'success' => true,
            'status' => $comment->status, // Припускаємо, що поле в базі називається status
            'message' => 'Статус коментаря змінено.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // Знаходимо та видаляємо коментар
        $comment = Comment::findOrFail($id);
        $comment->delete();

        // ЯКЩО запит прийшов через AJAX (наш JS-скрипт з коліщатком або кнопкою)
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Коментар успішно видалено через AJAX.'
            ]);
        }

        // Класичний редірект (якщо раптом AJAX не спрацював або форму відправили «по-старому»)
        return redirect()->route('admin.comments.index')->with('success', 'Коментар видалено');
    }


    public function bulkAction(Request $request)
    {
        // Отримуємо масив ID та дію (у нас це 'delete')
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if ($action === 'delete' && !empty($ids)) {
            // Видаляємо всі вибрані коментарі одним махом
            Comment::whereIn('id', $ids)->delete();

            // Повертаємо JSON, який чекає наш JS у Layout
            return response()->json([
                'success' => true,
                'message' => 'Коментарі успішно видалено.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Невірна дія або не вибрано жодного коментаря.'
        ], 400);
    }
}
