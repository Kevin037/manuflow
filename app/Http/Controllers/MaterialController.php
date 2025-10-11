<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Supplier;
use App\Http\Requests\MaterialStoreRequest;
use App\Http\Requests\MaterialUpdateRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Concerns\ExportsDataTable;

class MaterialController extends Controller
{
    use ExportsDataTable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $materials = $this->applyDateFilters(Material::with('supplier')->select(['id', 'name', 'qty', 'price', 'supplier_id', 'created_at']), $request);
            
            // Apply supplier filter if provided
            if ($request->filled('supplier_id')) {
                $materials->where('supplier_id', $request->supplier_id);
            }
            
            return DataTables::of($materials)
                ->addColumn('supplier_name', function($material) {
                    return $material->supplier ? $material->supplier->name : 'No Supplier';
                })
                ->editColumn('created_at', function($material) {
                    return $material->created_at->toISOString();
                })
                ->make(true);
        }

        $suppliers = Supplier::all();
        return view('master-data.materials.index', compact('suppliers'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->applyDateFilters(Material::with('supplier')->select(['id', 'name', 'qty', 'price', 'supplier_id', 'created_at']), $request);
        
        // Apply supplier filter if provided
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $rows = $query->orderBy('id')->get()->map(function($m) {
            return [
                'name' => $m->name,
                'qty' => $m->qty,
                'price' => 'Rp ' . number_format($m->price, 0, ',', '.'),
                'supplier_name' => $m->supplier ? $m->supplier->name : 'No Supplier',
                'created_at' => $m->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $headings = [
            'name' => 'Name',
            'qty' => 'Quantity',
            'price' => 'Price',
            'supplier_name' => 'Supplier',
            'created_at' => 'Created',
        ];

        return $this->exportWithImages($rows, $headings, null, 'materials_export');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view('master-data.materials.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaterialStoreRequest $request)
    {
        $data = $request->validated();
        
        // Set default quantity if not provided
        if (!isset($data['qty'])) {
            $data['qty'] = 0;
        }
        
        // Handle supplier assignment
        if (!isset($data['supplier_id']) || !$data['supplier_id']) {
            // Get the first supplier or create a default one
            $supplier = Supplier::first();
            if (!$supplier) {
                $supplier = Supplier::create([
                    'name' => 'Default Supplier',
                    'phone' => '0000000000'
                ]);
            }
            $data['supplier_id'] = $supplier->id;
        }
        
        Material::create($data);

        return redirect()->route('materials.index')->with('success', 'Material created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        $material->load('supplier');
        return view('master-data.materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        return view('master-data.materials.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialUpdateRequest $request, Material $material)
    {
        $data = $request->validated();
        $material->update($data);

        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        try {
            $material->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Material deleted successfully.']);
            }

            return redirect()->route('materials.index')->with('success', 'Material deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting material.'], 500);
            }

            return redirect()->route('materials.index')->with('error', 'Error deleting material.');
        }
    }
}