<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Material;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Concerns\ExportsDataTable;

class SupplierController extends Controller
{
    use ExportsDataTable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = $this->applyDateFilters(Supplier::select(['id', 'name', 'phone', 'created_at']), $request);
            
            return DataTables::of($suppliers)
                ->editColumn('created_at', function($supplier) {
                    return $supplier->created_at->toISOString();
                })
                ->addColumn('action', function($supplier) {
                    return view('master-data.suppliers.actions', compact('supplier'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master-data.suppliers.index');
    }

    public function exportExcel(Request $request)
    {
        $query = $this->applyDateFilters(Supplier::select(['id', 'name', 'phone', 'created_at']), $request);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $rows = $query->orderBy('id')->get()->map(function($s) {
            return [
                'name' => $s->name,
                'phone' => $s->phone,
                'created_at' => $s->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $headings = [
            'name' => 'Name',
            'phone' => 'Phone',
            'created_at' => 'Created',
        ];

        return $this->exportWithImages($rows, $headings, null, 'suppliers_export');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availableMaterials = Material::whereNull('supplier_id')->get();
        return view('master-data.suppliers.create', compact('availableMaterials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierStoreRequest $request)
    {
        $data = $request->validated();
        
        $supplier = Supplier::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        // Assign selected materials to this supplier
        if (!empty($data['materials'])) {
            Material::whereIn('id', $data['materials'])->update(['supplier_id' => $supplier->id]);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load('materials');
        return view('master-data.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        $allMaterials = Material::with('supplier')->get();
        $assignedMaterials = $supplier->materials->pluck('id')->toArray();
        
        return view('master-data.suppliers.edit', compact('supplier', 'allMaterials', 'assignedMaterials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierUpdateRequest $request, Supplier $supplier)
    {
        $data = $request->validated();
        
        $supplier->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);

        // Remove current material assignments
        Material::where('supplier_id', $supplier->id)->update(['supplier_id' => null]);
        
        // Assign selected materials to this supplier
        if (!empty($data['materials'])) {
            Material::whereIn('id', $data['materials'])->update(['supplier_id' => $supplier->id]);
        }

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // Remove material assignments before deleting
        Material::where('supplier_id', $supplier->id)->update(['supplier_id' => null]);
        
        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully'
        ]);
    }
}