<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'dt',
        'product_id',
        'qty',
        'notes',
        'status'
    ];

    protected $casts = [
        'dt' => 'date',
        'qty' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->no = 'PRO/' . $model->id . date('dmy');
            $model->save();
        });
    }

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // Business Logic Methods
    public function canBeModified(): bool
    {
        return $this->status !== 'completed';
    }

    /**
     * Update material stock when production is completed
     */
    public function updateMaterialStock()
    {
        if ($this->status === 'completed') {
            DB::transaction(function () {
                $product = $this->product->load('formula.formulaDetails.material');
                
                if ($product->formula) {
                    foreach ($product->formula->formulaDetails as $detail) {
                        $requiredQty = $detail->qty * $this->qty;
                        $detail->material->decrement('qty', $requiredQty);
                    }
                }
            });
        }
        return $this;
    }

    /**
     * Update product stock when production is completed
     */
    public function updateProductStock()
    {
        if ($this->status === 'completed') {
            $this->product->increment('qty', $this->qty);
        }
        return $this;
    }

    // Accessors
    public function getFormattedQtyAttribute()
    {
        return number_format($this->qty, 2, ',', '.');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>',
            'completed' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>'
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }
}
