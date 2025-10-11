<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FormulaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\TransactionProductionController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Master Data Routes
    Route::resource('users', UserController::class);
    Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
    Route::resource('customers', CustomerController::class);
    Route::get('customers/export/excel', [CustomerController::class, 'exportExcel'])->name('customers.export.excel');
    Route::resource('materials', MaterialController::class);
    Route::get('materials/export/excel', [MaterialController::class, 'exportExcel'])->name('materials.export.excel');
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers/export/excel', [SupplierController::class, 'exportExcel'])->name('suppliers.export.excel');
    Route::resource('formulas', FormulaController::class);
    Route::get('formulas/export/excel', [FormulaController::class, 'exportExcel'])->name('formulas.export.excel');
    Route::resource('products', ProductController::class);
    Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    
    // Transaction Routes
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::get('purchase-orders/export/excel', [PurchaseOrderController::class, 'exportExcel'])->name('purchase-orders.export.excel');
    Route::post('purchase-orders/{purchaseOrder}/complete', [PurchaseOrderController::class, 'complete'])->name('purchase-orders.complete');
    
    // Production Routes
    Route::resource('productions', TransactionProductionController::class);
    Route::get('productions/export/excel', [TransactionProductionController::class, 'exportExcel'])->name('productions.export.excel');
    Route::post('productions/{production}/complete', [TransactionProductionController::class, 'complete'])->name('productions.complete');
    Route::post('productions/check-material-stock', [TransactionProductionController::class, 'checkMaterialStock'])->name('productions.check-material-stock');
    
    // Sales Order Routes
    Route::resource('sales-orders', SalesOrderController::class);
    Route::get('sales-orders/export/excel', [SalesOrderController::class, 'exportExcel'])->name('sales-orders.export.excel');
    Route::post('sales-orders/{order}/complete', [SalesOrderController::class, 'complete'])->name('sales-orders.complete');
    Route::post('sales-orders/check-stock', [SalesOrderController::class, 'checkStock'])->name('sales-orders.check-stock');

    // Invoice Routes
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/export/excel', [InvoiceController::class, 'exportExcel'])->name('invoices.export.excel');
    Route::get('invoices/{invoice}/export', [InvoiceController::class, 'exportPdf'])->name('invoices.export');

    // Payment Routes
    Route::resource('payments', \App\Http\Controllers\PaymentController::class);
    Route::get('payments/export/excel', [\App\Http\Controllers\PaymentController::class, 'exportExcel'])->name('payments.export.excel');
    Route::get('payments/{payment}/export', [\App\Http\Controllers\PaymentController::class, 'exportPdf'])->name('payments.export');

    // General Ledger (Buku Besar)
    Route::get('ledger', [\App\Http\Controllers\GeneralLedgerController::class, 'index'])->name('ledger.index');
    Route::get('ledger/export/excel', [\App\Http\Controllers\GeneralLedgerController::class, 'exportExcel'])->name('ledger.export.excel');
    Route::get('ledger/{account}', [\App\Http\Controllers\GeneralLedgerController::class, 'show'])->name('ledger.show');

    // Chart of Account
    Route::get('chart-of-account', [\App\Http\Controllers\ChartOfAccountController::class, 'index'])->name('chart.index');

    // Journal Entries
    Route::get('journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'index'])->name('journals.index');
    Route::get('journal-entries/export/excel', [\App\Http\Controllers\JournalEntryController::class, 'exportExcel'])->name('journals.export.excel');

    // Trial Balance
    Route::get('trial-balance', [TrialBalanceController::class, 'index'])->name('trial-balance.index');
    Route::get('trial-balance/export/excel', [TrialBalanceController::class, 'exportExcel'])->name('trial-balance.export.excel');

    // Reports - Profit & Loss
    Route::get('reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit_loss');
    Route::get('reports/profit-loss/export/excel', [ReportController::class, 'exportProfitLoss'])->name('reports.profit-loss.export.excel');
    // Reports - Balance Sheet
    Route::get('reports/balance-sheet', [ReportController::class, 'balanceSheet'])->name('reports.balance_sheet');
    Route::get('reports/balance-sheet/export/excel', [ReportController::class, 'exportBalanceSheet'])->name('reports.balance-sheet.export.excel');
    // Reports - Monthly Growth (JSON for dashboard)
    Route::get('reports/monthly-growth', [ReportController::class, 'monthlyGrowth'])->name('reports.monthly_growth');
    // Reports - Monthly Top Products (JSON series + drilldown)
    Route::get('reports/monthly-top-products', [ReportController::class, 'monthlyTopProducts'])->name('reports.monthly_top_products');
    Route::get('reports/monthly-top-products/{year}/{month}', [ReportController::class, 'monthlyTopProductsDrilldown'])->name('reports.monthly_top_products.drilldown');
    // Reports - Monthly Summary (JSON for dashboard cards)
    Route::get('reports/monthly-summary', [ReportController::class, 'monthlySummary'])->name('reports.monthly_summary');
});

require __DIR__.'/auth.php';
