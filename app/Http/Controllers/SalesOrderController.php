<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Http\Controllers\Concerns\ExportsDataTable;
use Illuminate\Http\Request;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SalesOrderController extends Controller
{
    use ExportsDataTable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['customer', 'orderDetails.product'])
                ->select('orders.*');

            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('customer_name', function ($order) {
                    return $order->customer ? $order->customer->name : 'No customer';
                })
                ->addColumn('product_quantity', function ($order) {
                    $details = $order->orderDetails->map(function ($detail) {
                        return $detail->product->name . ' (' . number_format($detail->qty, 0) . ')';
                    })->implode(', ');
                    
                    return $details ?: '-';
                })
                ->addColumn('action', function ($order) {
                    $actions = '<div class="flex items-center gap-x-2">';
                    // WhatsApp button to message customer with order details
                    try {
                        $waLink = null;
                        if ($order->customer && !empty($order->customer->phone)) {
                            $lines = [];
                            $lines[] = 'Sales Order ' . ($order->no ?? '#');
                            $lines[] = 'Date: ' . $order->dt->format('Y-m-d');
                            $lines[] = 'Customer: ' . $order->customer->name;
                            $lines[] = '';
                            $lines[] = 'Items:';
                            foreach (($order->orderDetails ?? []) as $d) {
                                $prodName = optional($d->product)->name ?? 'Unknown';
                                $unit = optional($d->product)->unit ?? 'pcs';
                                $qty = rtrim(rtrim(number_format((float)$d->qty, 2, '.', ''), '0'), '.');
                                $lines[] = '• ' . $prodName . ' — ' . $qty . ($unit ? (' ' . $unit) : '');
                            }
                            $lines[] = '';
                            $lines[] = 'Total: Rp ' . number_format((float)($order->total ?? 0), 0, ',', '.');
                            $lines[] = '';
                            $lines[] = 'Terima kasih.';

                            $message = implode("\n", $lines);
                            $phone = preg_replace('/\D+/', '', $order->customer->phone);
                            if (!empty($phone)) {
                                if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                                $waLink = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
                            }
                        }
                        if ($waLink) {
                            $actions .= '<a href="' . $waLink . '" target="_blank" rel="noopener" class="inline-flex items-center gap-x-1 rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 transition-colors duration-200" title="WhatsApp">'
                                . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-3 w-3">'
                                . '<path d="M20.52 3.48A11.74 11.74 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.59 5.98L0 24l6.18-1.62A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.52zM12 22a9.93 9.93 0 0 1-5.08-1.41l-.36-.21-3.66.96.98-3.56-.24-.37A9.92 9.92 0 1 1 22 12c0 5.52-4.48 10-10 10zm5.49-7.26c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.38-.03-.53-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.49 1.72.63.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.44.25-.71.25-1.32.17-1.45-.08-.13-.27-.2-.57-.35z"/>'
                                . '</svg>'
                                . '</a>';
                        }
                    } catch (\Throwable $th) { /* ignore */ }
                    
                    // View button
                    $actions .= '<a href="' . route('sales-orders.show', $order->id) . '" 
                                   class="inline-flex items-center gap-x-1 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors duration-200"
                                   title="View Details">
                                   <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                   </svg>
                                   View
                               </a>';
                    
                    // Edit and Delete buttons (only for pending orders)
                    if ($order->status === 'pending') {
                        $actions .= '<a href="' . route('sales-orders.edit', $order->id) . '" 
                                       class="inline-flex items-center gap-x-1 rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100 transition-colors duration-200"
                                       title="Edit Order">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                       </svg>
                                       Edit
                                   </a>';
                        
                        $actions .= '<button type="button" 
                                       class="complete-btn inline-flex items-center gap-x-1 rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100 transition-colors duration-200"
                                       data-url="' . route('sales-orders.complete', $order->id) . '"
                                       title="Complete Order">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                       </svg>
                                       Complete
                                   </button>';
                        
                        $actions .= '<button type="button" 
                                       class="delete-btn inline-flex items-center gap-x-1 rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors duration-200"
                                       data-url="' . route('sales-orders.destroy', $order->id) . '"
                                       title="Delete Order">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                       </svg>
                                       Delete
                                   </button>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('transactions.sales-orders.index');
    }

    /**
     * Export sales orders to Excel.
     */
    public function exportExcel(Request $request)
    {
        $orders = Order::with(['customer', 'orderDetails.product'])->select(['id', 'no', 'dt', 'total', 'status', 'customer_id', 'created_at']);
        
        // Apply date filters
        $orders = $this->applyDateFilters($orders, $request, 'dt');
        
        // Apply status filter
        if ($request->filled('status')) {
            $orders->where('status', $request->status);
        }
        
        $data = $orders->get()->map(function($order) {
            $productQuantity = $order->orderDetails->map(function ($detail) {
                return $detail->product->name . ' (' . number_format($detail->qty, 0) . ')';
            })->implode(', ');
            
            return [
                'no' => $order->no,
                'customer_name' => $order->customer ? $order->customer->name : 'No customer',
                'dt' => $order->dt->format('Y-m-d'),
                'product_quantity' => $productQuantity ?: '-',
                'total' => $order->total,
                'status' => ucfirst($order->status),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
        
        $headers = [
            'no' => 'Sales Order No',
            'customer_name' => 'Customer',
            'dt' => 'Date',
            'product_quantity' => 'Products & Quantities',
            'total' => 'Total',
            'status' => 'Status',
            'created_at' => 'Created At'
        ];
        
        return $this->exportWithImages($data, $headers, 'sales-orders');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        return view('transactions.sales-orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AccountingService $accounting)
    {
        $request->validate([
            'dt' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01'
        ]);

        try {
            DB::beginTransaction();

            // Generate order number
            $orderNo = 'SO-' . date('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'no' => $orderNo,
                'dt' => $request->dt,
                'customer_id' => $request->customer_id,
                'total' => 0,
                'status' => 'pending'
            ]);

            $total = 0;

            foreach ($request->products as $detail) {
                $product = Product::findOrFail($detail['product_id']);
                
                // Check stock availability
                if ($product->qty < $detail['qty']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->qty}, Requested: {$detail['qty']}");
                }

                $subtotal = ($product->price ?? 0) * $detail['qty'];

                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty']
                ]);

                // Reduce product stock
                $product->decrement('qty', $detail['qty']);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            // Post sales order journal
            if($total > 0){
                $accounting->record('sales_order', $order->id, 'SalesOrder', (float)$total, $order->dt->format('Y-m-d'), 'Sales order created '.$order->no);
            }
            // Build WhatsApp link for customer notification
            $order->loadMissing(['customer','orderDetails.product']);
            $waLink = null;
            try {
                if ($order->customer && !empty($order->customer->phone)) {
                    $lines = [];
                    $lines[] = 'Sales Order ' . ($order->no ?? '#');
                    $lines[] = 'Date: ' . $order->dt->format('Y-m-d');
                    $lines[] = 'Customer: ' . $order->customer->name;
                    $lines[] = '';
                    $lines[] = 'Items:';
                    foreach (($order->orderDetails ?? []) as $d) {
                        $prodName = optional($d->product)->name ?? 'Unknown';
                        $unit = optional($d->product)->unit ?? 'pcs';
                        $qty = rtrim(rtrim(number_format((float)$d->qty, 2, '.', ''), '0'), '.');
                        $lines[] = '• ' . $prodName . ' — ' . $qty . ($unit ? (' ' . $unit) : '');
                    }
                    $lines[] = '';
                    $lines[] = 'Total: Rp ' . number_format((float)($order->total ?? 0), 0, ',', '.');
                    $lines[] = '';
                    $lines[] = 'Terima kasih.';

                    $message = implode("\n", $lines);
                    $phone = preg_replace('/\D+/', '', $order->customer->phone);
                    if (!empty($phone)) {
                        if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                        $waLink = 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
                    }
                }
            } catch (\Throwable $th) { $waLink = null; }

            DB::commit();

            $redirect = redirect()->route('sales-orders.index')
                ->with('success', 'Sales order created successfully!');
            if ($waLink) { $redirect->with('wa_link', $waLink); }
            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $salesOrder)
    {
        $salesOrder->load(['customer', 'orderDetails.product']);
        
        return view('transactions.sales-orders.show', ['order' => $salesOrder]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $salesOrder)
    {
        if ($salesOrder->status !== 'pending') {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Only pending orders can be edited.');
        }

        $salesOrder->load(['orderDetails.product']);
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        // Pre-compute lightweight arrays for JS (avoid complex arrow fn syntax inside Blade @json)
        $productsForJs = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'qty' => $p->qty,
                'unit' => $p->unit ?? 'pcs',
            ];
        })->values();

        $orderDetailsForJs = $salesOrder->orderDetails->map(function ($d) {
            return [
                'id' => $d->id,
                'product_id' => $d->product_id,
                'qty' => $d->qty,
            ];
        })->values();

        return view('transactions.sales-orders.edit', [
            'order' => $salesOrder,
            'customers' => $customers,
            'products' => $products,
            'productsForJs' => $productsForJs,
            'orderDetailsForJs' => $orderDetailsForJs,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $salesOrder)
    {
        if ($salesOrder->status !== 'pending') {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Only pending orders can be updated.');
        }

        $request->validate([
            'dt' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|numeric|min:0.01'
        ]);

        try {
            DB::beginTransaction();

            // Restore original stock
            foreach ($salesOrder->orderDetails as $originalDetail) {
                $product = Product::find($originalDetail->product_id);
                if ($product) {
                    $product->increment('qty', $originalDetail->qty);
                }
            }

            // Delete old order details
            $salesOrder->orderDetails()->delete();

            // Update order basic info
            $salesOrder->update([
                'dt' => $request->dt,
                'customer_id' => $request->customer_id
            ]);

            $total = 0;

            // Create new order details
            foreach ($request->products as $detail) {
                $product = Product::findOrFail($detail['product_id']);
                
                if ($product->qty < $detail['qty']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->qty}, Requested: {$detail['qty']}");
                }

                $subtotal = ($product->price ?? 0) * $detail['qty'];

                OrderDetail::create([
                    'order_id' => $salesOrder->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty']
                ]);

                $product->decrement('qty', $detail['qty']);
                $total += $subtotal;
            }

            $salesOrder->update(['total' => $total]);

            DB::commit();

            return redirect()->route('sales-orders.show', $salesOrder->id)
                ->with('success', 'Sales order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $salesOrder)
    {
        if ($salesOrder->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be deleted.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Restore stock for all order details
            foreach ($salesOrder->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $product->increment('qty', $detail['qty']);
                }
            }

            $salesOrder->orderDetails()->delete();
            $salesOrder->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales order deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting sales order: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Complete the sales order
     */
    public function complete(Order $order, AccountingService $accounting)
    {
        if ($order->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Sales order is already completed.'
            ]);
        }

        try {
            DB::beginTransaction();

            $order->update(['status' => 'completed']);
            // Optionally record another event if needed (e.g., goods delivered) – skipping unless specified

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales order completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error completing sales order: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Check product stock availability
     */
    public function checkStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01'
        ]);

        $product = Product::find($request->product_id);
        
        if (!$product) {
            return response()->json([
                'available' => false,
                'message' => 'Product not found'
            ]);
        }

        $requestedQty = $request->qty;
        $availableStock = $product->qty;
        
        $isAvailable = $availableStock >= $requestedQty;
        
        return response()->json([
            'available' => $isAvailable,
            'stock_quantity' => $availableStock,
            'requested_quantity' => $requestedQty,
            'message' => $isAvailable ? 'Stock available' : 'Insufficient stock'
        ]);
    }
}
