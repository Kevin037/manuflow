<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\JournalEntry;

class Account extends Model
{
    protected $fillable = [
        'code','name','parent_id'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get journal entries relation filtered by type and date range.
     * When $dt_end is null, it returns entries strictly before $dt_start (opening balance).
     * When $dt_end is provided, it returns entries between $dt_start and $dt_end (inclusive).
     */
    public function journal_entries(string $type, string $dt_start, ?string $dt_end = null): HasMany
    {
        $query = $this->hasMany(JournalEntry::class, 'account_id')
            ->whereNotNull($type);

        if ($dt_end === null) {
            $query->where('dt', '<', $dt_start);
        } else {
            $query->whereBetween('dt', [$dt_start, $dt_end]);
        }

        return $query;
    }
}
