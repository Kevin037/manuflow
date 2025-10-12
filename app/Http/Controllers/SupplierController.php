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
                ->addColumn('phone', function($supplier) {
                    $phone = preg_replace('/\D+/', '', $supplier->phone);
                    if (!empty($phone)) {
                        if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                        $waLink = 'https://wa.me/' . $phone . '';
                    }
                    return "<a href=\"{$waLink}\" target=\"_blank\" rel=\"noopener\"
                        class=\"inline-flex items-center gap-x-1.5 rounded-lg bg-green-50 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 transition-colors duration-200\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"currentColor\" class=\"h-3 w-3\">
                            <path d=\"M20.52 3.48A11.74 11.74 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.59 5.98L0 24l6.18-1.62A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.52zM12 22a9.93 9.93 0 0 1-5.08-1.41l-.36-.21-3.66.96.98-3.56-.24-.37A9.92 9.92 0 1 1 22 12c0 5.52-4.48 10-10 10zm5.49-7.26c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.38-.03-.53-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.49 1.72.63.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.44.25-.71.25-1.32.17-1.45-.08-.13-.27-.2-.57-.35z\"/>
                            </svg>
                        </a>".$supplier->phone;
                })
                ->rawColumns(['action', 'phone'])
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