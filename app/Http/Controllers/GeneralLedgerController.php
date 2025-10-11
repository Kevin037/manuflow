<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Http\Controllers\Concerns\ExportsDataTable;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    use ExportsDataTable;
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

    public function exportExcel(Request $request)
    {
        $defaultStart = now()->subMonth()->toDateString();
        $defaultEnd = now()->toDateString();
        $dt_start = $request->get('dt_start', $defaultStart);
        $dt_end = $request->get('dt_end', $defaultEnd);

        // Export the same list shown on the index view (accounts list)
        $accounts = Account::whereNotNull('parent_id')
            ->orderBy('id','asc')
            ->get(['code','name']);

        $rows = $accounts->map(function($acc){
            return [
                'code' => $acc->code,
                'name' => $acc->name,
            ];
        })->toArray();

        return $this->exportWithImages($rows, [
            'code' => 'Account Code',
            'name' => 'Account Name',
        ], null, 'general_ledger');
    }
}
