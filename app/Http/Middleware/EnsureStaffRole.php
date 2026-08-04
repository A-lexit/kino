<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Пропускає в адмінку будь-яку staff-роль (Admin/Editor/Viewer),
 * на відміну від AdminMiddleware, який пускає ТІЛЬКИ Admin.
 * Застосовується лише до розділу "Фільми" — решта адмінки
 * (Налаштування, Користувачі, довідники) лишається на AdminMiddleware.
 */
class EnsureStaffRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->isStaff()) {
            abort(404);
        }

        return $next($request);
    }

}
