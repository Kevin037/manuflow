<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Controllers\Concerns\ExportsDataTable;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    use ExportsDataTable;
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

    public function exportExcel(Request $request)
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
                'code' => $acc->code,
                'name' => $acc->name,
                'saldo_awal_debit' => $saldoAwalDebit,
                'saldo_awal_kredit' => $saldoAwalKredit,
                'mutasi_debit' => $mutasiDebit,
                'mutasi_kredit' => $mutasiKredit,
                'saldo_akhir_debit' => $saldoAkhirDebit,
                'saldo_akhir_kredit' => $saldoAkhirKredit,
                'saldo_akhir' => $saldoAkhir,
            ];
        });

        return $this->exportWithImages($rows->toArray(), [
            'code' => 'Account Code',
            'name' => 'Account Name',
            'saldo_awal_debit' => 'Opening Balance Debit',
            'saldo_awal_kredit' => 'Opening Balance Credit',
            'mutasi_debit' => 'Transaction Debit',
            'mutasi_kredit' => 'Transaction Credit',
            'saldo_akhir_debit' => 'Ending Balance Debit',
            'saldo_akhir_kredit' => 'Ending Balance Credit',
            'saldo_akhir' => 'Final Balance',
        ], null, 'trial_balance');
    }
}
