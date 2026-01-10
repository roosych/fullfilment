<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Middleware уже проверил наличие merchant, но загружаем с отношениями
        $merchant = auth()->user()->load('merchant')->merchant;

        // Статистика по заказам
        $ordersCount = $merchant->orders()->count();
        $activeOrdersCount = $merchant->orders()
            ->whereIn('status', [
                \App\Enums\OrderStatusEnum::CREATED,
                \App\Enums\OrderStatusEnum::READY_FOR_DELIVERY,
                \App\Enums\OrderStatusEnum::DELIVERY_IN_PROGRESS,
            ])
            ->count();
        $completedOrdersCount = $merchant->orders()
            ->where('status', \App\Enums\OrderStatusEnum::DELIVERED)
            ->count();

        // Статистика по товарам
        $productsCount = $merchant->products()->count();
        $totalStock = DB::table('stock_entries')
            ->join('products', 'stock_entries.product_id', '=', 'products.id')
            ->where('products.merchant_id', $merchant->id)
            ->select(DB::raw('SUM(stock_entries.remaining_quantity - stock_entries.reserved_quantity) as available'))
            ->value('available') ?? 0;

        // Последние заказы
        $recentOrders = $merchant->orders()
            ->with(['items.product', 'zone', 'rate'])
            ->latest()
            ->limit(10)
            ->get();

        // Последние 5 транзакций
        $recentTransactions = $merchant->transactions()
            ->with(['source'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Загружаем order для Delivery источников
        $recentTransactions->loadMorph('source', [
            \App\Models\Delivery::class => ['order'],
        ]);

        return view('merchant.dashboard.index', compact(
            'merchant',
            'ordersCount',
            'activeOrdersCount',
            'completedOrdersCount',
            'productsCount',
            'totalStock',
            'recentOrders',
            'recentTransactions'
        ));
    }
}

