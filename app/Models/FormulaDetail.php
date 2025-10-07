<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'formula_id',
        'material_id',
        'qty'
    ];

    protected $casts = [
        'qty' => 'decimal:2'
    ];

    // Relationships
    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->qty * $this->material->price;
    }

    public function getFormattedQtyAttribute()
    {
        return number_format($this->qty, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.');
    }
}
