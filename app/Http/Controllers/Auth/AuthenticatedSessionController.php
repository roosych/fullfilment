<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // Перенаправление в зависимости от роли через Spatie Permission
        // Приоритет: admin > merchant > receiver
        $redirectTo = match (true) {
            $user->hasRole('admin') => route('dashboard.index'),
            $user->hasRole('merchant') => route('cabinet.dashboard'),
            $user->hasRole('receiver') => route('receiver.dashboard'),
            default => route('login'),
        };

        // Очищаем intended URL из сессии, чтобы всегда редиректить на правильный маршрут
        session()->forget('url.intended');
        
        return redirect($redirectTo);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
