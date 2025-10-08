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
}
