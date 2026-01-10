<?php

namespace App\Enums;

enum UserRoleEnum :string
{
    case ADMIN = 'admin';
    case MERCHANT = 'merchant';
    case RECEIVER = 'receiver';

    public function is(self $role): bool
    {
        return $this === $role;
    }

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::MERCHANT => 'Merchant',
            self::RECEIVER => 'Receiver',
        };
    }

    // Получить все значения enum
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Получить все лейблы enum
    public static function getAllLabels(): array
    {
        return array_map(fn(self $case) => $case->label(), self::cases());
    }
}
