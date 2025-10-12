<?php

namespace Database\Factories;

use App\Models\Production;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Production>
 */
class ProductionFactory extends Factory
{
    protected $model = Production::class;

    public function definition(): array
    {
        return [
            'dt' => $this->faker->dateTimeBetween('-90 days', 'now'),
            'product_id' => Product::factory(),
            'qty' => $this->faker->randomFloat(2, 1, 100),
            'notes' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['pending','completed']),
        ];
    }
}
