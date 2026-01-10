<?php

namespace App\Enums;

enum DeliveryStatusEnum: string
{
    case CREATED = 'created';
    case ON_THE_WAY = 'on_the_way';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case RETURNED = 'returned';

    
   // доставку отменить может только админ. но бабки всеравно спишуся кроме статуса Создан

    public function label(): string
    {
        return match($this) {
            self::CREATED => 'Создана',
            self::ON_THE_WAY => 'В пути',
            self::DELIVERED => 'Доставлена',
            self::FAILED => 'Неудачная',
            self::RETURNED => 'Возвращена',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::CREATED => 'warning',
            self::ON_THE_WAY => 'primary',
            self::DELIVERED => 'success',
            self::FAILED => 'danger',
            self::RETURNED => 'danger',
        };
    }

    public function is(DeliveryStatusEnum ...$statuses): bool
    {
        return in_array($this, $statuses);
    }

    public function isCancellable(): bool
    {
        return !$this->is(
            self::FAILED,
            self::RETURNED,
            self::ON_THE_WAY,
            self::DELIVERED,
        );
    }
}
