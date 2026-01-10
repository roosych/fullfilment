<?php

namespace App\Policies;

use App\Models\StockBatch;
use App\Models\User;

class StockBatchPolicy
{
    /**
     * Determine if the user can view the stock batch.
     * Админ может просматривать все батчи, мерчант - только свои.
     */
    public function view(User $user, StockBatch $stockBatch): bool
    {
        // Админ имеет доступ ко всем батчам
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может просматривать только свои батчи
        if ($user->hasRole('merchant') && $user->merchant) {
            return $stockBatch->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can view any stock batches.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('merchant');
    }
}

