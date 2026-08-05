<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = Validator::make($request->all(), [
                'body' => 'required|string|max:255',
                'subject' => 'nullable|string|max:255',
                'status' => 'nullable|integer',
                'film_id' => 'required|integer|exists:films,id',
                'user_id' => 'nullable|integer',
            ])->validate();

            // Знаходимо користувача через Auth або переданий з Vue user_id
            $user = Auth::user() ?? ($request->filled('user_id') ? User::find($request->user_id) : null);

            $status = $validatedData['status'] ?? 0;
            
            // Якщо subject не передано, беремо ім'я користувача або ставимо 'Гість'
            $subject = $validatedData['subject'] ?? ($user?->name ?? 'Гість');

            $comment = new Comment();
            $comment->body = $validatedData['body'];
            $comment->subject = $subject;
            $comment->status = $status;
            $comment->film_id = $validatedData['film_id'];
            $comment->user_id = $user?->id ?? $validatedData['user_id'] ?? null;
            $comment->save();

            Log::info('Коментар успішно збережено:', $comment->toArray());

            return response()->json(['status' => 'success', 'message' => 'Коментар додано'], 201);

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            Log::error('Помилка збереження коментаря: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Помилка додавання коментаря'], 500);
        }
    }
}
