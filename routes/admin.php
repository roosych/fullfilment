<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\DeliveryRateController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\IndexController;

Route::prefix('dashboard')
    ->middleware(['auth', 'role:admin'])
    ->name('dashboard.')
    ->group(function () {
        Route::get('/', [IndexController::class, 'index'])->name('index');

        Route::get('merchants', [MerchantController::class, 'index'])->name('merchants.index');
        Route::get('merchants/create', [MerchantController::class, 'create'])->name('merchants.create');
        Route::post('merchants/create', [MerchantController::class, 'store'])->name('merchants.store');
        Route::post('merchants/topup', [MerchantController::class, 'topup'])->name('merchants.topup');
        Route::get('merchants/{merchant}/show', [MerchantController::class, 'show'])->name('merchants.show');
        Route::get('merchants/{merchant}/edit', [MerchantController::class, 'edit'])->name('merchants.edit');
        Route::put('merchants/{merchant}', [MerchantController::class, 'update'])->name('merchants.update');
        Route::get('merchants/{merchant}/stock', [MerchantController::class, 'stock'])->name('merchants.stock');

        Route::get('merchants/{merchant}/products', [OrderController::class, 'getMerchantProducts'])->name('merchant.products');

        Route::get('delivery-zones', [DeliveryZoneController::class, 'index'])->name('delivery-zones.index');
        Route::post('delivery-zones', [DeliveryZoneController::class, 'store'])->name('delivery-zones.store');

//        Route::resource('delivery-rates', DeliveryRateController::class);

        Route::get('delivery-rates', [DeliveryRateController::class, 'index'])->name('delivery-rates.index');
        Route::get('delivery-rates/create', [DeliveryRateController::class, 'create'])->name('delivery-rates.create');
        Route::post('delivery-rates/create', [DeliveryRateController::class, 'store'])->name('delivery-rates.store');
        Route::get('delivery-rates/{deliveryRate}/edit', [DeliveryRateController::class, 'edit'])->name('delivery-rates.edit');
        Route::put('delivery-rates/{deliveryRate}/edit', [DeliveryRateController::class, 'update'])->name('delivery-rates.update');
        Route::get('delivery-rates/{id}/zones', [DeliveryRateController::class, 'getZones'])->name('delivery-rates.zones');


        Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
        Route::post('warehouses/create', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::get('warehouses/{warehouse}/edit', [WarehouseController::class, 'show'])->name('warehouses.show');
        Route::put('warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::patch('/warehouses/{warehouse}/set-primary', [WarehouseController::class, 'setPrimary'])
            ->name('warehouses.setPrimary');

        Route::get('stock', [StockController::class, 'index'])->name('stock.index');
        Route::get('stock/create/{merchant}/{warehouse}', [StockController::class, 'create'])->name('stock.create');
        Route::post('preview/{merchant}/{warehouse}', [StockController::class, 'preview'])->name('stock.preview');
        Route::get('preview/{merchant}/{warehouse}', [StockController::class, 'showPreview'])->name('stock.showPreview');
        Route::post('stock/create/{merchant}/{warehouse}', [StockController::class, 'store'])->name('stock.store');

        Route::get('batches', [BatchController::class, 'index'])->name('batch.index');
        Route::get('batches/{stockBatch}/receipt', [BatchController::class, 'receipt'])->name('batch.receipt');

        Route::get('orders', [OrderController::class, 'index'])->name('order.index');
        Route::get('orders/create', [OrderController::class, 'create'])->name('order.create');
        Route::post('orders/create', [OrderController::class, 'store'])->name('order.store');
        Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
        Route::get('orders/{order}/show', [OrderController::class, 'show'])->name('order.show');

        Route::post('delivery/calculate-price', [OrderController::class, 'calculatePrice'])
            ->name('delivery.calculatePrice');

        Route::post('/deliveries/{order:uuid}', [DeliveryController::class, 'store'])
            ->whereUuid('uuid')
            ->name('deliveries.store');

        Route::post('deliveries/{delivery}/complete', [DeliveryController::class, 'complete'])
            ->name('deliveries.complete');

        Route::post('deliveries/{delivery}/cancel', [DeliveryController::class, 'cancel'])
            ->name('deliveries.cancel');

        Route::post('deliveries/{delivery}/fail', [DeliveryController::class, 'fail'])
            ->name('deliveries.fail');

        Route::post('deliveries/{delivery}/start', [DeliveryController::class, 'startDelivery'])
            ->name('deliveries.start');

    });
