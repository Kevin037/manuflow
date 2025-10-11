<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Http\Controllers\Concerns\ExportsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AccountingService;

class InvoiceController extends Controller
{
    use ExportsDataTable;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::with(['order.customer','payments'])->latest();
            return DataTables::of($invoices)
                ->addIndexColumn()
                ->addColumn('no', fn($invoice) => $invoice->no)
                ->addColumn('order_no', fn($invoice) => $invoice->order?->no ?? '-')
                ->addColumn('dt', fn($invoice) => $invoice->dt->format('d M Y'))
                ->addColumn('total_formatted', function($invoice){
                    $total = $invoice->order?->total ?? 0; return 'Rp '.number_format($total,0,',','.');
                })
                ->addColumn('status', function($invoice){
                    // status derived from payments
                    $orderTotal = $invoice->order?->total ?? 0; $paid = $invoice->payments->sum('amount');
                    $status = $paid >= $orderTotal && $orderTotal>0 ? 'paid' : 'pending';
                    $badgeMap = [
                        'paid' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>',
                        'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>'
                    ];
                    return $badgeMap[$status];
                })
                ->addColumn('action', fn($invoice)=> view('transactions.invoices.actions', compact('invoice')))
                ->rawColumns(['status','action'])
                ->make(true);
        }
        return view('transactions.invoices.index');
    }

    /**
     * Export invoices to Excel.
     */
    public function exportExcel(Request $request)
    {
        $invoices = Invoice::with(['order.customer','payments'])->select(['id', 'no', 'dt', 'status', 'order_id', 'created_at']);
        
        // Apply date filters
        $invoices = $this->applyDateFilters($invoices, $request, 'dt');
        
        // Apply status filter
        if ($request->filled('status')) {
            $invoices->where('status', $request->status);
        }
        
        $data = $invoices->get()->map(function($invoice) {
            return [
                'no' => $invoice->no,
                'dt' => $invoice->dt->format('Y-m-d'),
                'order_no' => $invoice->order ? $invoice->order->no : '-',
                'total_formatted' => 'Rp ' . number_format($invoice->order ? $invoice->order->total : 0, 0, ',', '.'),
                'status' => ucfirst($invoice->status),
                'created_at' => $invoice->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
        
        $headers = [
            'no' => 'Invoice No',
            'dt' => 'Date',
            'order_no' => 'Order No',
            'total_formatted' => 'Total',
            'status' => 'Status',
            'created_at' => 'Created At'
        ];
        
        return $this->exportWithImages($data, $headers, 'invoices');
    }

    public function create()
    {
        $orders = Order::with('orderDetails.product','customer')->get();
        return view('transactions.invoices.create', [
            'orders' => $orders,
        ]);
    }

    public function store(InvoiceStoreRequest $request, AccountingService $accounting)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $invoice = Invoice::create([
                'dt' => $data['dt'],
                'order_id' => $data['order_id'],
                'status' => 'unpaid'
            ]);
            // Post accounting journal for invoice sent
            $invoice->load('order');
            $amount = (float)($invoice->order?->total ?? 0);
            if($amount > 0){
                $accounting->record('invoice_sent', $invoice->id, 'Invoice', $amount, $invoice->dt->format('Y-m-d'), 'Invoice created '.$invoice->no);
            }
            DB::commit();
            return redirect()->route('invoices.index')->with('success','Invoice created successfully');
        } catch(\Exception $e){
            DB::rollBack();
            return back()->withInput()->with('error','Failed to create invoice: '.$e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('order.orderDetails.product','order.customer','payments');
        return view('transactions.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('order.orderDetails.product','order.customer');
        $orders = Order::with('orderDetails.product','customer')->get();
        return view('transactions.invoices.edit', compact('invoice','orders'));
    }

    public function update(InvoiceUpdateRequest $request, Invoice $invoice)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $invoice->update([
                'dt' => $data['dt'],
                'order_id' => $data['order_id']
            ]);
            DB::commit();
            return redirect()->route('invoices.index')->with('success','Invoice updated successfully');
        } catch(\Exception $e){
            DB::rollBack();
            return back()->withInput()->with('error','Failed to update invoice: '.$e->getMessage());
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            $invoice->delete();
            return response()->json(['success'=>true,'message'=>'Invoice deleted successfully']);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>'Failed to delete invoice: '.$e->getMessage()],500);
        }
    }

    public function exportPdf(Invoice $invoice)
    {
        $invoice->load('order.orderDetails.product','order.customer','payments');
        $pdf = Pdf::loadView('transactions.invoices.pdf', [
            'invoice' => $invoice,
            'generatedAt' => now()
        ])->setPaper('A4','portrait');
        $filename = str_replace('/','-', $invoice->no).'.pdf';
        return $pdf->stream($filename); // open in browser tab
    }
}
