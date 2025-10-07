<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'no',
        'total'
    ];

    protected $casts = [
        'total' => 'decimal:2'
    ];

    // Relationships
    public function formulaDetails(): HasMany
    {
        return $this->hasMany(FormulaDetail::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Accessors & Mutators
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 0, ',', '.');
    }

    // Business Logic Methods
    public function calculateTotal()
    {
        $this->total = $this->formulaDetails()
            ->join('materials', 'materials.id', '=', 'formula_details.material_id')
            ->sum(DB::raw('formula_details.qty * materials.price'));
        
        $this->save();
        return $this->total;
    }

    public function getTotalMaterialsAttribute()
    {
        return $this->formulaDetails()->count();
    }

    // Query Scopes
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('no', 'like', "%{$code}%");
    }
}
