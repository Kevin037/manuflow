<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentStoreRequest;
use App\Http\Requests\PaymentUpdateRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Http\Controllers\Concerns\ExportsDataTable;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    use ExportsDataTable;
    public function index(Request $request)
    {
        if($request->ajax()){
            $payments = Payment::with('invoice.order.customer')->latest();
            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('no', fn($p)=> $p->no)
                ->addColumn('dt', fn($p)=> optional($p->paid_at ?? $p->created_at)->format('d M Y'))
                ->addColumn('invoice_no', fn($p)=> $p->invoice?->no)
                ->addColumn('payment_type', fn($p)=> $p->payment_type_badge)
                ->addColumn('amount_formatted', fn($p)=> 'Rp '.number_format($p->amount,0,',','.'))
                ->addColumn('action', fn($p)=> view('transactions.payments.actions', ['payment'=>$p]))
                ->rawColumns(['payment_type','action'])
                ->make(true);
        }
        return view('transactions.payments.index');
    }

    /**
     * Export payments to Excel.
     */
    public function exportExcel(Request $request)
    {
        $payments = Payment::with('invoice')->select(['id', 'no', 'paid_at', 'amount', 'payment_type', 'invoice_id', 'created_at']);
        
        // Apply date filters
        $payments = $this->applyDateFilters($payments, $request, 'paid_at');
        
        // Apply payment type filter
        if ($request->filled('payment_type')) {
            $payments->where('payment_type', $request->payment_type);
        }
        
        $data = $payments->get()->map(function($payment) {
            return [
                'no' => $payment->no,
                'paid_at' => $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '-',
                'invoice_no' => $payment->invoice ? $payment->invoice->no : '-',
                'payment_type' => ucfirst(str_replace('_', ' ', $payment->payment_type)),
                'amount_formatted' => 'Rp ' . number_format($payment->amount, 0, ',', '.'),
                'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
        
        $headers = [
            'no' => 'Payment No',
            'paid_at' => 'Paid At',
            'invoice_no' => 'Invoice No',
            'payment_type' => 'Payment Type',
            'amount_formatted' => 'Amount',
            'created_at' => 'Created At'
        ];
        
        return $this->exportWithImages($data, $headers, 'payments');
    }

    public function create()
    {
        $invoices = Invoice::with('order.orderDetails.product','order.customer','payments')->get();
        return view('transactions.payments.create', compact('invoices'));
    }

    public function store(PaymentStoreRequest $request, AccountingService $accounting)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $invoice = Invoice::with('order.orderDetails.product','payments')->findOrFail($data['invoice_id']);
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'payment_type' => $data['payment_type'],
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_account_type' => $data['bank_account_type'] ?? null,
                'amount' => $data['amount'],
                'paid_at' => $data['dt'].' '.now()->format('H:i:s'),
            ]);

            // Reduce stock for each product in the order details
            foreach($invoice->order?->orderDetails ?? [] as $detail){
                if($detail->product){
                    $detail->product->updateStock($detail->qty, 'subtract');
                }
            }

            // Record accounting journal entries (payment received)
            $accounting->record('payment_received', $payment->id, 'Payment', (float)$payment->amount, $payment->paid_at->format('Y-m-d'), 'Payment received for invoice '.$invoice->no);
            DB::commit();
            return redirect()->route('payments.index')->with('success','Payment recorded successfully');
        } catch(\Exception $e){
            DB::rollBack();
            return back()->withInput()->with('error','Failed to record payment: '.$e->getMessage());
        }
    }

    public function show(Payment $payment)
    {
        $payment->load('invoice.order.orderDetails.product','invoice.order.customer');
        return view('transactions.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load('invoice.order.orderDetails.product');
        $invoices = Invoice::with('order.orderDetails.product','order.customer','payments')->get();
        return view('transactions.payments.edit', compact('payment','invoices'));
    }

    public function update(PaymentUpdateRequest $request, Payment $payment)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $payment->update([
                'payment_type' => $data['payment_type'],
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_account_type' => $data['bank_account_type'] ?? null,
                'amount' => $data['amount'],
                'paid_at' => $data['dt'].' '.optional($payment->paid_at)->format('H:i:s') ?? now()->format('H:i:s'),
            ]);
            DB::commit();
            return redirect()->route('payments.index')->with('success','Payment updated successfully');
        } catch(\Exception $e){
            DB::rollBack();
            return back()->withInput()->with('error','Failed to update payment: '.$e->getMessage());
        }
    }

    public function destroy(Payment $payment)
    {
        try {
            $payment->delete();
            return response()->json(['success'=>true,'message'=>'Payment deleted successfully']);
        } catch(\Exception $e){
            return response()->json(['success'=>false,'message'=>'Failed to delete payment: '.$e->getMessage()],500);
        }
    }

    public function exportPdf(Payment $payment)
    {
        $payment->load('invoice.order.orderDetails.product','invoice.order.customer');
        $pdf = Pdf::loadView('transactions.payments.pdf', [
            'payment' => $payment,
            'generatedAt' => now()
        ])->setPaper('A4','portrait');
        $filename = str_replace('/','-', $payment->no).'.pdf';
        return $pdf->stream($filename);
    }
}
