<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockEntry;
use Illuminate\Support\Facades\DB;

class OrderStockReservationService
{
    /**
     * Резервирует товары для заказа с защитой от race condition
     *
     * @param Order $order
     * @param array $productsData Массив с данными товаров: [['product_id' => int, 'quantity' => int], ...]
     * @return void
     * @throws InsufficientStockException
     */
    public function reserveStockForOrder(Order $order, array $productsData): void
    {
        DB::transaction(function () use ($order, $productsData) {
            foreach ($productsData as $item) {
                $productId = $item['product_id'];
                $qtyToReserve = $item['quantity'];

                // Блокируем продукт и его stock entries для обновления
                // Это предотвращает race condition при одновременных заказах
                $product = Product::lockForUpdate()
                    ->findOrFail($productId);

                // Проверяем доступное количество с блокировкой записей
                $available = $this->getAvailableStock($product);

                if ($available < $qtyToReserve) {
                    throw new InsufficientStockException(
                        $product->name,
                        $available,
                        $qtyToReserve
                    );
                }

                // Создаем позицию заказа
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qtyToReserve,
                ]);

                // Резервируем товар по складам (FIFO)
                $this->reserveStockEntries($product, $qtyToReserve);
            }
        });
    }

    /**
     * Получить доступное количество товара (remaining - reserved)
     * С блокировкой записей для предотвращения race condition
     *
     * @param Product $product
     * @return int
     */
    protected function getAvailableStock(Product $product): int
    {
        // Блокируем все записи склада для этого товара
        $entries = StockEntry::lockForUpdate()
            ->where('product_id', $product->id)
            ->whereRaw('remaining_quantity - reserved_quantity > 0')
            ->get();

        return $entries->sum(function ($entry) {
            return $entry->remaining_quantity - $entry->reserved_quantity;
        });
    }

    /**
     * Резервирует товар по складам (FIFO - First In First Out)
     *
     * @param Product $product
     * @param int $qtyToReserve
     * @return void
     */
    protected function reserveStockEntries(Product $product, int $qtyToReserve): void
    {
        // Получаем записи с блокировкой, отсортированные по дате истечения (FIFO)
        $entries = StockEntry::lockForUpdate()
            ->where('product_id', $product->id)
            ->whereRaw('remaining_quantity - reserved_quantity > 0')
            ->orderBy('expires_at')
            ->orderBy('created_at')
            ->get();

        $remainingQty = $qtyToReserve;

        foreach ($entries as $entry) {
            if ($remainingQty <= 0) {
                break;
            }

            // Вычисляем сколько можем зарезервировать из этой записи
            $availableInEntry = $entry->remaining_quantity - $entry->reserved_quantity;
            $canReserve = min($availableInEntry, $remainingQty);

            if ($canReserve > 0) {
                // Обновляем резерв с проверкой на уровне БД
                // increment() возвращает количество обновленных строк (0 или 1)
                $updatedRows = StockEntry::where('id', $entry->id)
                    ->whereRaw('remaining_quantity - reserved_quantity >= ?', [$canReserve])
                    ->increment('reserved_quantity', $canReserve);

                if ($updatedRows > 0) {
                    // Успешно зарезервировали
                    $remainingQty -= $canReserve;
                } else {
                    // Если не удалось обновить, значит данные изменились
                    // Пересчитываем доступное количество с блокировкой
                    $entry->refresh();
                    $availableInEntry = $entry->remaining_quantity - $entry->reserved_quantity;
                    $canReserve = min($availableInEntry, $remainingQty);

                    if ($canReserve > 0) {
                        $updatedRows = StockEntry::where('id', $entry->id)
                            ->whereRaw('remaining_quantity - reserved_quantity >= ?', [$canReserve])
                            ->increment('reserved_quantity', $canReserve);

                        if ($updatedRows > 0) {
                            $remainingQty -= $canReserve;
                        }
                    }
                }
            }
        }

        // Если не удалось зарезервировать всё количество
        if ($remainingQty > 0) {
            throw new InsufficientStockException(
                $product->name,
                $qtyToReserve - $remainingQty,
                $qtyToReserve
            );
        }
    }

    /**
     * Освобождает резерв товара при отмене заказа
     *
     * @param Order $order
     * @return void
     */
    public function releaseStockReservation(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                $qtyToReturn = $item->quantity;

                // Блокируем записи склада для обновления
                $stockEntries = StockEntry::lockForUpdate()
                    ->where('product_id', $product->id)
                    ->where('reserved_quantity', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($stockEntries as $entry) {
                    if ($qtyToReturn <= 0) {
                        break;
                    }

                    $canReturn = min($entry->reserved_quantity, $qtyToReturn);

                    if ($canReturn > 0) {
                        // Снимаем резерв и возвращаем количество
                        $entry->decrement('reserved_quantity', $canReturn);
                        $entry->increment('remaining_quantity', $canReturn);
                        $qtyToReturn -= $canReturn;
                    }
                }

                // Логируем предупреждение, если не удалось вернуть весь товар
                if ($qtyToReturn > 0) {
                    \Log::warning("Order {$order->id}: не удалось вернуть {$qtyToReturn} шт. товара {$product->id}", [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'unreturned_quantity' => $qtyToReturn,
                    ]);
                }
            }
        });
    }

    /**
     * Списывает товары при завершении доставки
     * Снимает резерв и уменьшает remaining_quantity (товар фактически продан/отгружен)
     *
     * @param Order $order
     * @return void
     */
    public function deductStockOnDelivery(Order $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = $item->product;
                $qtyToDeduct = $item->quantity;

                // Блокируем записи склада для обновления
                $stockEntries = StockEntry::lockForUpdate()
                    ->where('product_id', $product->id)
                    ->where('reserved_quantity', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($stockEntries as $entry) {
                    if ($qtyToDeduct <= 0) {
                        break;
                    }

                    $canDeduct = min($entry->reserved_quantity, $qtyToDeduct);

                    if ($canDeduct > 0) {
                        // Снимаем резерв и уменьшаем оставшееся количество (товар продан)
                        $entry->decrement('reserved_quantity', $canDeduct);
                        $entry->decrement('remaining_quantity', $canDeduct);
                        $qtyToDeduct -= $canDeduct;
                    }
                }

                // Логируем предупреждение, если не удалось списать весь товар
                if ($qtyToDeduct > 0) {
                    \Log::warning("Order {$order->id}: не удалось списать {$qtyToDeduct} шт. товара {$product->id} при доставке", [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'undeducted_quantity' => $qtyToDeduct,
                    ]);
                }
            }
        });
    }
}

