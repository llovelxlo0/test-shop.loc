<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

    // Пропускаем гостей (не авторизованных)
    if (!$user) {
        return $next($request);
    }

    // Пропускаем, если 2FA вообще не включена
    if (!$user->twoFactor || !$user->twoFactor->enabled) {
        return $next($request);
    }

    // Разрешаем только страницы, связанные с вводом кода
    if ($request->is('2fa/*') || $request->is('logout')) {
        return $next($request);
    }

    // Если еще не ввёл код — отправляем на страницу ввода
    if (!session('2fa_passed', false)) {
        return redirect()->route('2fa.login.form');
    }

    return $next($request);
    }
}
