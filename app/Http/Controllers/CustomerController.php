<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Controllers\Concerns\ExportsDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    use ExportsDataTable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::select(['id', 'name', 'phone', 'created_at']);
            
            // Apply date filters
            $customers = $this->applyDateFilters($customers, $request, 'created_at');
            
            return DataTables::of($customers)
                ->editColumn('created_at', function($customer) {
                    return $customer->created_at->toISOString();
                })
                ->addColumn('phone', function($customer) {
                    $phone = preg_replace('/\D+/', '', $customer->phone);
                    if (!empty($phone)) {
                        if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                        $waLink = 'https://wa.me/' . $phone . '';
                    }
                    return "<a href=\"{$waLink}\" target=\"_blank\" rel=\"noopener\"
                        class=\"inline-flex items-center gap-x-1.5 rounded-lg bg-green-50 px-2.5 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 transition-colors duration-200\">
                            <svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\" fill=\"currentColor\" class=\"h-3 w-3\">
                            <path d=\"M20.52 3.48A11.74 11.74 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.11.55 4.16 1.59 5.98L0 24l6.18-1.62A11.93 11.93 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.2-1.25-6.21-3.48-8.52zM12 22a9.93 9.93 0 0 1-5.08-1.41l-.36-.21-3.66.96.98-3.56-.24-.37A9.92 9.92 0 1 1 22 12c0 5.52-4.48 10-10 10zm5.49-7.26c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.48-1.75-1.65-2.05-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.38-.03-.53-.07-.15-.67-1.62-.92-2.22-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.53.08-.8.38-.27.3-1.05 1.03-1.05 2.5s1.08 2.9 1.23 3.1c.15.2 2.13 3.25 5.16 4.56.72.31 1.28.49 1.72.63.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.44.25-.71.25-1.32.17-1.45-.08-.13-.27-.2-.57-.35z\"/>
                            </svg>
                        </a>".$customer->phone;
                })
                ->rawColumns(['action', 'phone'])
                ->make(true);
        }

        return view('master-data.customers.index');
    }

    /**
     * Export customers to Excel.
     */
    public function exportExcel(Request $request)
    {
        $customers = Customer::select(['id', 'name', 'phone', 'created_at']);
        
        // Apply date filters
        $customers = $this->applyDateFilters($customers, $request, 'created_at');
        
        $data = $customers->get()->map(function($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'created_at' => $customer->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();
        
        $headers = [
            'id' => 'ID',
            'name' => 'Name', 
            'phone' => 'Phone',
            'created_at' => 'Created At'
        ];
        
        return $this->exportWithImages($data, $headers, 'customers');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master-data.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request)
    {
        Customer::create($request->validated());

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('master-data.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('master-data.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'Customer deleted successfully.']);
            }

            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Error deleting customer.'], 500);
            }

            return redirect()->route('customers.index')->with('error', 'Error deleting customer.');
        }
    }
}