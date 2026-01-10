<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        
        if ($user->hasVerifiedEmail()) {
            // Определяем маршрут в зависимости от роли через Spatie Permission
            $redirectRoute = match (true) {
                $user->hasRole('admin') => route('dashboard.index'),
                $user->hasRole('merchant') => route('cabinet.dashboard'),
                $user->hasRole('receiver') => route('receiver.dashboard'),
                default => route('dashboard.index'),
            };
            
            return redirect()->intended($redirectRoute);
        }
        
        return view('auth.verify-email');
    }
}
