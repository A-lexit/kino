<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBan
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_banned == 1) {
            Auth::logout();

            return redirect('login')
                ->with('error', 'Ваш акаунт заблоковано');
        }

        if ($request->is('telegram/webhook')) {
            return $next($request);
        }

        return $next($request);
    }

}
