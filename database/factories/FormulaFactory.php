<?php

namespace Database\Factories;

use App\Models\Formula;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Formula>
 */
class FormulaFactory extends Factory
{
    protected $model = Formula::class;

    public function definition(): array
    {
        return [
            'name' => 'Formula ' . strtoupper($this->faker->bothify('??-###')),
            'total' => 0,
        ];
    }
}
