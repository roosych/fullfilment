<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    /**
     * Determine if the user can view the delivery.
     * Админ может просматривать все доставки, мерчант - только свои (через заказ).
     */
    public function view(User $user, Delivery $delivery): bool
    {
        // Админ имеет доступ ко всем доставкам
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может просматривать только доставки своих заказов
        if ($user->hasRole('merchant') && $user->merchant) {
            // Загружаем связь с заказом, если еще не загружена
            if (!$delivery->relationLoaded('order')) {
                $delivery->load('order');
            }

            return $delivery->order && $delivery->order->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can view any deliveries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('merchant');
    }

    /**
     * Determine if the user can create deliveries.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the delivery.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        // Админ может обновлять все доставки
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может обновлять только доставки своих заказов
        if ($user->hasRole('merchant') && $user->merchant) {
            if (!$delivery->relationLoaded('order')) {
                $delivery->load('order');
            }

            return $delivery->order && $delivery->order->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the delivery.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        // Админ может удалять все доставки
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может удалять только доставки своих заказов
        if ($user->hasRole('merchant') && $user->merchant) {
            if (!$delivery->relationLoaded('order')) {
                $delivery->load('order');
            }

            return $delivery->order && $delivery->order->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can start the delivery.
     */
    public function startDelivery(User $user, Delivery $delivery): bool
    {
        // Только админ может запускать доставки
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can complete the delivery.
     */
    public function complete(User $user, Delivery $delivery): bool
    {
        // Только админ может завершать доставки
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can cancel the delivery.
     */
    public function cancel(User $user, Delivery $delivery): bool
    {
        // Только админ может отменять доставки
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can fail the delivery.
     */
    public function fail(User $user, Delivery $delivery): bool
    {
        // Только админ может помечать доставки как неудачные
        return $user->hasRole('admin');
    }
}

