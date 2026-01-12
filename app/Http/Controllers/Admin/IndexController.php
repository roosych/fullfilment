<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockEntry;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        // Общая статистика заказов
        $totalOrders = Order::count();
        $ordersToday = Order::whereDate('created_at', today())->count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Статистика по статусам заказов
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status->value => $item->count];
            });

        // Статистика мерчантов
        $totalMerchants = Merchant::count();
        $activeMerchants = Merchant::whereHas('user', function ($query) {
            $query->where('active', true);
        })->count();

        // Статистика товаров на складах
        $totalProducts = Product::count();
        $totalStockQuantity = StockEntry::where('remaining_quantity', '>', 0)->sum('remaining_quantity');
        $totalWarehouses = Warehouse::where('active', true)->count();

        // Статистика доставок
        $totalDeliveries = Delivery::count();
        $completedDeliveries = Delivery::where('status', DeliveryStatusEnum::DELIVERED)->count();

        // Общая сумма доставок (если есть поле price)
        $totalDeliveryRevenue = Delivery::where('status', DeliveryStatusEnum::DELIVERED)
            ->sum('price');

        // Последние заказы
        $recentOrders = Order::with(['merchant.user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.index', compact(
            'totalOrders',
            'ordersToday',
            'ordersThisMonth',
            'ordersByStatus',
            'totalMerchants',
            'activeMerchants',
            'totalProducts',
            'totalStockQuantity',
            'totalWarehouses',
            'totalDeliveries',
            'completedDeliveries',
            'totalDeliveryRevenue',
            'recentOrders'
        ));
    }
}
