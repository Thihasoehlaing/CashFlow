<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'account_id' => ['required', 'exists:accounts,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', Rule::in(['personal', 'business'])],
        ];
    }
}
