<?php

namespace App\Http\Requests\Admin\Orders;

use App\Models\Merchant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
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
        return [
            'merchant_id' => ['required', 'exists:merchants,uuid'],
            'tariff_id' => ['required', 'exists:delivery_rates,id'],
            'zone_id' => ['required', 'exists:delivery_zones,id'],

            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id')->where('merchant_id', $this->getMerchantId())
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
            'products.required' => 'Пожалуйста, выберите хотя бы один товар.',
            'products.min' => 'Пожалуйста, выберите хотя бы один товар.',
            'products.*.quantity.required' => 'Пожалуйста, укажите количество для каждого выбранного товара.',
            'products.*.quantity.min' => 'Количество должно быть не менее 1.',
            'recipient_address.required' => 'Пожалуйста, введите адрес доставки.',
            'recipient_name.required' => 'Пожалуйста, введите имя получателя.',
            'recipient_phone.required' => 'Пожалуйста, введите телефон получателя.',
        ];
    }

    protected function getMerchantId(): ?int
    {
        return cache()->remember(
            "merchant_id_{$this->input('merchant_id')}",
            60,
            fn() => Merchant::where('uuid', $this->input('merchant_id'))->value('id')
        );
    }
}
