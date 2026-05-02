<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source' => ['required', Rule::in(['job', 'family', 'freelance'])],
            'client_id' => ['nullable', 'required_if:source,freelance', 'exists:clients,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'billing_type' => ['nullable', 'required_if:source,freelance', Rule::in(['fixed', 'hourly'])],
            'hours' => ['nullable', 'required_if:billing_type,hourly', 'numeric', 'min:0'],
            'rate_per_hour' => ['nullable', 'required_if:billing_type,hourly', 'numeric', 'min:0'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'account_id' => ['required', 'exists:accounts,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'type' => ['required', Rule::in(['personal', 'business'])],
        ];
    }
}
