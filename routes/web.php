<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        // Редиректим в зависимости от роли через Spatie Permission
        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.index');
        }

        if ($user->hasRole('merchant')) {
            return redirect()->route('cabinet.dashboard');
        }

        if ($user->hasRole('receiver')) {
            return redirect()->route('receiver.dashboard');
        }
    }

    return redirect()->route('dashboard.index');
});



Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');
//Route::redirect('/', 'login');

//Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
//    Route::get('/', [IndexController::class, 'index'])->name('index');
//});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
