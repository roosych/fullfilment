<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view the order.
     * Админ может просматривать все заказы, мерчант - только свои.
     */
    public function view(User $user, Order $order): bool
    {
        // Админ имеет доступ ко всем заказам
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может просматривать только свои заказы
        if ($user->hasRole('merchant') && $user->merchant) {
            return $order->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('merchant');
    }

    /**
     * Determine if the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('merchant');
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Админ может обновлять все заказы
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может обновлять только свои заказы
        if ($user->hasRole('merchant') && $user->merchant) {
            return $order->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Админ может удалять все заказы
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может удалять только свои заказы
        if ($user->hasRole('merchant') && $user->merchant) {
            return $order->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // Админ может отменять все заказы
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может отменять только свои заказы
        if ($user->hasRole('merchant') && $user->merchant) {
            return $order->merchant_id === $user->merchant->id;
        }

        return false;
    }
}

