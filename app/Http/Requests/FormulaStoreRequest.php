<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FormulaStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:formulas,name',
            'no' => 'required|string|max:100|unique:formulas,no',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.qty' => 'required|numeric|min:0.01'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Formula name is required',
            'name.unique' => 'This formula name already exists',
            'no.required' => 'Formula code is required',
            'no.unique' => 'This formula code already exists',
            'materials.required' => 'At least one material is required',
            'materials.min' => 'At least one material is required',
            'materials.*.material_id.required' => 'Material selection is required',
            'materials.*.material_id.exists' => 'Selected material is invalid',
            'materials.*.qty.required' => 'Quantity is required',
            'materials.*.qty.numeric' => 'Quantity must be a number',
            'materials.*.qty.min' => 'Quantity must be greater than 0'
        ];
    }
}
