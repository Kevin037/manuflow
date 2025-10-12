<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'dt' => $this->faker->dateTimeBetween('-90 days', 'now'),
            'customer_id' => Customer::factory(),
            'total' => 0,
            'status' => $this->faker->randomElement(['pending','completed']),
        ];
    }
}
