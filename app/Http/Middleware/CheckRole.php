<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Доступ запрещен');
        }

        $user = auth()->user();

        // Получаем строковое значение роли (поддержка string и enum)
        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        // Проверяем, есть ли роль пользователя среди разрешенных
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, "Доступ запрещен. Ваша роль: {$userRole}, требуется: " . implode(', ', $roles));
    }
}
