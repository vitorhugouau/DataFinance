<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'type' => ['required', 'string', 'in:expense,income,transfer'],
            'account_id' => ['required', 'exists:accounts,id'],
            'category_id' => ['nullable', 'required_unless:type,transfer', 'exists:categories,id'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'related_account_id' => ['nullable', 'required_if:type,transfer', 'exists:accounts,id', 'different:account_id'],
        ];
    }
}
