<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            // merchant_id нужно будет передавать при вызове фабрики
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(10),
            'sku' => strtoupper($this->faker->unique()->bothify('SKU-####')),
            'barcode' => $this->faker->unique()->ean13(),
            'active' => $this->faker->boolean(90),
        ];
    }
}
