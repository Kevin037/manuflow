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

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2);
    }

    public function getTotalValueAttribute()
    {
        return $this->price * $this->qty;
    }
}