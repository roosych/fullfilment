<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::all();
        return view('admin.warehouse.index', compact('warehouses'));
    }

    public function create()
    {
        $managers = User::role(UserRoleEnum::ADMIN->value)->get();
        return view('admin.warehouse.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'active' => $request->has('active'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'manager_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
//            'location_lat' => 'nullable|numeric|between:-90,90',
//            'location_lng' => 'nullable|numeric|between:-180,180',
            'active' => 'boolean',
        ]);

        $warehouse = Warehouse::create($validated);

        return redirect()
            ->route('dashboard.warehouses.index')
            ->with('alert', [
                'type' => 'success',
                'message' => "Warehouse '{$warehouse->name}' created successfully.",
            ]);
    }


    public function show(Warehouse $warehouse)
    {
        $managers = User::role(UserRoleEnum::ADMIN->value)->get();
        return view('admin.warehouse.show', compact('warehouse', 'managers'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->merge([
            'active' => $request->has('active'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'manager_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return redirect()
            ->route('dashboard.warehouses.index')
            ->with('alert', [
                'type' => 'success',
                'message' => "Warehouse '{$warehouse->name}' updated successfully.",
            ]);
    }

    public function setPrimary(Warehouse $warehouse)
    {
        // Сбрасываем признак primary у всех складов
        Warehouse::query()->update(['is_primary' => false]);

        // Устанавливаем primary только у выбранного
        $warehouse->update(['is_primary' => true]);

        return redirect()
            ->route('dashboard.warehouses.index')
            ->with('alert', [
                'type' => 'success',
                'message' => "Warehouse '{$warehouse->name}' set as primary successfully.",
            ]);
    }
}
