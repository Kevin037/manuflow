<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Http\Controllers\Concerns\ExportsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    use ExportsDataTable;
    public function __construct(private ReportService $reportService) {}

    public function profitLoss(Request $request)
    {
        $startDefault = now()->startOfMonth()->toDateString();
        $endDefault = now()->toDateString();

        // Validate dates
        $validated = $request->validate([
            'start_date' => ['nullable','date_format:Y-m-d'],
            'end_date' => ['nullable','date_format:Y-m-d','after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date'] ?? $startDefault;
        $endDate = $validated['end_date'] ?? $endDefault;

        $data = $this->reportService->calculateProfitLoss($startDate, $endDate);
        $detailed = $this->reportService->calculateProfitLossDetailed($startDate, $endDate);

        return view('reports.profit_loss', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'results' => $data,
            'detailed' => $detailed,
        ]);
    }

    public function balanceSheet(Request $request)
    {
        $validated = $request->validate([
            'as_of' => ['nullable','date_format:Y-m-d'],
            'pl_start_date' => ['nullable','date_format:Y-m-d'],
            'pl_end_date' => ['nullable','date_format:Y-m-d','after_or_equal:pl_start_date'],
        ]);

        $asOf = $validated['as_of'] ?? now()->toDateString();
        $plStart = $validated['pl_start_date'] ?? Carbon::parse($asOf)->startOfMonth()->toDateString();
        $plEnd = $validated['pl_end_date'] ?? $asOf;

        $data = $this->reportService->calculateBalanceSheet($asOf, $plStart, $plEnd);

        return view('reports.balance_sheet', [
            'as_of' => $asOf,
            'pl_start_date' => $plStart,
            'pl_end_date' => $plEnd,
            'report' => $data,
        ]);
    }

    /**
     * API endpoint: GET /reports/monthly-growth
     * Optional query: months (int, max 36)
     */
    public function monthlyGrowth(Request $request)
    {
        $validated = $request->validate([
            'months' => ['nullable','integer','min:1','max:36'],
        ]);
        $months = (int) ($validated['months'] ?? 12);
        $payload = $this->reportService->getMonthlySalesAndProfit($months);
        return response()->json($payload);
    }

    // Monthly Top Products: time series of total revenues
    public function monthlyTopProducts(Request $request)
    {
        $validated = $request->validate([
            'months' => ['nullable','integer','min:1','max:36'],
        ]);
        $months = (int) ($validated['months'] ?? 12);
        $data = $this->reportService->getMonthlyTopProducts($months);
        return response()->json($data);
    }

    // Drilldown: Top 5 products for selected month
    public function monthlyTopProductsDrilldown(int $year, int $month, Request $request)
    {
        if ($month < 1 || $month > 12) {
            return response()->json(['message' => 'Invalid month'], 422);
        }
        $data = $this->reportService->getTop5ProductsForMonth($year, $month);
        return response()->json($data);
    }

    // Monthly Summary (JSON for dashboard cards)
    public function monthlySummary(Request $request)
    {
        $payload = $this->reportService->getMonthlySummary();
        return response()->json($payload);
    }

    public function exportProfitLoss(Request $request)
    {
        $startDefault = now()->startOfMonth()->toDateString();
        $endDefault = now()->toDateString();

        // Validate dates
        $validated = $request->validate([
            'start_date' => ['nullable','date_format:Y-m-d'],
            'end_date' => ['nullable','date_format:Y-m-d','after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date'] ?? $startDefault;
        $endDate = $validated['end_date'] ?? $endDefault;

        $results = $this->reportService->calculateProfitLoss($startDate, $endDate);
        $detailed = $this->reportService->calculateProfitLossDetailed($startDate, $endDate);

        $groups = $detailed['groups'] ?? [];
        $summary = $detailed['summary'] ?? [];

        $order = ['4','7','5','6','8'];
        $data = [];

        // Summary block (matches the top table on the page)
        $data[] = ['section' => 'Total Sales', 'account' => '', 'amount' => $results['total_sales'] ?? 0];
        $data[] = ['section' => 'Total HPP', 'account' => '', 'amount' => $results['total_hpp'] ?? 0];
        $data[] = ['section' => 'Total Expenses', 'account' => '', 'amount' => $results['total_expenses'] ?? 0];
        $data[] = ['section' => 'Gross Profit', 'account' => '', 'amount' => $results['gross_profit'] ?? 0];
        $data[] = ['section' => 'Net Profit', 'account' => '', 'amount' => $results['net_profit'] ?? 0];
        $data[] = ['section' => '', 'account' => '', 'amount' => '']; // spacer

        foreach ($order as $gkey) {
            if (!isset($groups[$gkey])) { continue; }
            $group = $groups[$gkey];
            $label = strtoupper((string)($group['label'] ?? ''));
            $accounts = $group['accounts'] ?? [];
            $total = (float)($group['total'] ?? 0);

            if (empty($accounts) && abs($total) < 0.0005) {
                continue; // skip empty sections
            }

            // Section header with section total
            $data[] = ['section' => $label, 'account' => '', 'amount' => $total];

            // Detail rows
            foreach ($accounts as $acc) {
                $data[] = [
                    'section' => '',
                    'account' => $acc['name'] ?? '',
                    'amount' => $acc['amount'] ?? 0,
                ];
            }

            // Spacer
            $data[] = ['section' => '', 'account' => '', 'amount' => ''];
        }

    // Summary rows similar to the UI (detailed block)
    $data[] = ['section' => 'GROSS PROFIT', 'account' => '', 'amount' => $summary['gross_profit'] ?? 0];
    $data[] = ['section' => 'NET PROFIT', 'account' => '', 'amount' => $summary['net_profit'] ?? 0];

        return $this->exportWithImages($data, [
            'section' => 'Section',
            'account' => 'Account',
            'amount' => 'Amount',
        ], null, 'profit_loss');
    }

    public function exportBalanceSheet(Request $request)
    {
        $validated = $request->validate([
            'as_of' => ['nullable','date_format:Y-m-d'],
            'pl_start_date' => ['nullable','date_format:Y-m-d'],
            'pl_end_date' => ['nullable','date_format:Y-m-d','after_or_equal:pl_start_date'],
        ]);

        $asOf = $validated['as_of'] ?? now()->toDateString();
        $plStart = $validated['pl_start_date'] ?? Carbon::parse($asOf)->startOfMonth()->toDateString();
        $plEnd = $validated['pl_end_date'] ?? $asOf;

    $report = $this->reportService->calculateBalanceSheet($asOf, $plStart, $plEnd);

    $data = [];

    // Summary block to match the page summary card
    $data[] = ['section' => 'Assets (Total)', 'account' => '', 'amount' => $report['assets_total'] ?? 0];
    $data[] = ['section' => 'Liabilities (Total)', 'account' => '', 'amount' => $report['liabilities_total'] ?? 0];
    $data[] = ['section' => 'Equity (incl. Net Profit)', 'account' => '', 'amount' => $report['equities_total'] ?? 0];
    $data[] = ['section' => 'Balanced', 'account' => '', 'amount' => isset($report['balanced']) && $report['balanced'] ? 1 : 0];
    $data[] = ['section' => '', 'account' => '', 'amount' => '']; // spacer

        // Assets
        $assets = $report['assets_breakdown'] ?? [];
        $assetsTotal = (float)($report['assets_total'] ?? 0);
        if (!empty($assets) || abs($assetsTotal) > 0) {
            $data[] = ['section' => 'ASSETS', 'account' => '', 'amount' => $assetsTotal];
            foreach ($assets as $row) {
                $data[] = [
                    'section' => '',
                    'account' => $row['account_name'] ?? '',
                    'amount' => $row['amount'] ?? 0,
                ];
            }
            $data[] = ['section' => '', 'account' => '', 'amount' => ''];
        }

        // Liabilities
        $liabs = $report['liabilities_breakdown'] ?? [];
        $liabsTotal = (float)($report['liabilities_total'] ?? 0);
        if (!empty($liabs) || abs($liabsTotal) > 0) {
            $data[] = ['section' => 'LIABILITIES', 'account' => '', 'amount' => $liabsTotal];
            foreach ($liabs as $row) {
                $data[] = [
                    'section' => '',
                    'account' => $row['account_name'] ?? '',
                    'amount' => $row['amount'] ?? 0,
                ];
            }
            $data[] = ['section' => '', 'account' => '', 'amount' => ''];
        }

        // Equity (+ Net Profit)
        $equities = $report['equities_breakdown'] ?? [];
        $equitiesTotal = (float)($report['equities_total'] ?? 0);
        $netProfit = (float)($report['net_profit'] ?? 0);
        if (!empty($equities) || abs($equitiesTotal) > 0 || abs($netProfit) > 0) {
            $data[] = ['section' => 'EQUITY', 'account' => '', 'amount' => $equitiesTotal];
            foreach ($equities as $row) {
                $data[] = [
                    'section' => '',
                    'account' => $row['account_name'] ?? '',
                    'amount' => $row['amount'] ?? 0,
                ];
            }
            // Net profit line consistent with UI
            $data[] = ['section' => '', 'account' => 'Net Profit (from P&L)', 'amount' => $netProfit];
        }

        return $this->exportWithImages($data, [
            'section' => 'Section',
            'account' => 'Account',
            'amount' => 'Amount',
        ], null, 'balance_sheet');
    }
}
