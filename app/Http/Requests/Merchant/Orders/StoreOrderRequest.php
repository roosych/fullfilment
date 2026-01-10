<?php

namespace App\Http\Requests\Merchant\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('merchant') && auth()->user()->merchant;
    }

    protected function prepareForValidation(): void
    {
        // Фильтруем пустые продукты (где quantity не заполнено)
        $products = collect($this->products ?? [])
            ->filter(fn($item) => !empty($item['quantity']))
            ->all();

        $this->merge(['products' => $products]);
    }

    public function rules(): array
    {
        $merchant = auth()->user()->merchant;

        return [
            'tariff_id' => ['required', 'exists:delivery_rates,id'],
            'zone_id' => ['required', 'exists:delivery_zones,id'],

            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('merchant_id', $merchant->id)
            ],
            'products.*.quantity' => ['required', 'integer', 'min:1'],

            'recipient_address' => ['required', 'string', 'max:255'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'Please select at least one product.',
            'products.min' => 'Please select at least one product.',
            'products.*.quantity.required' => 'Please specify quantity for each selected product.',
            'products.*.quantity.min' => 'Quantity must be at least 1.',
            'recipient_address.required' => 'Please enter the delivery address.',
            'recipient_name.required' => 'Please enter the recipient name.',
            'recipient_phone.required' => 'Please enter the recipient phone.',
        ];
    }
}

