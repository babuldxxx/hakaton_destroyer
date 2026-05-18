<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;

// class CheckRole
// {
//     public function handle(Request $request, Closure $next, string $role): Response
//     {
//         $userRole = $request->user()->role;

//         if ($userRole instanceof \BackedEnum) {
//             $userRole = $userRole->value;
//         }

//         if ($userRole !== $role) {
//             abort(403, 'Доступ запрещён. Ваша роль: ' . $userRole . ', требуется: ' . $role);
//         }

//         return $next($request);
//     }
//}