<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        return [
            'dt' => $this->faker->dateTimeBetween('-90 days', 'now'),
            'supplier_id' => Supplier::factory(),
            'total' => 0,
            'status' => $this->faker->randomElement(['pending','completed']),
        ];
    }
}
