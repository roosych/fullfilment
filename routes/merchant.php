<?php

use App\Http\Controllers\Merchant\DashboardController;
use App\Http\Controllers\Merchant\OrderController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\RateController;
use App\Http\Controllers\Merchant\StockController;
use App\Http\Controllers\Merchant\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('cabinet')
    ->middleware(['auth', 'merchant'])
    ->name('cabinet.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/create', [OrderController::class, 'store'])->name('store');
            
            // AJAX endpoints - должны быть перед {order} маршрутом
            Route::get('/products/list', [OrderController::class, 'getProducts'])->name('products.list');
            Route::get('/rates/{rateId}/zones', [OrderController::class, 'getZones'])->name('rates.zones');
            Route::post('/calculate-price', [OrderController::class, 'calculatePrice'])->name('calculate-price');
            
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        });
        
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        });
        
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/{stockBatch}', [StockController::class, 'show'])->name('show');
        });
        
        Route::get('/rates', [RateController::class, 'index'])->name('rates.index');
        
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    });

