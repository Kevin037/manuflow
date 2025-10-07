<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'material_id',
        'qty'
    ];

    protected $casts = [
        'qty' => 'decimal:2'
    ];

    // Relationships
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Business Logic Methods
    public function getSubtotal()
    {
        return $this->qty * $this->material->price;
    }

    // Accessors & Mutators
    public function getSubtotalAttribute()
    {
        return $this->material ? $this->qty * $this->material->price : 0;
    }

    public function getSubtotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($detail) {
            // Recalculate purchase order total when detail is saved
            $detail->purchaseOrder->calculateTotal();
        });

        static::deleted(function ($detail) {
            // Recalculate purchase order total when detail is deleted
            if ($detail->purchaseOrder) {
                $detail->purchaseOrder->calculateTotal();
            }
        });
    }
}
