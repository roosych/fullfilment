<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine if the user can view the product.
     * Админ может просматривать все товары, мерчант - только свои.
     */
    public function view(User $user, Product $product): bool
    {
        // Админ имеет доступ ко всем товарам
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может просматривать только свои товары
        if ($user->hasRole('merchant') && $user->merchant) {
            return $product->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('merchant');
    }

    /**
     * Determine if the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('merchant');
    }

    /**
     * Determine if the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        // Админ может обновлять все товары
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может обновлять только свои товары
        if ($user->hasRole('merchant') && $user->merchant) {
            return $product->merchant_id === $user->merchant->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        // Админ может удалять все товары
        if ($user->hasRole('admin')) {
            return true;
        }

        // Мерчант может удалять только свои товары
        if ($user->hasRole('merchant') && $user->merchant) {
            return $product->merchant_id === $user->merchant->id;
        }

        return false;
    }
}

