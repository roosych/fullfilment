<?php

namespace App\Traits;

use InvalidArgumentException;
use RuntimeException;

trait HasBalance
{
    /**
     * Проверка достаточности доступных средств (баланс - резерв)
     */
    public function hasEnoughBalance(int $amount): bool
    {
        $availableBalance = $this->balance - ($this->reserved_balance ?? 0);
        return $availableBalance >= $amount;
    }

    /**
     * Получить доступный баланс
     */
    public function getAvailableBalanceAttribute(): int
    {
        return $this->balance - ($this->reserved_balance ?? 0);
    }

    /**
     * Пополнение баланса
     */
    public function credit(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Сумма пополнения должна быть положительной.");
        }

        $this->increment('balance', $amount);
    }

    /**
     * Списание из баланса
     */
    public function debit(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Сумма списания должна быть положительной.");
        }

        if ($this->balance < $amount) {
            throw new RuntimeException("Недостаточно средств для списания.");
        }

        $this->decrement('balance', $amount);
    }

    /**
     * Резервирование средств
     */
    public function reserve(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Сумма резервирования должна быть положительной.");
        }

        $availableBalance = $this->balance - ($this->reserved_balance ?? 0);
        if ($availableBalance < $amount) {
            throw new RuntimeException("Недостаточно доступных средств для резервирования.");
        }

        $this->increment('reserved_balance', $amount);
    }

    /**
     * Освобождение резерва
     */
    public function releaseReserve(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Сумма освобождения должна быть положительной.");
        }

        $this->decrement('reserved_balance', $amount);
    }

    /**
     * Списание из резерва и баланса одновременно
     */
    public function debitFromReserve(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Сумма списания должна быть положительной.");
        }

        $this->decrement('reserved_balance', $amount);
        $this->decrement('balance', $amount);
    }
}
