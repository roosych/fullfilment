<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BalanceTransactionTypeEnum;
use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Jobs\SendMerchantWelcomeEmail;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    public function index()
    {
        $merchants = Merchant::with('user')
            ->latest()
            ->get();

        return view('admin.merchants.index', compact('merchants'));
    }

    public function create()
    {
        return view('admin.merchants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'company' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'notes' => 'nullable|string',
            'balance' => 'nullable|numeric|min:0',
            'avatar' => 'nullable|string',
            'id_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $user = null;
        $merchant = null;
        $plainPassword = Str::random(8);

        DB::transaction(function () use ($data, &$user, &$merchant, $plainPassword, $request) {
            // Создаём пользователя
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'password' => Hash::make($plainPassword),
                'active' => true,
                'email_verified_at' => now(),
            ]);

            // Присваиваем роль мерчанта
            $user->assignRole(UserRoleEnum::MERCHANT->value);

            // Создаём мерчанта
            $merchant = Merchant::create([
                'user_id' => $user->id,
                'company' => $data['company'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'avatar' => $data['avatar'] ?? null,
                'notes' => $data['notes'] ?? null,
                'balance' => (int) round($data['balance'] * 100) ?? 0,
            ]);

            // Обработка загрузки id_card после создания мерчанта (чтобы знать id)
            if ($request->hasFile('id_card')) {
                $file = $request->file('id_card');
                $extension = $file->getClientOriginalExtension();
                $idCardFileName = $merchant->id . '.' . $extension;
                $file->storeAs('merchants/id_cards', $idCardFileName, 'public');
                $merchant->update(['id_card' => $idCardFileName]);
            }

            if (!empty($data['balance']) && $data['balance'] > 0) {
                $merchant->transactions()->create([
                    'type' => BalanceTransactionTypeEnum::INITIAL_TOP_UP,
                    'amount' => (int) round($data['balance'] * 100), // сохраняем в копейках
                    'source_type' => null,
                    'source_id' => null,
                ]);
            }
        });

        // Отправляем письмо с паролем через очередь
        if ($user->email && $merchant) {
            SendMerchantWelcomeEmail::dispatch($merchant, $plainPassword);
        }

        return redirect()->route('dashboard.merchants.index')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Merchant created successfully! Phone: ' . $user->phone . ', Password: ' . $plainPassword,
            ]);
    }

    public function topup(Request $request)
    {
        $data = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'amount' => 'required|numeric|min:1',
        ]);


        $merchant = Merchant::findOrFail($request->merchant_id);
        $amount = (int) round($data['amount'] * 100);

        try {
            DB::transaction(function () use ($merchant, $amount) {

                // Пополняем баланс через трейт
                $merchant->credit($amount);

                // Создаём запись в истории транзакций
                $merchant->transactions()->create([
                    'type' => BalanceTransactionTypeEnum::TOP_UP,
                    'amount' => $amount, // сохраняем в копейках
                    'source_type' => null,
                    'source_id' => null,
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

        return response()->json([
            'success' => true,
            'balance' => $merchant->balance,
            'message' => 'Merchant balance updated successfully.'
        ]);
    }

    public function show(Merchant $merchant)
    {
        $merchant->load([
            'transactions',
            'stock_batches.entries.product',
            'user',
            'deliveries.order', // чтобы внутри доставки был заказ
        ]);

        // Последние 10 доставок
        $lastDeliveries = $merchant->deliveries()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.merchants.show', compact('merchant', 'lastDeliveries'));
    }

    public function stock(Merchant $merchant)
    {
        $merchant->load('user');
        $productsData = $merchant->getStockByWarehouse();

        return view('admin.merchants.stock', compact('merchant', 'productsData'));
    }

    public function edit(Merchant $merchant)
    {
        $merchant->load('user');
        return view('admin.merchants.edit', compact('merchant'));
    }

    public function update(Request $request, Merchant $merchant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $merchant->user_id,
            'company' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $merchant->user_id,
            'notes' => 'nullable|string',
            'avatar' => 'nullable|string',
            'id_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        DB::transaction(function () use ($data, $request, $merchant) {
            // Обновляем пользователя
            $merchant->user->update([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
            ]);

            // Обработка загрузки id_card
            $idCardFileName = $merchant->id_card; // Сохраняем старое имя файла

            if ($request->hasFile('id_card')) {
                // Удаляем старый файл, если он существует
                if ($merchant->id_card && Storage::disk('public')->exists('merchants/id_cards/' . $merchant->id_card)) {
                    Storage::disk('public')->delete('merchants/id_cards/' . $merchant->id_card);
                }

                // Загружаем новый файл
                $file = $request->file('id_card');
                $extension = $file->getClientOriginalExtension();
                $idCardFileName = $merchant->id . '.' . $extension;
                $file->storeAs('merchants/id_cards', $idCardFileName, 'public');
            }

            // Обновляем мерчанта
            $merchant->update([
                'company' => $data['company'],
                'address' => $data['address'] ?? null,
                'phone' => $data['phone'] ?? null,
                'avatar' => $data['avatar'] ?? null,
                'notes' => $data['notes'] ?? null,
                'id_card' => $idCardFileName,
            ]);
        });

        return redirect()->route('dashboard.merchants.show', $merchant)
            ->with('alert', [
                'type' => 'success',
                'message' => 'Merchant updated successfully!',
            ]);
    }

    public function toggleStatus(Request $request, Merchant $merchant)
    {
        // Проверяем, что пользователь является мерчантом
        if (!$merchant->user || !$merchant->user->hasRole(UserRoleEnum::MERCHANT->value)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a merchant',
            ], 422);
        }

        $user = $merchant->user;
        $user->update([
            'active' => !$user->active,
        ]);

        return response()->json([
            'success' => true,
            'active' => $user->active,
            'message' => $user->active ? 'Merchant activated successfully' : 'Merchant deactivated successfully',
        ]);
    }

}
