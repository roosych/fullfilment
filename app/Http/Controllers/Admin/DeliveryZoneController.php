<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use App\Models\DeliveryRate;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::with('rates')->get();
        $rates = DeliveryRate::all();
        return view('admin.delivery_zones.index', compact('zones', 'rates'));
    }

    public function create()
    {
        $zones = DeliveryZone::with('rates')->get();
        return view('admin.delivery_zones.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'polygon_coordinates' => 'nullable|json',
        ]);

        $zone = DeliveryZone::create($data);

        return redirect()
            ->route('dashboard.delivery-zones.index')
            ->with('alert', [
                'type' => 'success',
                'message' => "Зона доставки '{$zone->name}' успешно создана.",
            ]);
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        return view('admin.delivery_zones.edit', compact('deliveryZone'));
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'polygon_coordinates' => 'required|json',
        ]);

        $deliveryZone->update($data);

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Зона успешно обновлена.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();
        return redirect()->route('admin.delivery-zones.index')->with('success', 'Зона успешно удалена.');
    }

    // Работа с тарифами через pivot
    public function attachRate(Request $request, DeliveryZone $deliveryZone)
    {
        $data = $request->validate([
            'delivery_rate_id' => 'required|exists:delivery_rates,id',
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'nullable|numeric|gte:min_weight',
            'price' => 'required|numeric|min:0',
        ]);

        $deliveryZone->rates()->attach($data['delivery_rate_id'], [
            'min_weight' => $data['min_weight'],
            'max_weight' => $data['max_weight'],
            'price' => $data['price'],
        ]);

        return redirect()->back()->with('success', 'Тариф успешно привязан к зоне.');
    }

    public function updateRate(Request $request, DeliveryZone $deliveryZone, DeliveryRate $rate)
    {
        $data = $request->validate([
            'min_weight' => 'required|numeric|min:0',
            'max_weight' => 'nullable|numeric|gte:min_weight',
            'price' => 'required|numeric|min:0',
        ]);

        $deliveryZone->rates()->updateExistingPivot($rate->id, $data);

        return redirect()->back()->with('success', 'Тариф успешно обновлен для зоны.');
    }

    public function detachRate(DeliveryZone $deliveryZone, DeliveryRate $rate)
    {
        $deliveryZone->rates()->detach($rate->id);

        return redirect()->back()->with('success', 'Тариф успешно удален из зоны.');
    }
}
