<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $defaultStart = now()->subMonth()->toDateString();
        $defaultEnd = now()->toDateString();
        $dt_start = $request->get('dt_start', $defaultStart);
        $dt_end = $request->get('dt_end', $defaultEnd);

        $entries = JournalEntry::with('account')
            ->when($dt_start && $dt_end, function($q) use ($dt_start,$dt_end){
                $q->whereBetween('dt', [$dt_start, $dt_end]);
            })
            ->orderBy('id','desc')
            ->get();

        // Group by transaction (use both name and id to avoid collisions)
        $groups = $entries->groupBy(function($e){
            return ($e->transaction_name ?: 'tx').':'.($e->transaction_id ?: 0);
        })->map(function($collection){
            // also compute a key for sorting by max id desc (already ordered desc globally, but ensure grouping order)
            $collection->max_id = $collection->max('id');
            return $collection;
        })->sortByDesc(function($c){ return $c->max_id; });

        return view('accounting.journals.index', compact('groups','dt_start','dt_end'));
    }
}
