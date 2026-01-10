<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BalanceTransactionTypeEnum;
use App\Enums\DeliveryStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Exceptions\DeliveryException;
use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryRatePrice;
use App\Models\Order;
use App\Services\OrderStockReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    public function __construct(
        protected OrderStockReservationService $stockReservationService
    ) {
    }
    public function store(Request $request, Order $order)
    {
        // Проверяем доступ к заказу через Policy (админ имеет доступ ко всем заказам)
        $this->authorize('view', $order);

        $activeDelivery = $order->delivery()
            ->whereIn('status', [DeliveryStatusEnum::CREATED, DeliveryStatusEnum::ON_THE_WAY])
            ->first();

        if ($activeDelivery) {
            return redirect()->back()->withErrors(['delivery' => 'Активная доставка уже существует.']);
        }

        if (!$order->status->is(OrderStatusEnum::CREATED)) {
            return redirect()->back()->withErrors(['order' => 'Заказ ещё не готов к созданию доставки.']);
        }

        $validated = $request->validate([
            'weight_input' => 'required|integer|min:1',
        ]);
        $weight = $validated['weight_input'];

        $rateId = $order->delivery_rate_id;
        $zoneId = $order->delivery_zone_id;

        $ratePrices = DeliveryRatePrice::where('delivery_rate_id', $rateId)
            ->where('delivery_zone_id', $zoneId)
            ->where('active', true)
            ->orderBy('min_weight')
            ->get();

        if ($ratePrices->isEmpty()) {
            return redirect()->back()->withErrors(['tariff' => 'Тарифные значения не найдены для этой зоны.']);
        }

        $found = $ratePrices->first(function ($rp) use ($weight) {
            return $weight >= $rp->min_weight && $weight <= $rp->max_weight;
        });

        if (!$found) {
            $maxWeight = $ratePrices->max('max_weight');
            return redirect()->back()->withErrors(['weight' => "Максимальный вес для этого тарифа: {$maxWeight} г"]);
        }

        $merchant = $order->merchant;

        if (!$merchant) {
            return redirect()->back()->withErrors(['merchant' => 'Мерчант не найден.']);
        }

        $deliveryPrice = $found->price;

        if (!$merchant->hasEnoughBalance($deliveryPrice)) {
            throw new InsufficientBalanceException(
                $deliveryPrice,
                $merchant->available_balance
            );
        }

        try {
            DB::beginTransaction();

            // Обновляем статус заказа
            $order->update([
                'status' => OrderStatusEnum::READY_FOR_DELIVERY,
            ]);

            // Создаем доставку
            $delivery = $order->delivery()->create([
                'delivery_zone_id' => $order->delivery_zone_id,
                'delivery_rate_id' => $order->delivery_rate_id,
                'weight' => $weight,
                'price' => $deliveryPrice,
                'notes' => $order->notes,
                'status' => DeliveryStatusEnum::CREATED,
            ]);

            // Резервируем средства
            $merchant->reserve($deliveryPrice);

            // Создаём транзакцию резервирования
            $merchant->transactions()->create([
                'type' => BalanceTransactionTypeEnum::DELIVERY_RESERVED,
                'amount' => -$deliveryPrice,
                'source_type' => Delivery::class,
                'source_id' => $delivery->id,
            ]);

            DB::commit();

            return redirect()->route('dashboard.order.show', $order)
                ->with('success', 'Доставка создана! Средства зарезервированы.');

        } catch (InsufficientBalanceException $e) {
            DB::rollBack();

            Log::warning('Delivery creation failed: insufficient balance', [
                'order_id' => $order->id,
                'merchant_id' => $merchant->id,
                'required' => $e->getRequiredAmount(),
                'available' => $e->getAvailableBalance(),
            ]);

            return redirect()->back()->withErrors(['balance' => $e->getMessage()]);

        } catch (DeliveryException $e) {
            DB::rollBack();

            Log::error('Delivery creation failed: delivery exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['delivery' => $e->getMessage()]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Delivery creation failed: unexpected error', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Ошибка при создании доставки. Пожалуйста, попробуйте снова.']);
        }
    }

    /**
     * Отправить доставку в путь
     * DeliveryStatus: CREATED → ON_THE_WAY
     * OrderStatus: READY_FOR_DELIVERY → DELIVERY_IN_PROGRESS
     */
    public function startDelivery(Delivery $delivery)
    {
        // Проверяем доступ через Policy
        $this->authorize('startDelivery', $delivery);

        if ($delivery->status !== DeliveryStatusEnum::CREATED) {
            return response()->json([
                'success' => false,
                'message' => 'Можно отправить в путь только доставку в статусе "Created".'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Обновляем статус доставки
            $delivery->update([
                'status' => DeliveryStatusEnum::ON_THE_WAY,
                'started_at' => now(),
            ]);

            // Обновляем статус заказа
            $delivery->order->update([
                'status' => OrderStatusEnum::DELIVERY_IN_PROGRESS,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Доставка отправлена в путь!'
            ]);

        } catch (DeliveryException $e) {
            DB::rollBack();

            Log::error('Delivery start failed: delivery exception', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Delivery start failed: unexpected error', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке доставки. Пожалуйста, попробуйте снова.'
            ], 500);
        }
    }

    /**
     * Завершение доставки
     * DeliveryStatus: CREATED/ON_THE_WAY → DELIVERED
     * OrderStatus: → DELIVERED
     *
     * CREATED → DELIVERED: освобождение резерва (без списания)
     * ON_THE_WAY → DELIVERED: списание средств
     */
    public function complete(Delivery $delivery)
    {
        // Проверяем доступ через Policy
        $this->authorize('complete', $delivery);

        if (!in_array($delivery->status, [DeliveryStatusEnum::CREATED, DeliveryStatusEnum::ON_THE_WAY])) {
            return response()->json([
                'success' => false,
                'message' => 'Доставка уже завершена или отменена.'
            ], 400);
        }

        $merchant = $delivery->order->merchant;

        try {
            DB::beginTransaction();

            // Если доставка была ON_THE_WAY - списываем средства
            if ($delivery->status === DeliveryStatusEnum::ON_THE_WAY) {

                $delivery->update([
                    'status' => DeliveryStatusEnum::DELIVERED,
                    'delivered_at' => now(),
                ]);

                // Списываем средства из резерва и баланса
                $merchant->debitFromReserve($delivery->price);

                // Создаём транзакцию оплаты доставки
                $merchant->transactions()->create([
                    'type' => BalanceTransactionTypeEnum::DELIVERY_PAYMENT,
                    'amount' => -$delivery->price,
                    'source_type' => Delivery::class,
                    'source_id' => $delivery->id,
                ]);

                $message = 'Доставка завершена! Средства списаны.';

            } else {
                // Если доставка была CREATED - освобождаем резерв без списания

                $delivery->update([
                    'status' => DeliveryStatusEnum::DELIVERED,
                    'delivered_at' => now(),
                ]);

                // Освобождаем резерв (деньги остаются у мерчанта)
                $merchant->releaseReserve($delivery->price);

                // Создаём транзакцию возврата резерва
                $merchant->transactions()->create([
                    'type' => BalanceTransactionTypeEnum::REFUND,
                    'amount' => $delivery->price,
                    'source_type' => Delivery::class,
                    'source_id' => $delivery->id,
                ]);

                $message = 'Доставка завершена! Резерв освобождён без списания.';
            }

            // Обновляем статус заказа
            $order = $delivery->order;
            $order->update([
                'status' => OrderStatusEnum::DELIVERED,
            ]);

            // Списываем товары (снимаем резерв и уменьшаем remaining_quantity)
            $this->stockReservationService->deductStockOnDelivery($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (DeliveryException $e) {
            DB::rollBack();

            Log::error('Delivery complete failed: delivery exception', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Delivery complete failed: unexpected error', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при завершении доставки. Пожалуйста, попробуйте снова.'
            ], 500);
        }
    }

    /**
     * Отмена доставки
     * DeliveryStatus: CREATED → RETURNED
     * OrderStatus: → CREATED
     */
    public function cancel(Delivery $delivery)
    {
        // Проверяем доступ через Policy
        $this->authorize('cancel', $delivery);

        if (! $delivery->status->isCancellable()) {
            return response()->json([
                'success' => false,
                'message' => 'Можно отменить только доставку в статусе "Created".'
            ], 400);
        }

        $merchant = $delivery->order->merchant;

        try {
            DB::beginTransaction();

            // Обновляем статус доставки
            $delivery->update([
                'status' => DeliveryStatusEnum::RETURNED,
            ]);

            // Освобождаем резерв
            $merchant->releaseReserve($delivery->price);

            // Создаём транзакцию возврата резерва
            $merchant->transactions()->create([
                'type' => BalanceTransactionTypeEnum::REFUND,
                'amount' => $delivery->price,
                'source_type' => Delivery::class,
                'source_id' => $delivery->id,
            ]);

            // Возвращаем заказ в статус CREATED
            $delivery->order->update([
                'status' => OrderStatusEnum::CREATED,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Доставка отменена. Резерв освобождён.'
            ]);

        } catch (DeliveryException $e) {
            DB::rollBack();

            Log::error('Delivery cancel failed: delivery exception', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Delivery cancel failed: unexpected error', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отмене доставки. Пожалуйста, попробуйте снова.'
            ], 500);
        }
    }

    /**
     * Пометить доставку как неудачную
     * DeliveryStatus: ON_THE_WAY → FAILED
     * OrderStatus: → CANCELLED
     */
    public function fail(Delivery $delivery)
    {
        // Проверяем доступ через Policy
        $this->authorize('fail', $delivery);

        if ($delivery->status !== DeliveryStatusEnum::ON_THE_WAY) {
            return response()->json([
                'success' => false,
                'message' => 'Можно пометить как неудачную только доставку в пути.'
            ], 400);
        }

        $merchant = $delivery->order->merchant;

        try {
            DB::beginTransaction();

            // Обновляем статус доставки
            $delivery->update([
                'status' => DeliveryStatusEnum::FAILED,
                'failed_at' => now(),
            ]);

            // Освобождаем резерв (не списываем деньги)
            $merchant->releaseReserve($delivery->price);

            // Создаём транзакцию возврата
            $merchant->transactions()->create([
                'type' => BalanceTransactionTypeEnum::REFUND,
                'amount' => $delivery->price,
                'source_type' => Delivery::class,
                'source_id' => $delivery->id,
            ]);

            // Обновляем статус заказа на CANCELLED
            $delivery->order->update([
                'status' => OrderStatusEnum::CANCELLED,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Доставка помечена как неудачная. Резерв освобождён.'
            ]);

        } catch (DeliveryException $e) {
            DB::rollBack();

            Log::error('Delivery fail failed: delivery exception', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Delivery fail failed: unexpected error', [
                'delivery_id' => $delivery->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обработке доставки. Пожалуйста, попробуйте снова.'
            ], 500);
        }
    }
}
