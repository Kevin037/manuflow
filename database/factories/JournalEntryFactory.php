<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    public function definition(): array
    {
        $debit = $this->faker->randomElement([0, $this->faker->numberBetween(10000, 500000)]);
        $credit = $debit ? 0 : $this->faker->numberBetween(10000, 500000);
        return [
            'transaction_id' => 0,
            'transaction_name' => 'orders',
            'dt' => $this->faker->dateTimeBetween('-90 days', 'now'),
            'account_id' => Account::inRandomOrder()->value('id') ?? function(){ return Account::factory(); },
            'debit' => $debit,
            'credit' => $credit,
            'desc' => $this->faker->sentence(),
            'journal_entry_id' => null,
        ];
    }
}
