<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accounting extends Model
{
    /**
     * Get journal entries by type and date range
     * @param string $type Column name to check for not null (e.g. 'debit' or 'credit')
     * @param string $dt_start Start date (Y-m-d)
     * @param string|null $dt_end End date (Y-m-d) or null
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function journal_entries($type, $dt_start, $dt_end = null): HasMany
    {
        $query = $this->hasMany(JournalEntry::class)
            ->whereNotNull($type);
        if ($dt_end === null) {
            $query->where('dt', '<', $dt_start);
        } else {
            $query->whereBetween('dt', [$dt_start, $dt_end]);
        }
        return $query;
    }
}