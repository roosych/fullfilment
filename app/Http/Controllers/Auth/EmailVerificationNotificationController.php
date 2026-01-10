<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
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

        $user->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
