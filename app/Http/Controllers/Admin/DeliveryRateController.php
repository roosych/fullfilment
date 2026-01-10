<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRate;
use App\Models\DeliveryRatePrice;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryRateController extends Controller
{
    public function index()
    {
        $rates = DeliveryRate::with(['ratePrices.zone'])->latest()->get();
        $zones = DeliveryZone::all();

        return view('admin.delivery_rates.index', compact('rates', 'zones'));
    }

    public function create()
    {
        $zones = DeliveryZone::all();

        if ($zones->isEmpty()) {
            return redirect()
                ->route('dashboard.delivery-zones.create')
                ->with('alert', [
                    'type' => 'warning',
                    'message' => 'Сначала создайте хотя бы одну зону доставки перед добавлением тарифа.'
                ]);
        }

        return view('admin.delivery_rates.create', compact('zones'));
    }

    public function store(Request $request)
    {
        // Преобразуем checkbox в 0/1
        if ($request->has('zones')) {
            foreach ($request->zones as $zoneId => $intervals) {
                foreach ($intervals as $i => $interval) {
                    $request->merge([
                        "zones.$zoneId.$i.active" => isset($interval['active']) && $interval['active'] ? 1 : 0
                    ]);
                }
            }
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:delivery_rates,name',
            'description' => 'nullable|string',
            'zones' => 'nullable|array',
            'zones.*.*.min_weight' => 'nullable|numeric|min:0',
            'zones.*.*.max_weight' => 'nullable|numeric|min:0',
            'zones.*.*.price' => 'nullable|numeric|min:0',
            'zones.*.*.active' => 'nullable|boolean',
        ]);

        // Валидируем только заполненные интервалы
        $validationErrors = $this->validateIntervals($data['zones'] ?? []);
        if (!empty($validationErrors)) {
            return back()->withErrors($validationErrors)->withInput();
        }

        // Проверяем пересечения внутри одной зоны
        $overlapErrors = $this->validateNoOverlaps($data['zones'] ?? []);
        if (!empty($overlapErrors)) {
            return back()->withErrors($overlapErrors)->withInput();
        }

        $deliveryRate = DeliveryRate::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $this->saveRatePrices($deliveryRate, $data['zones'] ?? []);

        return redirect()
            ->route('dashboard.delivery-rates.index')
            ->with('success', 'Тариф успешно создан.');
    }

    public function edit(DeliveryRate $deliveryRate)
    {
        $zones = DeliveryZone::all();
        $deliveryRate->load('ratePrices.zone');

        return view('admin.delivery_rates.edit', compact('deliveryRate', 'zones'));
    }

    public function update(Request $request, DeliveryRate $deliveryRate)
    {
        // Преобразуем checkbox в 0/1
        if ($request->has('zones')) {
            foreach ($request->zones as $zoneId => $intervals) {
                foreach ($intervals as $i => $interval) {
                    $request->merge([
                        "zones.$zoneId.$i.active" => isset($interval['active']) && $interval['active'] ? 1 : 0
                    ]);
                }
            }
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'zones' => 'nullable|array',
            'zones.*.*.min_weight' => 'nullable|numeric|min:0',
            'zones.*.*.max_weight' => 'nullable|numeric|min:0',
            'zones.*.*.price' => 'nullable|numeric|min:0',
            'zones.*.*.active' => 'nullable|boolean',
        ]);

        // Валидируем только заполненные интервалы
        $validationErrors = $this->validateIntervals($data['zones'] ?? []);
        if (!empty($validationErrors)) {
            return back()->withErrors($validationErrors)->withInput();
        }

        // Проверяем пересечения
        $overlapErrors = $this->validateNoOverlaps($data['zones'] ?? []);
        if (!empty($overlapErrors)) {
            return back()->withErrors($overlapErrors)->withInput();
        }

        $deliveryRate->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Удаляем старые цены и сохраняем новые
        $deliveryRate->ratePrices()->delete();
        $this->saveRatePrices($deliveryRate, $data['zones'] ?? []);

        return redirect()
            ->route('dashboard.delivery-rates.index')
            ->with('success', 'Тариф успешно обновлен.');
    }

    public function destroy(DeliveryRate $deliveryRate)
    {
        $deliveryRate->delete();
        return redirect()->route('dashboard.delivery-rates.index')->with('success', 'Тариф успешно удален.');
    }

    public function getZones($rateId)
    {
        $zones = DeliveryZone::whereHas('ratePrices', function($query) use ($rateId) {
            $query->where('delivery_rate_id', $rateId);
        })
            ->distinct()
            ->get();

        return response()->json([
            'zones' => $zones
        ]);
    }







    /**
     * Валидирует заполненные интервалы (если указана цена, то должны быть min и max)
     */
    protected function validateIntervals(array $zones): array
    {
        $errors = [];

        foreach ($zones as $zoneId => $intervals) {
            foreach ($intervals as $index => $interval) {
                // Пропускаем полностью пустые строки
                if (empty($interval['price']) && empty($interval['min_weight']) && empty($interval['max_weight'])) {
                    continue;
                }

                // Если указана цена, то должны быть min и max
                if (!empty($interval['price'])) {
                    if (empty($interval['min_weight']) && $interval['min_weight'] !== '0' && $interval['min_weight'] !== 0) {
                        $errors["zones.$zoneId.$index.min_weight"] = "Минимальный вес обязателен при указании цены.";
                    }
                    if (empty($interval['max_weight']) && $interval['max_weight'] !== '0' && $interval['max_weight'] !== 0) {
                        $errors["zones.$zoneId.$index.max_weight"] = "Максимальный вес обязателен при указании цены.";
                    }
                }

                // Проверяем что max >= min (только если оба заполнены)
                if (isset($interval['min_weight']) && isset($interval['max_weight'])) {
                    if ($interval['max_weight'] < $interval['min_weight']) {
                        $errors["zones.$zoneId.$index.max_weight"] = "Максимальный вес должен быть больше или равен минимальному весу.";
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Проверяет пересечения диапазонов веса внутри каждой зоны
     * Диапазоны [0-1000] и [1000-2000] НЕ пересекаются (граница допустима)
     * Диапазоны [0-1000] и [500-2500] ПЕРЕСЕКАЮТСЯ
     */
    protected function validateNoOverlaps(array $zones): array
    {
        $errors = [];

        foreach ($zones as $zoneId => $intervals) {
            // Фильтруем только интервалы с ценой
            $validIntervals = array_filter($intervals, fn($i) => !empty($i['price']));

            if (empty($validIntervals)) {
                continue;
            }

            // Получаем название зоны
            $zone = DeliveryZone::find($zoneId);
            $zoneName = $zone ? $zone->name : "Zone #$zoneId";

            // Сортируем по min_weight
            usort($validIntervals, fn($a, $b) => $a['min_weight'] <=> $b['min_weight']);

            // Проверяем каждую пару соседних интервалов
            for ($i = 0; $i < count($validIntervals) - 1; $i++) {
                $current = $validIntervals[$i];
                $next = $validIntervals[$i + 1];

                // Пересечение есть, если следующий min МЕНЬШЕ текущего max
                // [0-1000] и [1000-2000] - OK (1000 >= 1000)
                // [0-1000] и [500-2500] - ОШИБКА (500 < 1000)
                if ($next['min_weight'] < $current['max_weight']) {
                    $errors["zones.$zoneId"] = sprintf(
                        "Диапазоны веса пересекаются в %s: [%s-%s г] и [%s-%s г]",
                        $zoneName,
                        $current['min_weight'],
                        $current['max_weight'],
                        $next['min_weight'],
                        $next['max_weight']
                    );
                    break; // Одной ошибки на зону достаточно
                }
            }
        }

        return $errors;
    }

    /**
     * Сохраняет диапазоны веса для тарифа
     */
    protected function saveRatePrices(DeliveryRate $deliveryRate, array $zones)
    {
        foreach ($zones as $zoneId => $intervals) {
            foreach ($intervals as $interval) {
                // Пропускаем интервалы без цены
                if (empty($interval['price'])) {
                    continue;
                }

                DeliveryRatePrice::create([
                    'delivery_rate_id' => $deliveryRate->id,
                    'delivery_zone_id' => $zoneId,
                    'min_weight' => $interval['min_weight'],
                    'max_weight' => $interval['max_weight'],
                    'price' => $interval['price'] * 100, // Конвертируем в копейки
                    'active' => $interval['active'] ?? true,
                ]);
            }
        }
    }
}
