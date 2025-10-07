<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->route('product')->id;

        return [
            'name' => 'required|string|max:150',
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($productId)
            ],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|numeric|min:0',
            'formula_id' => 'required|exists:formulas,id'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'product name',
            'sku' => 'SKU',
            'photo' => 'product photo',
            'price' => 'price',
            'qty' => 'stock quantity',
            'formula_id' => 'formula'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name must not exceed 150 characters.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU already exists.',
            'sku.max' => 'SKU must not exceed 100 characters.',
            'photo.image' => 'Product photo must be an image.',
            'photo.mimes' => 'Product photo must be a file of type: jpeg, png, jpg, gif.',
            'photo.max' => 'Product photo must not exceed 2MB.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be at least 0.',
            'qty.required' => 'Stock quantity is required.',
            'qty.numeric' => 'Stock quantity must be a valid number.',
            'qty.min' => 'Stock quantity must be at least 0.',
            'formula_id.required' => 'Formula selection is required.',
            'formula_id.exists' => 'Selected formula does not exist.'
        ];
    }
}