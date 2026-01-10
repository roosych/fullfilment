<?php

namespace App\Exceptions;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $requiredAmount;
    protected $availableBalance;

    public function __construct(int $requiredAmount, int $availableBalance)
    {
        $this->requiredAmount = $requiredAmount;
        $this->availableBalance = $availableBalance;

        $requiredFormatted = number_format($requiredAmount / 100, 2, '.', ' ');
        $availableFormatted = number_format($availableBalance / 100, 2, '.', ' ');

        $message = "Недостаточно средств. Необходимо: {$requiredFormatted} ₼, доступно: {$availableFormatted} ₼";

        parent::__construct($message);
    }

    public function getRequiredAmount(): int
    {
        return $this->requiredAmount;
    }

    public function getAvailableBalance(): int
    {
        return $this->availableBalance;
    }
}

