<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'barcode' => $this->faker->unique()->ean13(),
            'selling_price' => 100,
            'quantity' => 0,
        ];
    }
}
