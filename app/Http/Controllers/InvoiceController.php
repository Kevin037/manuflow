<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
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

    public function create()
    {
        $orders = Order::with('orderDetails.product','customer')->get();
        return view('transactions.invoices.create', [
            'orders' => $orders,
        ]);
    }

    public function store(InvoiceStoreRequest $request)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $invoice = Invoice::create([
                'dt' => $data['dt'],
                'order_id' => $data['order_id'],
                'status' => 'unpaid'
            ]);
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
        // Placeholder - integrate dompdf/snappy later
        return redirect()->back()->with('info','PDF export not implemented yet');
    }
}
