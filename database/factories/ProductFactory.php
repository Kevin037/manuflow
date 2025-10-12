<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $units = ['pcs','box','kg'];
        return [
            'name' => $this->faker->words(2, true),
            'sku' => strtoupper($this->faker->bothify('SKU-???-#####')),
            'price' => $this->faker->numberBetween(10000, 500000),
            'photo' => "tes.jpg",
            'qty' => $this->faker->randomFloat(2, 0, 500),
            'formula_id' => 1,
        ];
    }
}
