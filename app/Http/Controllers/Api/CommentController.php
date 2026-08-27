<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\AddNewComment;
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
            ])->validate();

            $user = Auth::user();
            $status = (int) ($validatedData['status'] ?? 0);
            $subject = $validatedData['subject'] ?? $user->name;

            AddNewComment::dispatch(
                $subject,
                $validatedData['body'],
                $status,
                $validatedData['film_id'],
                $user->id
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Коментар додано',
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
