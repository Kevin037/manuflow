<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Models\Formula;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with('formula')->latest();

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('photo', function($product) {
                    if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                        return '<img src="' . Storage::url($product->photo) . '" alt="' . $product->name . '" class="h-12 w-12 object-cover rounded-lg">';
                    }
                    return '<div class="h-12 w-12 bg-gray-200 rounded-lg flex items-center justify-center">
                              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                              </svg>
                            </div>';
                })
                ->addColumn('sku', function($product) {
                    return $product->sku;
                })
                ->addColumn('name', function($product) {
                    return $product->name;
                })
                ->addColumn('qty', function($product) {
                    $stockClass = $product->qty > 10 ? 'text-green-600' : ($product->qty > 0 ? 'text-yellow-600' : 'text-red-600');
                    return '<span class="font-medium ' . $stockClass . '">' . number_format($product->qty, 0) . '</span>';
                })
                ->addColumn('formatted_price', function($product) {
                    return 'Rp ' . number_format($product->price, 0, ',', '.');
                })
                ->addColumn('formula_name', function($product) {
                    return $product->formula ? $product->formula->name : '-';
                })
                ->addColumn('action', function($product) {
                    return view('master-data.products.actions', compact('product'));
                })
                ->rawColumns(['photo', 'qty', 'action'])
                ->make(true);
        }

        return view('master-data.products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formulas = Formula::all();
        return view('master-data.products.create', compact('formulas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('products', $filename, 'public');
                $data['photo'] = $path;
            }

            Product::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully'
                ]);
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Product created successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('formula.formulaDetails.material');
        return view('master-data.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $formulas = Formula::all();
        return view('master-data.products.edit', compact('product', 'formulas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $data = $request->validated();

        try {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                    Storage::disk('public')->delete($product->photo);
                }

                $photo = $request->file('photo');
                $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
                $path = $photo->storeAs('products', $filename, 'public');
                $data['photo'] = $path;
            }

            $product->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully'
                ]);
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Delete photo if exists
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }
}