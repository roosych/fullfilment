<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Middleware уже проверил наличие merchant
        $merchant = auth()->user()->load('merchant')->merchant;

        $query = $merchant->transactions()->with(['source']);

        // Фильтрация по типу транзакции
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Поиск по ID транзакции
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id', 'like', "%{$search}%");
        }

        $transactions = $query->latest()->paginate(50);
        
        // Загружаем order для Delivery источников
        $transactions->loadMorph('source', [
            \App\Models\Delivery::class => ['order'],
        ]);

        return view('merchant.transactions.index', compact('transactions'));
    }
}

