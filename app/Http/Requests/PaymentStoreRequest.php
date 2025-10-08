<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust authorization as needed
    }

    public function rules(): array
    {
        return [
            'dt' => ['required','date'],
            'invoice_id' => ['required','exists:invoices,id'],
            'payment_type' => ['required','in:cash,transfer'],
            'amount' => ['required','numeric','min:0'],
            'bank_account_id' => ['nullable','exists:accounts,id'],
            'bank_account_name' => ['nullable','string','max:255'],
            'bank_account_type' => ['nullable','string','max:255'],
        ];
    }

    public function prepareForValidation(): void
    {
        if($this->payment_type === 'cash') {
            $this->merge([
                'bank_account_id' => null,
                'bank_account_name' => null,
                'bank_account_type' => null,
            ]);
        }
    }
}
