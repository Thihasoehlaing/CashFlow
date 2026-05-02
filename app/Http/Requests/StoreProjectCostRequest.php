<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'account_id' => ['nullable', 'required_if:auto_log_expense,1', 'exists:accounts,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['domain', 'server', 'email', 'plugin', 'maintenance', 'other'])],
            'provider' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_cycle' => ['required', Rule::in(['one_time', 'monthly', 'yearly'])],
            'next_renewal_date' => ['nullable', 'date'],
            'is_billable' => ['nullable', 'boolean'],
            'auto_log_expense' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
