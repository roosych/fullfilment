<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminActive
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

        // Проверяем, что пользователь является админом
        if (!$user->hasRole(UserRoleEnum::ADMIN->value)) {
            abort(403, 'Access denied. Administrator role required.');
        }

        // Проверяем, что админ активен
        if (!$user->active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('alert', [
                    'type' => 'error',
                    'message' => 'Your account is inactive. Please contact an administrator.',
                ]);
        }

        return $next($request);
    }
}

