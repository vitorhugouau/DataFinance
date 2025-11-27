<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvestmentRequest extends FormRequest
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
            'account_id' => ['required', 'exists:accounts,id'],
            'type' => ['required', 'string', 'in:stock,crypto,fixed_income,currency,savings,piggy_bank,other'],
            'symbol' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.00000001'],
            'purchase_price' => ['required', 'numeric', 'min:0.01'],
            'purchase_date' => ['required', 'date'],
            'current_price' => ['nullable', 'numeric', 'min:0.01'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'interest_type' => ['nullable', 'string', 'in:monthly,yearly'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
