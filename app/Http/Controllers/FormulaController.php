<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormulaStoreRequest;
use App\Http\Requests\FormulaUpdateRequest;
use App\Models\Formula;
use App\Models\FormulaDetail;
use App\Models\Material;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class FormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $formulas = Formula::withCount('formulaDetails')->latest();

            return DataTables::of($formulas)
                ->addIndexColumn()
                ->addColumn('no', function($formula) {
                    return $formula->no;
                })
                ->addColumn('name', function($formula) {
                    return $formula->name;
                })
                ->addColumn('total_formatted', function($formula) {
                    return 'Rp ' . number_format($formula->total, 0, ',', '.');
                })
                ->addColumn('materials_count', function($formula) {
                    return $formula->formula_details_count;
                })
                ->addColumn('action', function($formula) {
                    return view('master-data.formulas.actions', compact('formula'));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master-data.formulas.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $materials = Material::all();
        return view('master-data.formulas.create', compact('materials'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FormulaStoreRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Create formula
            $formula = Formula::create([
                'name' => $data['name'],
                'no' => $data['no'],
                'total' => 0
            ]);

            // Create formula details
            if (isset($data['materials']) && is_array($data['materials'])) {
                foreach ($data['materials'] as $materialData) {
                    if (!empty($materialData['material_id']) && !empty($materialData['qty'])) {
                        FormulaDetail::create([
                            'formula_id' => $formula->id,
                            'material_id' => $materialData['material_id'],
                            'qty' => $materialData['qty']
                        ]);
                    }
                }
            }

            // Calculate total
            $formula->calculateTotal();

            DB::commit();

            return redirect()
                ->route('formulas.index')
                ->with('success', 'Formula created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create formula: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Formula $formula)
    {
        $formula->load('formulaDetails.material');
        return view('master-data.formulas.show', compact('formula'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formula $formula)
    {
        $formula->load('formulaDetails.material');
        $materials = Material::all();
        return view('master-data.formulas.edit', compact('formula', 'materials'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FormulaUpdateRequest $request, Formula $formula)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Update formula
            $formula->update([
                'name' => $data['name'],
                'no' => $data['no']
            ]);

            // Delete existing formula details
            $formula->formulaDetails()->delete();

            // Create new formula details
            if (isset($data['materials']) && is_array($data['materials'])) {
                foreach ($data['materials'] as $materialData) {
                    if (!empty($materialData['material_id']) && !empty($materialData['qty'])) {
                        FormulaDetail::create([
                            'formula_id' => $formula->id,
                            'material_id' => $materialData['material_id'],
                            'qty' => $materialData['qty']
                        ]);
                    }
                }
            }

            // Recalculate total
            $formula->calculateTotal();

            DB::commit();

            return redirect()
                ->route('formulas.index')
                ->with('success', 'Formula updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update formula: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Formula $formula)
    {
        try {
            $formula->delete();

            return response()->json([
                'success' => true,
                'message' => 'Formula deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete formula: ' . $e->getMessage()
            ], 500);
        }
    }
}
