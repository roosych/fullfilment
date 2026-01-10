<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $productName;
    protected $available;
    protected $requested;

    public function __construct(string $productName, int $available, int $requested)
    {
        $this->productName = $productName;
        $this->available = $available;
        $this->requested = $requested;

        $message = "Недостаточно товара: {$productName}. Доступно: {$available}, требуется: {$requested}";

        parent::__construct($message);
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getAvailable(): int
    {
        return $this->available;
    }

    public function getRequested(): int
    {
        return $this->requested;
    }
}

