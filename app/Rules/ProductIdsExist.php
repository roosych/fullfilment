<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ProductIdsExist implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // $value — это весь массив products
        $productIds = array_keys($value);

        $exists = Product::whereIn('id', $productIds)->count();

        if ($exists !== count($productIds)) {
            $fail('Некоторые товары отсутствуют в базе.');
        }
    }
}
