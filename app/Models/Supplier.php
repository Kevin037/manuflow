<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relationships
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Query Scopes
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }

    // Business Logic Methods
    public function getTotalPurchasesAttribute()
    {
        return $this->purchaseOrders()->sum('total');
    }

    public function getActiveMaterialsCountAttribute()
    {
        return $this->materials()->where('qty', '>', 0)->count();
    }
}
