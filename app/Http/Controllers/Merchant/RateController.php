<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRate;

class RateController extends Controller
{
    public function index()
    {
        // Загружаем активные тарифы с ценами по зонам
        $deliveryRates = DeliveryRate::where('active', true)
            ->with(['ratePrices' => function($query) {
                $query->where('active', true)
                    ->with('zone')
                    ->orderBy('delivery_zone_id')
                    ->orderBy('min_weight');
            }])
            ->get();

        return view('merchant.rates.index', compact('deliveryRates'));
    }
}

