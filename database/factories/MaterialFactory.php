<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        $units = ['kg','g','pcs','liter'];
        return [
            'name' => ucfirst($this->faker->unique()->word()),
            'price' => $this->faker->numberBetween(1000, 100000),
            'unit' => $this->faker->randomElement($units),
            'qty' => $this->faker->randomFloat(2, 0, 1000),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
