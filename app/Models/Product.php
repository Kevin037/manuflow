<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'decimal:2',
    ];

    // Relationships
    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    // Query Scopes
    public function scopeInStock($query)
    {
        return $query->where('qty', '>', 0);
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', 'like', "%{$sku}%");
    }

    // Business Logic Methods
    public function updateStock($quantity, $operation = 'subtract')
    {
        if ($operation === 'add') {
            $this->increment('qty', $quantity);
        } else {
            $this->decrement('qty', $quantity);
        }
    }

    /**
     * Check if there's sufficient material stock for production
     * 
     * @param float $productionQty The quantity of products to produce
     * @return array Information about stock availability
     */
    public function checkMaterialStock($productionQty)
    {
        if (!$this->formula) {
            return [
                'can_produce' => false,
                'message' => 'Product has no formula defined.',
                'materials' => [],
                'max_producible' => 0
            ];
        }

        $this->load('formula.formulaDetails.material');
        $materials = [];
        $canProduce = true;
        $constrainingMaterials = [];
        $maxProducible = PHP_INT_MAX;

        foreach ($this->formula->formulaDetails as $detail) {
            $material = $detail->material;
            $requiredQty = $detail->qty * $productionQty;
            $availableQty = $material->qty;
            $maxPossibleFromThisMaterial = floor($availableQty / $detail->qty);
            
            if ($maxPossibleFromThisMaterial < $maxProducible) {
                $maxProducible = $maxPossibleFromThisMaterial;
            }

            $isInsufficient = $requiredQty > $availableQty;
            
            if ($isInsufficient) {
                $canProduce = false;
                $constrainingMaterials[] = $material->name;
            }

            $materials[] = [
                'id' => $material->id,
                'name' => $material->name,
                'unit' => $material->unit ?? 'pcs',
                'required_per_product' => $detail->qty,
                'required_total' => $requiredQty,
                'available' => $availableQty,
                'sufficient' => !$isInsufficient,
                'shortage' => $isInsufficient ? $requiredQty - $availableQty : 0,
                'max_producible_from_this' => $maxPossibleFromThisMaterial
            ];
        }

        $message = '';
        if (!$canProduce) {
            $message = 'Insufficient stock for: ' . implode(', ', $constrainingMaterials) . '. ';
        }
        
        if ($maxProducible > 0) {
            $message .= "Maximum producible quantity: {$maxProducible}";
        } else {
            $message .= "No production possible with current stock.";
        }

        return [
            'can_produce' => $canProduce,
            'message' => $message,
            'materials' => $materials,
            'max_producible' => max(0, $maxProducible),
            'constraining_materials' => $constrainingMaterials
        ];
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getTotalValueAttribute()
    {
        return $this->price * $this->qty;
    }
}