<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case CREATED = 'created';                // Мерчант создал заказ
    case READY_FOR_DELIVERY = 'ready_for_delivery'; // Собран, взвешен, готов к доставке
    case DELIVERY_IN_PROGRESS = 'delivery_in_progress'; // Курьер забрал
    case DELIVERED = 'delivered';            // Доставлен
    case CANCELLED = 'cancelled';            // Отменён
    //  статус возврат товара

    // админ должен уметь создавать возврат или обмену товара из какого либо заказа и создавать
    // новую доставку по этому заказу и отмечать товар (синх со складом). стоимость этой доставки равна
    // стоимости доставки того самого заказа

    // создан, собран взвешен и готов к отправке, в пути (деньги с баланса списались), доставлен, отменен
    // Отменить заказ можно в статусах всех кроме доставлен, отменен

    public function label(): string
    {
        return match($this) {
            self::CREATED => 'Создан',
            self::READY_FOR_DELIVERY => 'Готов к доставке',
            self::DELIVERY_IN_PROGRESS => 'В пути',
            self::DELIVERED => 'Доставлен',
            self::CANCELLED => 'Отменен',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::CREATED => 'warning',
            self::READY_FOR_DELIVERY => 'info',
            self::DELIVERY_IN_PROGRESS => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function is(OrderStatusEnum ...$statuses): bool
    {
        return in_array($this, $statuses);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::CREATED,
            self::READY_FOR_DELIVERY,
        ]);
    }
}
