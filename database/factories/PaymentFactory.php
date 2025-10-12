<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'bank_account_id' => null,
            'bank_account_name' => $this->faker->company(),
            'bank_account_type' => $this->faker->randomElement(['virtual_account','bank_transfer']),
            'paid_at' => $this->faker->dateTimeBetween('-90 days', 'now'),
            'payment_type' => $this->faker->randomElement(['cash','transfer']),
            'amount' => $this->faker->numberBetween(50000, 5000000),
        ];
    }
}
