<?php

namespace App\Enums;

enum BalanceTransactionTypeEnum: string
{
    case TOP_UP = 'top_up';           // Пополнение
    case WITHDRAWAL = 'withdrawal';   // Снятие
    case DELIVERY_PAYMENT = 'delivery_payment'; // Оплата заказа
    case DELIVERY_RESERVED = 'delivery_reserved'; // Резерв для доставки
    case REFUND = 'refund';           // Возврат
    case INITIAL_TOP_UP = 'initial_top_up'; // Первичное пополнение

    public function label(): string
    {
        return match ($this) {
            self::TOP_UP => 'Top Up',
            self::WITHDRAWAL => 'Withdrawal',
            self::DELIVERY_PAYMENT => 'Delivery Payment',
            self::DELIVERY_RESERVED => 'Delivery Reserved',
            self::REFUND => 'Refund',
            self::INITIAL_TOP_UP => 'Initial Top Up',
        };
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::TOP_UP, self::INITIAL_TOP_UP => 'success',
            self::WITHDRAWAL => 'danger',
            self::DELIVERY_PAYMENT => 'primary',
            self::DELIVERY_RESERVED => 'info',
            self::REFUND => 'warning',
        };
    }
}
