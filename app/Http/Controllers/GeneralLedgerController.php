<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $defaultStart = now()->subMonth()->toDateString();
        $defaultEnd = now()->toDateString();
        $dt_start = $request->get('dt_start', $defaultStart);
        $dt_end = $request->get('dt_end', $defaultEnd);

        $accounts = Account::whereNotNull('parent_id')
            ->orderBy('id','asc')
            ->get(['id','code','name']);

        return view('accounting.ledger.index', compact('accounts','dt_start','dt_end'));
    }

    public function show(Account $account, Request $request)
    {
        $defaultStart = now()->subMonth()->toDateString();
        $defaultEnd = now()->toDateString();
        $dt_start = $request->get('dt_start', $defaultStart);
        $dt_end = $request->get('dt_end', $defaultEnd);

        $entries = JournalEntry::with(['account'])
            ->where('account_id', $account->id)
            ->when($dt_start && $dt_end, function($q) use ($dt_start,$dt_end){
                $q->whereBetween('dt', [$dt_start, $dt_end]);
            })
            ->orderBy('dt','asc')
            ->orderBy('id','asc')
            ->get();

        // Preload related transactions to avoid N+1 where possible via our dynamic relation
        $entries->loadMissing('transaction');

        // Totals
        $totalDebit = $entries->sum('debit');
        $totalCredit = $entries->sum('credit');
        $totalBalance = $totalDebit - $totalCredit;

        return view('accounting.ledger.show', compact('account','entries','dt_start','dt_end','totalDebit','totalCredit','totalBalance'));
    }
}
