<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'dt',
        'supplier_id',
        'total',
        'status'
    ];

    protected $casts = [
        'dt' => 'date',
        'total' => 'decimal:2'
    ];

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrderDetails(): HasMany
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    // Query Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    // Business Logic Methods
    public function calculateTotal()
    {
        $total = $this->purchaseOrderDetails()
            ->join('materials', 'purchase_order_details.material_id', '=', 'materials.id')
            ->selectRaw('SUM(purchase_order_details.qty * materials.price) as total')
            ->value('total');

        $this->update(['total' => $total ?? 0]);
        return $this;
    }

    public function generateTransactionNumber()
    {
        if (empty($this->no)) {
            $this->update(['no' => 'PUR/' . $this->id . date('dmy')]);
        }
        return $this;
    }

    public function updateMaterialStock()
    {
        if ($this->status === 'completed') {
            DB::transaction(function () {
                foreach ($this->purchaseOrderDetails as $detail) {
                    $detail->material->increment('qty', $detail->qty);
                }
            });
        }
        return $this;
    }

    public function canBeModified(): bool
    {
        return $this->status !== 'completed';
    }

    public function markAsCompleted()
    {
        if ($this->canBeModified()) {
            $this->update(['status' => 'completed']);
            $this->updateMaterialStock();
        }
        return $this;
    }

    // Accessors & Mutators
    public function getTotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
            'completed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>'
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($purchaseOrder) {
            $purchaseOrder->generateTransactionNumber();
        });

        static::deleting(function ($purchaseOrder) {
            // Delete related purchase order details
            $purchaseOrder->purchaseOrderDetails()->delete();
        });
    }
}
