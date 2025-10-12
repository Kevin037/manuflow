<?php

namespace Database\Factories;

use App\Models\FormulaDetail;
use App\Models\Formula;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormulaDetail>
 */
class FormulaDetailFactory extends Factory
{
    protected $model = FormulaDetail::class;

    public function definition(): array
    {
        return [
            'formula_id' => Formula::factory(),
            'material_id' => Material::factory(),
            'qty' => $this->faker->randomFloat(2, 0.1, 5),
        ];
    }
}
