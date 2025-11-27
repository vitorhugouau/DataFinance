<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFixedExpenseRequest extends FormRequest
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
            'account_id' => ['sometimes', 'nullable', 'exists:accounts,id'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'due_date' => ['sometimes', 'date'],
            'frequency' => ['sometimes', 'string', 'in:weekly,biweekly,monthly,quarterly,yearly,custom'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'auto_debit' => ['sometimes', 'boolean'],
            'active' => ['sometimes', 'boolean'],
            'last_paid_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
