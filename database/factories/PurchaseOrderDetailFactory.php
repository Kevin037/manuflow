<?php

namespace Database\Factories;

use App\Models\PurchaseOrderDetail;
use App\Models\PurchaseOrder;
use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderDetail>
 */
class PurchaseOrderDetailFactory extends Factory
{
    protected $model = PurchaseOrderDetail::class;

    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'material_id' => Material::factory(),
            'qty' => $this->faker->randomFloat(2, 1, 200),
        ];
    }
}
