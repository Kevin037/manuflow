<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Account;
use App\Models\JournalEntry;

class ReportService
{
    /**
     * Calculate Profit & Loss for a period.
     * Returns: total_sales, total_hpp, total_expenses, gross_profit, net_profit
     */
    public function calculateProfitLoss($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Aggregates using Query Builder with safe fallbacks based on available tables/columns
        $totalSales = 0.0;
        if (Schema::hasTable('sales')) {
            $totalSales = $this->sumTableInRange('sales', ['date','dt','created_at'], ['total','amount','grand_total'], $start, $end);
        } elseif (Schema::hasTable('orders')) {
            $totalSales = $this->sumTableInRange('orders', ['dt','date','created_at'], ['total','grand_total'], $start, $end);
        }

        $totalHpp = 0.0;
        if (Schema::hasTable('purchases')) {
            $totalHpp = $this->sumTableInRange('purchases', ['date','dt','created_at'], ['total','amount','grand_total'], $start, $end);
        } elseif (Schema::hasTable('purchase_orders')) {
            $totalHpp = $this->sumTableInRange('purchase_orders', ['dt','date','created_at'], ['total','grand_total'], $start, $end);
        }

        $totalExpenses = 0.0;
        if (Schema::hasTable('expenses')) {
            $totalExpenses = $this->sumTableInRange('expenses', ['date','dt','created_at'], ['amount','total'], $start, $end);
        }

        $grossProfit = $totalSales - $totalHpp;
        $netProfit = $grossProfit - $totalExpenses;

        return [
            'total_sales' => $totalSales,
            'total_hpp' => $totalHpp,
            'total_expenses' => $totalExpenses,
            'gross_profit' => $grossProfit,
            'net_profit' => $netProfit,
        ];
    }

    /**
     * Sum an amount column in a table within a date range, choosing the first existing date and amount columns.
     */
    private function sumTableInRange(string $table, array $dateColumns, array $amountColumns, $start, $end): float
    {
        $dateCol = null;
        foreach ($dateColumns as $col) {
            if (Schema::hasColumn($table, $col)) { $dateCol = $col; break; }
        }
        $amountCol = null;
        foreach ($amountColumns as $col) {
            if (Schema::hasColumn($table, $col)) { $amountCol = $col; break; }
        }
        if (!$dateCol || !$amountCol) {
            return 0.0;
        }
        return (float) DB::table($table)->whereBetween($dateCol, [$start, $end])->sum($amountCol);
    }

    /**
     * Detailed Profit & Loss by account using Journal Entries for the given period.
     * Groups by top-level account (Pendapatan, Biaya, Beban Keuangan, Pendapatan Lainnya, Pajak).
     */
    public function calculateProfitLossDetailed($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Sum journal entries by account in one query for the period
        $sums = JournalEntry::selectRaw('account_id, SUM(debit) as sum_debit, SUM(credit) as sum_credit')
            ->whereBetween('dt', [$start, $end])
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        // Load all child accounts (ignore root headings with null parent)
        $accounts = Account::whereNotNull('parent_id')->with('parent')->get();

        // Helper to resolve top-level ancestor
        $getTop = function(Account $acc): Account {
            $cur = $acc;
            while ($cur->parent_id) {
                $parent = $cur->parent; // may be lazy loaded next
                if (!$parent) break;
                if ($parent->parent_id === null) return $parent;
                $cur = $parent;
            }
            return $cur;
        };

        $groups = [
            '4' => ['key' => 'pendapatan', 'label' => 'Pendapatan', 'accounts' => [], 'total' => 0.0],
            '5' => ['key' => 'hpp_biaya', 'label' => 'Biaya (Termasuk HPP)', 'accounts' => [], 'total' => 0.0],
            '6' => ['key' => 'beban_keuangan', 'label' => 'Beban Keuangan', 'accounts' => [], 'total' => 0.0],
            '7' => ['key' => 'pendapatan_lainnya', 'label' => 'Pendapatan Lainnya', 'accounts' => [], 'total' => 0.0],
            '8' => ['key' => 'pajak', 'label' => 'Pajak', 'accounts' => [], 'total' => 0.0],
        ];

        foreach ($accounts as $acc) {
            $top = $getTop($acc);
            $topFirstDigit = substr((string)$top->code, 0, 1);
            if (!isset($groups[$topFirstDigit])) {
                continue; // skip balance sheet accounts (1-3) in P&L
            }
            $row = $sums->get($acc->id);
            $debit = (float)($row->sum_debit ?? 0);
            $credit = (float)($row->sum_credit ?? 0);
            if ($debit == 0 && $credit == 0) {
                continue; // skip empty rows for cleaner report
            }
            // Natural balance: revenue 4/7 = credit - debit, expenses 5/6/8 = debit - credit
            if (in_array($topFirstDigit, ['4','7'])) {
                $amount = $credit - $debit;
            } else {
                $amount = $debit - $credit;
            }
            if (abs($amount) < 0.005) continue;

            $groups[$topFirstDigit]['accounts'][] = [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'amount' => $amount,
            ];
            $groups[$topFirstDigit]['total'] += $amount;
        }

        // Compute high-level KPIs
        $totalRevenueMain = $groups['4']['total'];
        $hppTotal = $groups['5']['total'];
        $otherIncome = $groups['7']['total'];
        $financialExpense = $groups['6']['total'];
        $taxExpense = $groups['8']['total'];

        $grossProfit = $totalRevenueMain - $hppTotal;
        $netProfit = $grossProfit + $otherIncome - $financialExpense - $taxExpense;

        return [
            'groups' => $groups,
            'summary' => [
                'total_revenue_main' => $totalRevenueMain,
                'total_hpp' => $hppTotal,
                'other_income' => $otherIncome,
                'financial_expense' => $financialExpense,
                'tax_expense' => $taxExpense,
                'gross_profit' => $grossProfit,
                'net_profit' => $netProfit,
            ],
        ];
    }

    /**
     * Generate Balance Sheet as of a snapshot date, optionally including net profit from a P&L period.
     */
    public function calculateBalanceSheet(string $asOfDate, ?string $plStartDate = null, ?string $plEndDate = null): array
    {
        $asOf = Carbon::parse($asOfDate)->endOfDay();

        // Determine P&L period (defaults: start of month of asOf -> asOf)
        $plStart = $plStartDate ? Carbon::parse($plStartDate)->startOfDay() : Carbon::parse($asOfDate)->startOfMonth();
        $plEnd = $plEndDate ? Carbon::parse($plEndDate)->endOfDay() : $asOf;

        // Net profit from existing P&L logic
        $pl = $this->calculateProfitLoss($plStart->toDateString(), $plEnd->toDateString());
        $netProfit = (float)($pl['net_profit'] ?? 0);

        // Assets
        $assetsTotal = $this->sumTableUpToDate('assets', ['date','dt','created_at'], ['amount','total','balance'], $asOf);
        $assetsBreakdown = $this->breakdownUpToDate('assets', ['date','dt','created_at'], 'account_name', ['amount','total','balance'], $asOf);

        // Liabilities
        $liabilitiesTotal = $this->sumTableUpToDate('liabilities', ['date','dt','created_at'], ['amount','total','balance'], $asOf);
        $liabilitiesBreakdown = $this->breakdownUpToDate('liabilities', ['date','dt','created_at'], 'account_name', ['amount','total','balance'], $asOf);

        // Equities (optional)
        $equitiesBreakdown = [];
        if (Schema::hasTable('equities')) {
            $equitySum = $this->sumTableUpToDate('equities', ['date','dt','created_at'], ['amount','total','balance'], $asOf);
            $equitiesBreakdown = $this->breakdownUpToDate('equities', ['date','dt','created_at'], 'account_name', ['amount','total','balance'], $asOf);
            $equitiesTotal = $equitySum + $netProfit;
        } else {
            $equitiesTotal = ($assetsTotal - $liabilitiesTotal) + $netProfit;
        }

        // Sort breakdowns by amount desc and compute balanced flag
        $assetsBreakdown = $this->sortBreakdownDesc($assetsBreakdown);
        $liabilitiesBreakdown = $this->sortBreakdownDesc($liabilitiesBreakdown);
        $equitiesBreakdown = $this->sortBreakdownDesc($equitiesBreakdown);

        $balanced = $this->approximatelyEqual($assetsTotal, $liabilitiesTotal + $equitiesTotal, 0.01);

        return [
            'as_of' => $asOf->toDateString(),
            'assets_total' => $assetsTotal,
            'liabilities_total' => $liabilitiesTotal,
            'equities_total' => $equitiesTotal,
            'net_profit' => $netProfit,
            'balanced' => $balanced,
            'assets_breakdown' => $assetsBreakdown,
            'liabilities_breakdown' => $liabilitiesBreakdown,
            'equities_breakdown' => $equitiesBreakdown,
        ];
    }

    private function sumTableUpToDate(string $table, array $dateColumns, array $amountColumns, $asOf): float
    {
        if (!Schema::hasTable($table)) return 0.0;
        $dateCol = $this->firstExistingColumn($table, $dateColumns);
        $amountCol = $this->firstExistingColumn($table, $amountColumns);
        if (!$dateCol || !$amountCol) return 0.0;
        return (float) DB::table($table)
            ->where($dateCol, '<=', $asOf)
            ->sum($amountCol);
    }

    private function breakdownUpToDate(string $table, array $dateColumns, string $nameColumn, array $amountColumns, $asOf): array
    {
        if (!Schema::hasTable($table)) return [];
        $dateCol = $this->firstExistingColumn($table, $dateColumns);
        $amountCol = $this->firstExistingColumn($table, $amountColumns);
        if (!$dateCol || !$amountCol || !Schema::hasColumn($table, $nameColumn)) return [];

        $rows = DB::table($table)
            ->where($dateCol, '<=', $asOf)
            ->get([$nameColumn, $amountCol]);

        return $rows->groupBy($nameColumn)->map(function($items) use ($nameColumn, $amountCol){
            return [
                'account_name' => (string) $items->first()->{$nameColumn},
                'amount' => (float) $items->sum($amountCol),
            ];
        })->values()->all();
    }

    private function firstExistingColumn(string $table, array $candidates): ?string
    {
        foreach ($candidates as $col) {
            if (Schema::hasColumn($table, $col)) return $col;
        }
        return null;
    }

    private function sortBreakdownDesc(array $rows): array
    {
        usort($rows, fn($a,$b) => ($b['amount'] ?? 0) <=> ($a['amount'] ?? 0));
        return $rows;
    }

    private function approximatelyEqual(float $a, float $b, float $epsilon = 0.01): bool
    {
        return abs($a - $b) <= $epsilon;
    }
}
