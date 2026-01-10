<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Models\DeliveryRate;
use App\Models\DeliveryRatePrice;
use App\Models\Merchant;
use App\Models\Order;

class OrderBalanceService
{
    /**
     * Проверяет достаточность баланса для создания заказа
     * Рассчитывает примерную стоимость доставки на основе тарифа и зоны
     *
     * @param Merchant $merchant
     * @param int $deliveryRateId
     * @param int $deliveryZoneId
     * @param int|null $estimatedWeight Оценочный вес в граммах (опционально)
     * @return void
     * @throws InsufficientBalanceException
     */
    public function checkBalanceForOrder(
        Merchant $merchant,
        int $deliveryRateId,
        int $deliveryZoneId,
        ?int $estimatedWeight = null
    ): void {
        // Получаем минимальную стоимость доставки для данной зоны и тарифа
        $minPrice = $this->getMinDeliveryPrice($deliveryRateId, $deliveryZoneId);

        if (!$merchant->hasEnoughBalance($minPrice)) {
            throw new InsufficientBalanceException(
                $minPrice,
                $merchant->available_balance
            );
        }
    }

    /**
     * Получить минимальную стоимость доставки для тарифа и зоны
     *
     * @param int $deliveryRateId
     * @param int $deliveryZoneId
     * @return int Стоимость в копейках
     */
    protected function getMinDeliveryPrice(int $deliveryRateId, int $deliveryZoneId): int
    {
        $ratePrice = DeliveryRatePrice::where('delivery_rate_id', $deliveryRateId)
            ->where('delivery_zone_id', $deliveryZoneId)
            ->where('active', true)
            ->orderBy('min_weight')
            ->first();

        if (!$ratePrice) {
            // Если тариф не найден, возвращаем минимальную стоимость по умолчанию
            // Можно также выбросить исключение, но для проверки баланса используем минимальное значение
            return 0;
        }

        return $ratePrice->price;
    }

    /**
     * Рассчитывает точную стоимость доставки на основе веса
     *
     * @param int $deliveryRateId
     * @param int $deliveryZoneId
     * @param int $weight Вес в граммах
     * @return int Стоимость в копейках
     * @throws \Exception Если тариф не найден
     */
    public function calculateDeliveryPrice(int $deliveryRateId, int $deliveryZoneId, int $weight): int
    {
        $ratePrices = DeliveryRatePrice::where('delivery_rate_id', $deliveryRateId)
            ->where('delivery_zone_id', $deliveryZoneId)
            ->where('active', true)
            ->orderBy('min_weight')
            ->get();

        if ($ratePrices->isEmpty()) {
            throw new \Exception('Тарифные значения не найдены для этой зоны.');
        }

        $found = $ratePrices->first(function ($rp) use ($weight) {
            return $weight >= $rp->min_weight && $weight <= $rp->max_weight;
        });

        if (!$found) {
            throw new \Exception("Вес {$weight} г не попадает в диапазон тарифа.");
        }

        return $found->price;
    }
}

