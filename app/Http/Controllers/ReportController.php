<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
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
}
