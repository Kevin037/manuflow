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

    /**
     * Monthly summary KPIs for current month vs previous month.
     * Returns structure:
     * [
     *   'as_of' => 'YYYY-MM-DD', // start of current month
     *   'current_period' => ['start' => 'YYYY-MM-DD', 'end' => 'YYYY-MM-DD'],
     *   'metrics' => [
     *     'total_revenue' => ['current' => float, 'previous' => float, 'percent_change' => ?float, 'baseline_zero' => bool],
     *     'total_purchase_orders_count' => ['current' => int, 'previous' => int, 'percent_change' => ?float, 'baseline_zero' => bool],
     *     'total_net_profit' => ['current' => float, 'previous' => float, 'percent_change' => ?float, 'baseline_zero' => bool],
     *   ],
     * ]
     */
    public function getMonthlySummary(?Carbon $asOf = null): array
    {
        $asOf = $asOf ? $asOf->copy() : Carbon::now();
        $currentStart = $asOf->copy()->startOfMonth();
        $currentEnd = $asOf->copy()->endOfMonth();
        $previousStart = $currentStart->copy()->subMonthNoOverflow()->startOfMonth();
        $previousEnd = $currentStart->copy()->subMonthNoOverflow()->endOfMonth();

        // Revenue (prefer 'sales', fallback 'orders')
        $revCols = ['total_amount','total','amount','grand_total'];
        $dateCols = ['date','dt','created_at'];

        $revenueTable = null;
        if (Schema::hasTable('sales')) {
            $revenueTable = 'sales';
        } elseif (Schema::hasTable('orders')) {
            $revenueTable = 'orders';
        }
        $totalRevenueCurrent = 0.0;
        $totalRevenuePrevious = 0.0;
        if ($revenueTable) {
            $totalRevenueCurrent = (float) $this->sumTableInRange($revenueTable, $dateCols, $revCols, $currentStart, $currentEnd);
            $totalRevenuePrevious = (float) $this->sumTableInRange($revenueTable, $dateCols, $revCols, $previousStart, $previousEnd);
        }

        // Purchase orders total amount (prefer 'purchases', fallback 'purchase_orders')
        $poTable = null;
        if (Schema::hasTable('purchases')) {
            $poTable = 'purchases';
        } elseif (Schema::hasTable('purchase_orders')) {
            $poTable = 'purchase_orders';
        }
        $poTotalCurrent = 0.0;
        $poTotalPrevious = 0.0;
        if ($poTable) {
            // Prioritize 'total', then common fallbacks
            $poAmountCols = ['total','total_amount','amount','grand_total','total_cost'];
            $poTotalCurrent = (float) $this->sumTableInRange($poTable, $dateCols, $poAmountCols, $currentStart, $currentEnd);
            $poTotalPrevious = (float) $this->sumTableInRange($poTable, $dateCols, $poAmountCols, $previousStart, $previousEnd);
        }

        // Net profit using existing P&L logic
        $plCurrent = $this->calculateProfitLoss($currentStart->toDateString(), $currentEnd->toDateString());
        $plPrevious = $this->calculateProfitLoss($previousStart->toDateString(), $previousEnd->toDateString());
        $netProfitCurrent = (float) ($plCurrent['net_profit'] ?? 0.0);
        $netProfitPrevious = (float) ($plPrevious['net_profit'] ?? 0.0);

        // Percent change helper
        $pct = function(float $current, float $previous): array {
            if (abs($previous) < 1e-9) {
                return ['percent' => null, 'baseline_zero' => true];
            }
            $percent = (($current - $previous) / abs($previous)) * 100.0;
            return ['percent' => round($percent, 2), 'baseline_zero' => false];
        };

        $revPct = $pct($totalRevenueCurrent, $totalRevenuePrevious);
    $poPct = $pct($poTotalCurrent, $poTotalPrevious);
        $npPct = $pct($netProfitCurrent, $netProfitPrevious);

        return [
            'as_of' => $currentStart->toDateString(),
            'current_period' => [
                'start' => $currentStart->toDateString(),
                'end' => $currentEnd->toDateString(),
            ],
            'metrics' => [
                'total_revenue' => [
                    'current' => (float) round($totalRevenueCurrent, 2),
                    'previous' => (float) round($totalRevenuePrevious, 2),
                    'percent_change' => $revPct['percent'],
                    'baseline_zero' => $revPct['baseline_zero'],
                ],
                'total_purchase_orders' => [
                    'current' => (float) round($poTotalCurrent, 2),
                    'previous' => (float) round($poTotalPrevious, 2),
                    'percent_change' => $poPct['percent'],
                    'baseline_zero' => $poPct['baseline_zero'],
                ],
                'total_net_profit' => [
                    'current' => (float) round($netProfitCurrent, 2),
                    'previous' => (float) round($netProfitPrevious, 2),
                    'percent_change' => $npPct['percent'],
                    'baseline_zero' => $npPct['baseline_zero'],
                ],
            ],
        ];
    }

    /**
     * Get last N months Sales and Net Profit time series ending with current month.
     * Returns array: ['labels'=>['Nov 2024',...], 'sales'=>[...], 'profit'=>[...], 'meta'=>['start_date'=>..., 'end_date'=>...]]
     */
    public function getMonthlySalesAndProfit(int $months = 12): array
    {
        // Clamp months to [1,36]
        $months = max(1, min(36, (int) $months));

        $now = Carbon::now();
        $endMonth = $now->copy()->endOfMonth();
        $startMonth = $now->copy()->subMonthsNoOverflow($months - 1)->startOfMonth();

        $labels = [];
        $salesSeries = [];
        $profitSeries = [];

        $cursor = $startMonth->copy();
        while ($cursor->lessThanOrEqualTo($endMonth)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();

            // total_sales - prefer 'sales' table, fallback to 'orders'
            $totalSales = 0.0;
            if (Schema::hasTable('sales')) {
                $totalSales = $this->sumTableInRange('sales', ['date','dt','created_at'], ['total_amount','total','amount','grand_total'], $monthStart, $monthEnd);
            } elseif (Schema::hasTable('orders')) {
                $totalSales = $this->sumTableInRange('orders', ['date','dt','created_at'], ['total_amount','total','grand_total'], $monthStart, $monthEnd);
            }

            // total_hpp - prefer 'purchases', fallback to 'purchase_orders'
            $totalHpp = 0.0;
            if (Schema::hasTable('purchases')) {
                $totalHpp = $this->sumTableInRange('purchases', ['date','dt','created_at'], ['total_cost','amount','total','grand_total'], $monthStart, $monthEnd);
            } elseif (Schema::hasTable('purchase_orders')) {
                $totalHpp = $this->sumTableInRange('purchase_orders', ['date','dt','created_at'], ['total_cost','total','grand_total'], $monthStart, $monthEnd);
            }

            // total_expenses
            $totalExpenses = 0.0;
            if (Schema::hasTable('expenses')) {
                $totalExpenses = $this->sumTableInRange('expenses', ['date','dt','created_at'], ['amount','total'], $monthStart, $monthEnd);
            }

            $netProfit = $totalSales - $totalHpp - $totalExpenses;

            $labels[] = $monthStart->format('M Y');
            $salesSeries[] = (float) round($totalSales, 2);
            $profitSeries[] = (float) round($netProfit, 2);

            $cursor->addMonthNoOverflow();
        }

        return [
            'labels' => $labels,
            'sales' => $salesSeries,
            'profit' => $profitSeries,
            'meta' => [
                'start_date' => $startMonth->toDateString(),
                'end_date' => $now->toDateString(),
            ],
        ];
    }

    /**
     * Monthly total revenue series for last N months ending current month.
     * Returns: ['labels'=>['Nov 2024',...], 'revenues'=>[...], 'meta'=>['start_date','end_date']]
     */
    public function getMonthlyTopProducts(int $months = 12): array
    {
        $months = max(1, min(36, (int)$months));
        $now = Carbon::now();
        $endMonth = $now->copy()->endOfMonth();
        $startMonth = $now->copy()->subMonthsNoOverflow($months - 1)->startOfMonth();

        $labels = [];
        $revenues = [];
        $monthsMeta = [];

        $cursor = $startMonth->copy();
        while ($cursor->lessThanOrEqualTo($endMonth)) {
            $mStart = $cursor->copy()->startOfMonth();
            $mEnd = $cursor->copy()->endOfMonth();

            $totalRevenue = 0.0;
            if (Schema::hasTable('sales')) {
                $totalRevenue = $this->sumTableInRange('sales', ['date','dt','created_at'], ['total_amount','total','amount','grand_total'], $mStart, $mEnd);
            } elseif (Schema::hasTable('orders')) {
                $totalRevenue = $this->sumTableInRange('orders', ['date','dt','created_at'], ['total_amount','total','grand_total'], $mStart, $mEnd);
            }

            $labels[] = $mStart->format('M Y');
            $revenues[] = (float) round($totalRevenue, 2);
            $monthsMeta[] = [
                'year' => (int) $mStart->year,
                'month' => (int) $mStart->month,
            ];

            $cursor->addMonthNoOverflow();
        }

        return [
            'labels' => $labels,
            'months' => $monthsMeta,
            'revenues' => $revenues,
            'meta' => [
                'start_date' => $startMonth->toDateString(),
                'end_date' => $now->toDateString(),
            ],
        ];
    }

    /**
     * Strict Top-5 products for a specific month with percent of month revenue.
     * Returns: ['year','month','label','total_month_revenue','top_products'=>[{rank,product_id,product_name,revenue,percent}]]
     */
    public function getTop5ProductsForMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $label = $start->format('M Y');

        $hasSales = Schema::hasTable('sales');
        $hasOrders = Schema::hasTable('orders');
        $salesTable = $hasSales ? 'sales' : ($hasOrders ? 'orders' : null);
        if (!$salesTable) {
            return [
                'year' => (int)$year,
                'month' => (int)$month,
                'label' => $label,
                'total_month_revenue' => 0.0,
                'top_products' => [],
            ];
        }

        $salesDateCol = $this->firstExistingColumn($salesTable, ['date','dt','created_at']);

        $hasSaleItems = Schema::hasTable('sale_items');
        $hasOrderDetails = Schema::hasTable('order_details');
        $itemsTable = $hasSaleItems ? 'sale_items' : ($hasOrderDetails ? 'order_details' : null);

        $topProducts = [];
        $totalMonthRevenue = 0.0;

        if ($itemsTable) {
            $fkSaleCol = $itemsTable === 'sale_items' ? 'sale_id' : 'order_id';
            $productIdCol = Schema::hasColumn($itemsTable, 'product_id') ? 'product_id' : null;
            $productNameCol = Schema::hasColumn($itemsTable, 'product_name') ? 'product_name' : null;
            $hasProducts = Schema::hasTable('products');
            $productsHavePrice = $hasProducts && Schema::hasColumn('products', 'price');

            // Determine revenue expression
            $revenueCol = null;
            foreach (['subtotal','total','amount'] as $cand) {
                if (Schema::hasColumn($itemsTable, $cand)) { $revenueCol = $cand; break; }
            }
            $qtyCol = Schema::hasColumn($itemsTable, 'qty') ? 'qty' : (Schema::hasColumn($itemsTable, 'quantity') ? 'quantity' : null);
            $priceCol = Schema::hasColumn($itemsTable, 'price') ? 'price' : (Schema::hasColumn($itemsTable, 'unit_price') ? 'unit_price' : null);

            // Total month revenue from items scope (more accurate)
            if ($revenueCol) {
                $totalMonthRevenue = (float) DB::table($itemsTable.' as i')
                    ->join($salesTable.' as s', 's.id', '=', 'i.'.$fkSaleCol)
                    ->whereBetween('s.'.$salesDateCol, [$start, $end])
                    ->sum('i.'.$revenueCol);
            } elseif ($qtyCol && $priceCol) {
                $totalMonthRevenue = (float) DB::table($itemsTable.' as i')
                    ->join($salesTable.' as s', 's.id', '=', 'i.'.$fkSaleCol)
                    ->whereBetween('s.'.$salesDateCol, [$start, $end])
                    ->selectRaw('SUM(i.'.$qtyCol.' * i.'.$priceCol.') as rev')
                    ->value('rev');
            } elseif ($qtyCol && $productsHavePrice && $productIdCol) {
                // Use product price when item price is not stored
                $totalMonthRevenue = (float) DB::table($itemsTable.' as i')
                    ->join($salesTable.' as s', 's.id', '=', 'i.'.$fkSaleCol)
                    ->join('products as p', 'p.id', '=', 'i.'.$productIdCol)
                    ->whereBetween('s.'.$salesDateCol, [$start, $end])
                    ->selectRaw('SUM(i.'.$qtyCol.' * p.price) as rev')
                    ->value('rev');
            }

            // Build top-5 per product
            $query = DB::table($itemsTable.' as i')
                ->join($salesTable.' as s', 's.id', '=', 'i.'.$fkSaleCol)
                ->when($productIdCol && $hasProducts, function($q) use ($productIdCol){
                    return $q->join('products as p', 'p.id', '=', 'i.'.$productIdCol);
                })
                ->whereBetween('s.'.$salesDateCol, [$start, $end]);

            $selects = [];
            if ($productIdCol) { $selects[] = 'i.'.$productIdCol.' as product_id'; }
            if ($productNameCol) {
                $selects[] = 'i.'.$productNameCol.' as product_name';
            } elseif ($hasProducts && $productIdCol) {
                $selects[] = 'p.name as product_name';
            } else {
                $selects[] = DB::raw("CONCAT('Product ', COALESCE(i.".($productIdCol ?: 'id').", '')) as product_name");
            }
            if ($revenueCol) {
                $selects[] = DB::raw('SUM(i.'.$revenueCol.') as revenue');
            } elseif ($qtyCol && $priceCol) {
                $selects[] = DB::raw('SUM(i.'.$qtyCol.' * i.'.$priceCol.') as revenue');
            } elseif ($qtyCol && $productsHavePrice && $productIdCol) {
                $selects[] = DB::raw('SUM(i.'.$qtyCol.' * p.price) as revenue');
            } else {
                $selects[] = DB::raw('SUM(0) as revenue');
            }

            // Determine proper GROUP BY columns (avoid grouping by revenue aggregate)
            $groupByCols = [];
            if ($productIdCol) {
                $groupByCols[] = 'i.'.$productIdCol;
            }
            if ($productNameCol) {
                $groupByCols[] = 'i.'.$productNameCol;
            } elseif ($hasProducts && $productIdCol) {
                $groupByCols[] = 'p.name';
            } else {
                // Must match the raw expression used for product_name select
                $groupByCols[] = DB::raw("CONCAT('Product ', COALESCE(i.".($productIdCol ?: 'id').", ''))");
            }

            $rows = $query->select($selects)
                ->groupBy($groupByCols)
                ->orderByDesc('revenue')
                ->limit(5)
                ->get();

            $rank = 1;
            foreach ($rows as $r) {
                $rev = (float) round($r->revenue ?? 0, 2);
                $pct = $totalMonthRevenue > 0 ? round(($rev / $totalMonthRevenue) * 100, 2) : 0.0;
                $topProducts[] = [
                    'rank' => $rank++,
                    'product_id' => isset($r->product_id) ? (int)$r->product_id : null,
                    'product_name' => (string) $r->product_name,
                    'revenue' => $rev,
                    'percent' => $pct,
                ];
            }
        }

        // Fallback total revenue if items are not available or revenue was zero
        if ($totalMonthRevenue <= 0.0) {
            $totalMonthRevenue = 0.0;
            if ($salesTable) {
                $totalMonthRevenue = (float) $this->sumTableInRange($salesTable, ['date','dt','created_at'], ['total_amount','total','amount','grand_total'], $start, $end);
            }
        }

        return [
            'year' => (int)$year,
            'month' => (int)$month,
            'label' => $label,
            'total_month_revenue' => (float) round($totalMonthRevenue, 2),
            'top_products' => $topProducts,
        ];
    }
}
