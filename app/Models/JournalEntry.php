<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'transaction_id',
        'transaction_name',
        'dt',
        'account_id',
        'debit',
        'credit',
        'desc',
        'journal_entry_id'
    ];

    protected $casts = [
        'dt' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Dynamic relationship to the transaction model based on transaction_name and transaction_id
     */
    public function transaction()
    {
        $modelClass = $this->resolveModelClass($this->transaction_name);
        if ($modelClass && class_exists($modelClass)) {
            return $this->belongsTo($modelClass, 'transaction_id');
        }
        // fallback: return null relationship
        return null;
    }

    /**
     * Helper to resolve model class from table name
     */
    protected function resolveModelClass($table)
    {
        // Map table names to model classes (add more as needed)
        $map = [
            'orders' => \App\Models\Order::class,
            'purchase_orders' => \App\Models\PurchaseOrder::class,
            'productions' => \App\Models\Production::class,
            'invoices' => \App\Models\Invoice::class,
            'payments' => \App\Models\Payment::class,
        ];
        return $map[$table] ?? null;
    }
}
