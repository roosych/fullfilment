<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Подключаем дополнительные route файлы
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/merchant.php'));

            Route::middleware('web')
                ->group(base_path('routes/receiver.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            SetLocale::class,
        ]);

        // Регистрируем alias для Spatie middleware
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'merchant' => \App\Http\Middleware\EnsureMerchantAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Если мерчант пытается попасть на админские маршруты, редиректим на cabinet
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 403 && auth()->check()) {
                $user = auth()->user();
                
                if ($user->hasRole('merchant') && str_starts_with($request->path(), 'dashboard')) {
                    return redirect()->route('cabinet.dashboard');
                }
                
                if ($user->hasRole('receiver') && str_starts_with($request->path(), 'dashboard')) {
                    return redirect()->route('receiver.dashboard');
                }
            }
            
            return null;
        });
    })->create();
