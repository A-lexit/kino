<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Comment::class);

        $comments = Comment::with('film')
            ->latest('id')
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function toggle($id)
    {
        $comment = Comment::findOrFail($id);

        $this->authorize('update', $comment);

        $comment->toggleStatus();

        return response()->json([
            'success' => true,
            'status' => $comment->status,
            'message' => 'Статус коментаря змінено.',
        ]);
    }

    public function destroy(Comment $comment, Request $request)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Коментар успішно видалено через AJAX.',
            ]);
        }

        return redirect()
            ->route('admin.comments.index')
            ->with('success', 'Коментар видалено');
    }

    public function bulkAction(Request $request)
    {
        abort_unless(
            $request->user()?->isAdmin(),
            403
        );

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:comments,id',
            'action' => 'required|string|in:delete',
        ]);

        $ids = $request->input('ids');

        Comment::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Коментарі успішно видалено.',
        ]);
    }
}
