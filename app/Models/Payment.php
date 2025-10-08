<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'invoice_id',
        'bank_account_id',
        'bank_account_name',
        'bank_account_type',
        'paid_at',
        'payment_type',
        'amount'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->no = 'PAY/' . $model->id . date('dmy');
            $model->save();
        });
    }

    // Relationships
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // Query Scopes
    public function scopeCash($query)
    {
        return $query->where('payment_type', 'cash');
    }

    public function scopeTransfer($query)
    {
        return $query->where('payment_type', 'transfer');
    }

    // Accessors
    public function getPaymentTypeBadgeAttribute()
    {
        $badges = [
            'cash' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Cash</span>',
            'transfer' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Transfer</span>'
        ];

        return $badges[$this->payment_type] ?? $badges['transfer'];
    }
}
