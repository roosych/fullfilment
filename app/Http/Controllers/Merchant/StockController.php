<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\StockBatch;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index(Request $request)
    {
        // Middleware уже проверил наличие merchant
        $merchant = auth()->user()->load('merchant')->merchant;

        $query = $merchant->stock_batches()
            ->with(['warehouse', 'entries']);

        // Поиск по batch_code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('batch_code', 'like', "%{$search}%");
        }

        // Фильтрация по складу
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $batches = $query->latest('received_at')->paginate(20);

        // Загружаем склады для фильтра (уникальные склады из батчей мерчанта)
        // Используем прямой запрос, чтобы избежать конфликта с orderBy в отношении stock_batches
        $warehouseIds = \App\Models\StockBatch::where('merchant_id', $merchant->id)
            ->distinct()
            ->pluck('warehouse_id')
            ->filter();
        
        $warehouses = \App\Models\Warehouse::whereIn('id', $warehouseIds)->get();

        return view('merchant.stock.index', compact('batches', 'warehouses'));
    }

    public function show(StockBatch $stockBatch)
    {
        // Проверяем доступ через политику
        $this->authorize('view', $stockBatch);

        $stockBatch->load([
            'merchant.user',
            'warehouse',
            'entries.product'
        ]);

        return view('merchant.stock.show', compact('stockBatch'));
    }
}

