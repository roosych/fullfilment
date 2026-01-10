<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Middleware уже проверил наличие merchant
        $merchant = auth()->user()->load('merchant')->merchant;

        $query = $merchant->products()->with('stockEntries');

        // Поиск
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->get()->map(function ($product) {
            $availableStock = $product->stockEntries()->sum(DB::raw('remaining_quantity - reserved_quantity'));
            $totalStock = $product->stockEntries()->sum('remaining_quantity');
            $reservedStock = $product->stockEntries()->sum('reserved_quantity');
            
            return [
                'product' => $product,
                'available_stock' => $availableStock,
                'total_stock' => $totalStock,
                'reserved_stock' => $reservedStock,
            ];
        });

        return view('merchant.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        // Проверяем доступ через Policy
        $this->authorize('view', $product);

        // Middleware уже проверил наличие merchant
        $merchant = auth()->user()->load('merchant')->merchant;

        // Загружаем остатки по складам
        $product->load('stockEntries.batch.warehouse');
        
        $availableStock = $product->stockEntries()->sum(DB::raw('remaining_quantity - reserved_quantity'));
        $totalStock = $product->stockEntries()->sum('remaining_quantity');
        $reservedStock = $product->stockEntries()->sum('reserved_quantity');

        // Группировка по складам
        $stockByWarehouse = [];
        foreach ($product->stockEntries as $entry) {
            if ($entry->batch && $entry->batch->warehouse) {
                $warehouseName = $entry->batch->warehouse->name;
                $stockByWarehouse[$warehouseName] = ($stockByWarehouse[$warehouseName] ?? 0) + $entry->remaining_quantity;
            }
        }

        return view('merchant.products.show', compact('product', 'availableStock', 'totalStock', 'reservedStock', 'stockByWarehouse'));
    }
}

