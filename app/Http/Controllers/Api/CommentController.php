<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AddNewComment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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

            // Визначаємо користувача:
            // 1. авторизований користувач;
            // 2. користувач із переданого user_id;
            // 3. гість.
            $user = Auth::user()
                ?? ($request->filled('user_id')
                    ? User::find($request->user_id)
                    : null);

            $status = (int) ($validatedData['status'] ?? 0);

            // Якщо subject не передано — беремо ім'я користувача.
            // Для гостя використовуємо "Гість".
            $subject = $validatedData['subject']
                ?? ($user?->name ?? 'Гість');

            $userId = $user?->id ?? $validatedData['user_id'] ?? null;

            // Коментар створюється у фоні через Redis queue.
            AddNewComment::dispatch(
                $subject,
                $validatedData['body'],
                $status,
                $validatedData['film_id'],
                $userId
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Коментар прийнято до обробки',
            ], 201);

        } catch (ValidationException $e) {
            throw $e;

        } catch (\Throwable $e) {
            \Log::error('Помилка постановки коментаря в чергу', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Помилка додавання коментаря',
            ], 500);
        }
    }
}
