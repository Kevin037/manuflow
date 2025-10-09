<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $defaultStart = now()->subMonth()->toDateString();
        $defaultEnd = now()->toDateString();
        $dt_start = $request->get('dt_start', $defaultStart);
        $dt_end = $request->get('dt_end', $defaultEnd);

        // Only child accounts (parent_id <> null/0)
        $accounts = Account::whereNotNull('parent_id')
            ->where('parent_id', '!=', 0)
            ->orderBy('id', 'asc')
            ->get();

        // Precompute amounts per account using the helper on Account
        $rows = $accounts->map(function(Account $acc) use ($dt_start, $dt_end){
            $saldoAwalDebit = (float) $acc->journal_entries('debit', $dt_start)->sum('debit');
            $saldoAwalKredit = (float) $acc->journal_entries('credit', $dt_start)->sum('credit');
            $mutasiDebit = (float) $acc->journal_entries('debit', $dt_start, $dt_end)->sum('debit');
            $mutasiKredit = (float) $acc->journal_entries('credit', $dt_start, $dt_end)->sum('credit');
            $saldoAkhirDebit = $saldoAwalDebit + $mutasiDebit;
            $saldoAkhirKredit = $saldoAwalKredit + $mutasiKredit;
            $saldoAkhir = $saldoAkhirDebit - $saldoAkhirKredit;

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'saldoAwalDebit' => $saldoAwalDebit,
                'saldoAwalKredit' => $saldoAwalKredit,
                'mutasiDebit' => $mutasiDebit,
                'mutasiKredit' => $mutasiKredit,
                'saldoAkhirDebit' => $saldoAkhirDebit,
                'saldoAkhirKredit' => $saldoAkhirKredit,
                'saldoAkhir' => $saldoAkhir,
            ];
        });

        // Footer totals
        $totals = [
            'saldoAwalDebit' => (float) $rows->sum('saldoAwalDebit'),
            'saldoAwalKredit' => (float) $rows->sum('saldoAwalKredit'),
            'mutasiDebit' => (float) $rows->sum('mutasiDebit'),
            'mutasiKredit' => (float) $rows->sum('mutasiKredit'),
            'saldoAkhirDebit' => (float) $rows->sum('saldoAkhirDebit'),
            'saldoAkhirKredit' => (float) $rows->sum('saldoAkhirKredit'),
            'saldoAkhir' => (float) $rows->sum('saldoAkhir'),
        ];

        return view('accounting.trial-balance.index', compact('rows','dt_start','dt_end','totals'));
    }
}
