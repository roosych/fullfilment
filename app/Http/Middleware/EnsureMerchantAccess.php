<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMerchantAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Проверяем роль через Spatie Permission (роль 'merchant' как строка)
        // ВАЖНО: Роль должна быть назначена через $user->assignRole('merchant'), 
        // а не напрямую изменена в БД, так как Spatie Permission использует кэш
        if (!$user->hasRole('merchant')) {
            abort(403, 'Access denied. Merchant role required. Use command: php artisan user:assign-merchant-role {user_id}');
        }

        // Обязательно проверяем наличие связи с Merchant
        if (!$user->merchant) {
            abort(403, 'Merchant profile not found. Please contact administrator.');
        }

        return $next($request);
    }
}

