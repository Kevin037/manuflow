<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderStoreRequest;
use App\Http\Requests\PurchaseOrderUpdateRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Models\Material;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $purchaseOrders = PurchaseOrder::with('supplier')->latest();

            return DataTables::of($purchaseOrders)
                ->addIndexColumn()
                ->addColumn('no', function($purchaseOrder) {
                    return $purchaseOrder->no;
                })
                ->addColumn('supplier_name', function($purchaseOrder) {
                    return $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-';
                })
                ->addColumn('dt', function($purchaseOrder) {
                    return $purchaseOrder->dt->format('d M Y');
                })
                ->addColumn('total_formatted', function($purchaseOrder) {
                    return $purchaseOrder->total_formatted;
                })
                ->addColumn('status', function($purchaseOrder) {
                    return $purchaseOrder->status_badge;
                })
                ->addColumn('action', function($purchaseOrder) {
                    return view('transactions.purchase-orders.actions', compact('purchaseOrder'));
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('transactions.purchase-orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $materials = Material::all();
        return view('transactions.purchase-orders.create', compact('suppliers', 'materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseOrderStoreRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'dt' => $data['dt'],
                'supplier_id' => $data['supplier_id'],
                'total' => 0,
                'status' => 'pending'
            ]);

            // Create purchase order details
            if (isset($data['materials']) && is_array($data['materials'])) {
                foreach ($data['materials'] as $materialData) {
                    if (!empty($materialData['material_id']) && !empty($materialData['qty'])) {
                        PurchaseOrderDetail::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'material_id' => $materialData['material_id'],
                            'qty' => $materialData['qty']
                        ]);
                    }
                }
            }

            // Calculate total and generate transaction number
            $purchaseOrder->calculateTotal();

            DB::commit();

            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'purchaseOrderDetails.material');
        return view('transactions.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeModified()) {
            return redirect()
                ->route('purchase-orders.index')
                ->with('error', 'Cannot edit completed purchase order');
        }

        $purchaseOrder->load('purchaseOrderDetails.material');
        $suppliers = Supplier::all();
        $materials = Material::all();
        return view('transactions.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseOrderUpdateRequest $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeModified()) {
            return redirect()
                ->route('purchase-orders.index')
                ->with('error', 'Cannot update completed purchase order');
        }

        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Update purchase order
            $purchaseOrder->update([
                'dt' => $data['dt'],
                'supplier_id' => $data['supplier_id']
            ]);

            // Delete existing purchase order details
            $purchaseOrder->purchaseOrderDetails()->delete();

            // Create new purchase order details
            if (isset($data['materials']) && is_array($data['materials'])) {
                foreach ($data['materials'] as $materialData) {
                    if (!empty($materialData['material_id']) && !empty($materialData['qty'])) {
                        PurchaseOrderDetail::create([
                            'purchase_order_id' => $purchaseOrder->id,
                            'material_id' => $materialData['material_id'],
                            'qty' => $materialData['qty']
                        ]);
                    }
                }
            }

            // Recalculate total
            $purchaseOrder->calculateTotal();

            DB::commit();

            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase order updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeModified()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete completed purchase order'
            ], 400);
        }

        try {
            $purchaseOrder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark purchase order as completed
     */
    public function complete(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeModified()) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order is already completed'
            ], 400);
        }

        try {
            $purchaseOrder->markAsCompleted();

            return response()->json([
                'success' => true,
                'message' => 'Purchase order completed successfully. Material stock has been updated.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete purchase order: ' . $e->getMessage()
            ], 500);
        }
    }
}