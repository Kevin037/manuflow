<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'dt' => $this->faker->dateTimeBetween('-90 days', 'now'),
            'order_id' => Order::factory(),
            'status' => $this->faker->randomElement(['unpaid','partial','paid']),
        ];
    }
}
