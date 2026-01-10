<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\StoreOrderRequest;
use App\Models\DeliveryRate;
use App\Models\DeliveryRatePrice;
use App\Models\Merchant;
use App\Models\Order;
use App\Services\OrderBalanceService;
use App\Services\OrderStockReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderStockReservationService $stockReservationService,
        protected OrderBalanceService $balanceService
    ) {
    }

    public function index()
    {
        $orders = Order::all();
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $tariffs = DeliveryRate::with('zones.ratePrices')->get();
        $merchants = Merchant::all();

        // Подгружаем товары с остатком > 0 для первого мерчанта
        $firstMerchant = $merchants->first();
//        $products = $firstMerchant
//            ? $firstMerchant->products()->whereHas('stockEntries', function($q) {
//                $q->where('remaining_quantity', '>', 0);
//            })->get()
//            : collect();

        $products = $firstMerchant
            ? $firstMerchant->products()->get()->map(function ($product) {
                $availableStock = $product->stockEntries()->sum(DB::raw('remaining_quantity - reserved_quantity'));
                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'sku'   => $product->sku,
                    'available_stock' => $availableStock,
                ];
            })->filter(fn($p) => $p['available_stock'] > 0) // оставляем только доступные
            : collect();

        return view('admin.orders.create', compact('merchants', 'tariffs', 'products'));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $merchant = Merchant::where('uuid', $data['merchant_id'])->firstOrFail();

        try {
            DB::beginTransaction();

            // Проверяем баланс мерчанта перед созданием заказа
            $this->balanceService->checkBalanceForOrder(
                $merchant,
                $data['tariff_id'],
                $data['zone_id']
            );

            // Создаем заказ
            $order = Order::create([
                'merchant_id'       => $merchant->id,
                'created_by_id'     => auth()->id(),
                'delivery_zone_id'  => $data['zone_id'],
                'delivery_rate_id'  => $data['tariff_id'],
                'recipient_name'    => $data['recipient_name'],
                'recipient_phone'   => $data['recipient_phone'],
                'recipient_address' => $data['recipient_address'],
                'notes'             => $data['notes'] ?? null,
                'status'            => OrderStatusEnum::CREATED,
            ]);

            // Резервируем товары с защитой от race condition
            $this->stockReservationService->reserveStockForOrder($order, $data['products']);

            DB::commit();

            return redirect()->route('dashboard.order.show', $order)
                ->with('success', 'Заказ успешно создан и количество зарезервировано.');

        } catch (InsufficientBalanceException $e) {
            DB::rollBack();

            Log::warning('Order creation failed: insufficient balance', [
                'merchant_id' => $merchant->id,
                'required' => $e->getRequiredAmount(),
                'available' => $e->getAvailableBalance(),
            ]);

            return back()
                ->withErrors(['balance' => $e->getMessage()])
                ->withInput();

        } catch (InsufficientStockException $e) {
            DB::rollBack();

            Log::warning('Order creation failed: insufficient stock', [
                'merchant_id' => $merchant->id,
                'product' => $e->getProductName(),
                'available' => $e->getAvailable(),
                'requested' => $e->getRequested(),
            ]);

            return back()
                ->withErrors(['products' => $e->getMessage()])
                ->withInput();

        } catch (OrderException $e) {
            DB::rollBack();

            Log::error('Order creation failed: order exception', [
                'merchant_id' => $merchant->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['order' => $e->getMessage()])
                ->withInput();

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Order creation failed: unexpected error', [
                'merchant_id' => $merchant->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
            ]);

            return back()
                ->withErrors(['error' => 'Произошла ошибка при создании заказа. Пожалуйста, попробуйте снова.'])
                ->withInput();
        }
    }

    public function show(Order $order)
    {
        // Проверяем доступ через Policy (админ имеет доступ ко всем заказам)
        $this->authorize('view', $order);

        // Загружаем все связи
        $order->load([
            'creator',
            'merchant',
            'items.product',
            'delivery.courier',
            'delivery.zone',
            'delivery.rate',
            'rate',
            'zone',
        ]);

        //dd($order);

        return view('admin.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        // Проверяем доступ через Policy
        $this->authorize('cancel', $order);

        if (!$order->status->canBeCancelled()) {
            return back()->withErrors(['order' => 'Этот заказ нельзя отменить.']);
        }

        try {
            DB::beginTransaction();

            // Если у заказа есть доставка — проверяем её статус
            if ($order->delivery) {
                // Доставку можно отменять только если она не началась
                if (!$order->delivery->status->isCancellable()) {
                    DB::rollBack();
                    return back()->withErrors(['delivery' => 'Доставка уже началась. Заказ нельзя отменить.']);
                }

                // Отмена доставки
                $order->delivery->update([
                    'status' => DeliveryStatusEnum::FAILED,
                ]);
            }

            // Освобождаем резерв товара через сервис
            $this->stockReservationService->releaseStockReservation($order);

            // Обновляем статус заказа
            $order->update([
                'status' => OrderStatusEnum::CANCELLED,
            ]);

            DB::commit();

            return redirect()
                ->route('dashboard.order.index')
                ->with('success', 'Заказ успешно отменён.');

        } catch (OrderException $e) {
            DB::rollBack();

            Log::error('Order cancel failed: order exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['order' => $e->getMessage()]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Order cancel failed: unexpected error', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Ошибка при отмене заказа. Пожалуйста, попробуйте снова.']);
        }
    }

    public function getMerchantProducts($merchantUuid)
    {
        $merchant = Merchant::where('uuid', $merchantUuid)->firstOrFail();

        $products = $merchant->products()->get()->map(function ($p) {
            $totalStock = $p->stockEntries()->sum('remaining_quantity');
            $reservedStock = $p->stockEntries()->sum('reserved_quantity');
            $availableStock = $totalStock - $reservedStock;

            return [
                'id'       => $p->id,
                'name'     => $p->name,
                'sku'      => $p->sku,
                'stock'    => $availableStock,
                'reserved' => $reservedStock,
            ];
        })->filter(function($p) {  // Добавь это
            return $p['stock'] > 0;
        })->values();

        // Генерируем HTML
        $html = view('partials.admin.orders.products_table_row', [
            'products' => $products,
            'oldData'  => session()->get('old_products_data', [])
        ])->render();

        return response()->json([
            'products' => $products,
            'html'     => $html
        ]);
    }

    public function calculatePrice(Request $request)
    {
        $request->validate([
            'weight'   => 'required|integer|min:1',
            'rate_id'  => 'required|integer|exists:delivery_rates,id',
            'zone_id'  => 'required|integer|exists:delivery_zones,id',
        ]);

        $weight = $request->input('weight');
        $rateId = $request->input('rate_id');
        $zoneId = $request->input('zone_id');

        // Получаем тарифные диапазоны для зоны и тарифа
        $ratePrice = DeliveryRatePrice::where('delivery_rate_id', $rateId)
            ->where('delivery_zone_id', $zoneId)
            ->where('active', true)
            ->orderBy('min_weight')
            ->get();

        if ($ratePrice->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Тарифные значения не найдены для этой зоны.'
            ]);
        }

        // Находим подходящий диапазон
        $found = $ratePrice->first(function($rp) use ($weight) {
            return $weight >= $rp->min_weight && $weight <= $rp->max_weight;
        });

        // Если превышает максимальный
        if (!$found) {
            $maxWeight = $ratePrice->max('max_weight');
            $maxPrice = $ratePrice->firstWhere('max_weight', $maxWeight)->price_formatted;

            return response()->json([
                'success' => false,
                'message' => "Максимальный вес для этого тарифа: {$maxWeight} г",
                'price_formatted' => $maxPrice
            ]);
        }

        return response()->json([
            'success' => true,
            'price_formatted' => $found->price_formatted
        ]);
    }
}
