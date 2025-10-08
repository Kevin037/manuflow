<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;
use Yajra\DataTables\Facades\DataTables;

class TransactionProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $productions = Production::with(['product'])
                ->select(['id', 'no', 'dt', 'product_id', 'qty', 'status', 'notes']);

            return DataTables::of($productions)
                ->addIndexColumn()
                ->addColumn('product_name', function ($production) {
                    return $production->product->name ?? '-';
                })
                ->addColumn('action', function ($production) {
                    $actions = '<div class="flex items-center gap-x-2">';
                    
                    // View button
                    $actions .= '<a href="' . route('productions.show', $production->id) . '" 
                                   class="inline-flex items-center p-2 text-xs font-medium text-blue-700 bg-blue-100 border border-transparent rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200" 
                                   title="View Production">
                                   <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                   </svg>
                               </a>';

                    // Edit button (only for pending)
                    if ($production->status === 'pending') {
                        $actions .= '<a href="' . route('productions.edit', $production->id) . '" 
                                       class="inline-flex items-center p-2 text-xs font-medium text-yellow-700 bg-yellow-100 border border-transparent rounded-lg hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200" 
                                       title="Edit Production">
                                       <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                       </svg>
                                   </a>';
                        
                        // Complete button
                        $actions .= '<button type="button" 
                                       class="complete-btn inline-flex items-center p-2 text-xs font-medium text-green-700 bg-green-100 border border-transparent rounded-lg hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200" 
                                       data-url="' . route('productions.complete', $production->id) . '"
                                       title="Complete Production">
                                       <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                       </svg>
                                   </button>';
                    }

                    // Delete button (only for pending)
                    if ($production->status === 'pending') {
                        $actions .= '<button type="button" 
                                       class="delete-btn inline-flex items-center p-2 text-xs font-medium text-red-700 bg-red-100 border border-transparent rounded-lg hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200" 
                                       data-url="' . route('productions.destroy', $production->id) . '"
                                       title="Delete Production">
                                       <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                       </svg>
                                   </button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('transactions.productions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('formula')->get();
        return view('transactions.productions.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, AccountingService $accounting)
    {
        $request->validate([
            'dt' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $accounting) {
                $production = Production::create([
                    'dt' => $request->dt,
                    'product_id' => $request->product_id,
                    'qty' => $request->qty,
                    'notes' => $request->notes,
                    'status' => 'pending'
                ]);

                // Check material stock availability
                $product = Product::with('formula.formulaDetails.material')->find($request->product_id);
                $stockCheck = $product->checkMaterialStock($request->qty);
                
                if (!$stockCheck['can_produce']) {
                    throw new \Exception('Insufficient material stock for production. ' . $stockCheck['message']);
                }

                // Record production process accounting (WIP -> Finished Goods)
                // Using a notional amount: if product has price, multiply by qty; else use 0.
                $amount = (float)($product->price ?? 0) * (float)$request->qty;
                if($amount>0){
                    $accounting->record('production_process', $production->id, 'Production', $amount, $production->dt->format('Y-m-d'), 'Production initiated '.$production->no);
                }
            });

            return redirect()
                ->route('productions.index')
                ->with('success', 'Production created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Production $production)
    {
        $production->load(['product.formula.formulaDetails.material']);
        return view('transactions.productions.show', compact('production'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Production $production)
    {
        if (!$production->canBeModified()) {
            return redirect()
                ->route('productions.index')
                ->with('error', 'Completed productions cannot be modified.');
        }

        $products = Product::with('formula')->get();
        return view('transactions.productions.edit', compact('production', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Production $production)
    {
        if (!$production->canBeModified()) {
            return redirect()
                ->route('productions.index')
                ->with('error', 'Completed productions cannot be modified.');
        }

        $request->validate([
            'dt' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request, $production) {
                // Check material stock availability
                $product = Product::with('formula.formulaDetails.material')->find($request->product_id);
                $stockCheck = $product->checkMaterialStock($request->qty);
                
                if (!$stockCheck['can_produce']) {
                    throw new \Exception('Insufficient material stock for production. ' . $stockCheck['message']);
                }

                $production->update([
                    'dt' => $request->dt,
                    'product_id' => $request->product_id,
                    'qty' => $request->qty,
                    'notes' => $request->notes,
                ]);
            });

            return redirect()
                ->route('productions.index')
                ->with('success', 'Production updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Production $production)
    {
        try {
            $production->delete();
            return response()->json(['success' => true, 'message' => 'Production deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting production: ' . $e->getMessage()]);
        }
    }

    /**
     * Complete the production and update product stock
     */
    public function complete(Production $production)
    {
        if ($production->status === 'completed') {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Production is already completed.']);
            }
            return redirect()
                ->route('productions.index')
                ->with('error', 'Production is already completed.');
        }

        try {
            DB::transaction(function () use ($production) {
                // Check material stock one more time before completing
                $product = Product::with('formula.formulaDetails.material')->find($production->product_id);
                $stockCheck = $product->checkMaterialStock($production->qty);
                
                if (!$stockCheck['can_produce']) {
                    throw new \Exception('Insufficient material stock for production. ' . $stockCheck['message']);
                }

                // Deduct materials from stock
                $production->updateMaterialStock();
                
                // Add produced quantity to product stock
                $production->updateProductStock();
                
                // Mark production as completed
                $production->update(['status' => 'completed']);
            });

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Production completed successfully! Product stock updated.']);
            }

            return redirect()
                ->route('productions.index')
                ->with('success', 'Production completed successfully! Product stock updated.');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Check material stock for real-time validation
     */
    public function checkMaterialStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:0.01',
        ]);

        $product = Product::with('formula.formulaDetails.material')->find($request->product_id);
        
        if (!$product->formula) {
            return response()->json([
                'can_produce' => false,
                'message' => 'Product has no formula defined.',
                'materials' => []
            ]);
        }

        $stockCheck = $product->checkMaterialStock($request->qty);

        return response()->json($stockCheck);
    }
}
