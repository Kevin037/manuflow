<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dt' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.qty' => 'required|numeric|min:0.01'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'dt.required' => 'Purchase order date is required',
            'dt.date' => 'Purchase order date must be a valid date',
            'supplier_id.required' => 'Supplier is required',
            'supplier_id.exists' => 'Selected supplier is invalid',
            'materials.required' => 'At least one material is required',
            'materials.min' => 'At least one material is required',
            'materials.*.material_id.required' => 'Material is required',
            'materials.*.material_id.exists' => 'Selected material is invalid',
            'materials.*.qty.required' => 'Quantity is required',
            'materials.*.qty.numeric' => 'Quantity must be a number',
            'materials.*.qty.min' => 'Quantity must be greater than 0'
        ];
    }
}