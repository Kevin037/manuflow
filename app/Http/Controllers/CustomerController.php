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