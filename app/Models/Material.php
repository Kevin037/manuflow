<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price', 
        'unit',
        'qty',
        'supplier_id'
    ];

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Query Scopes
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeInStock($query)
    {
        return $query->where('qty', '>', 0);
    }

    // Business Logic Methods
    public function getTotalValueAttribute()
    {
        return $this->price * $this->qty;
    }

    public function isInStock()
    {
        return $this->qty > 0;
    }
}
