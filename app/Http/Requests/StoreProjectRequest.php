<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'quotation_id' => ['nullable', 'exists:quotations,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['planned', 'active', 'completed', 'paused', 'cancelled'])],
            'billing_type' => ['required', Rule::in(['paid', 'free', 'community', 'internal'])],
            'currency' => ['required', 'string', 'size:3'],
            'agreed_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'live_url' => ['nullable', 'url', 'max:255'],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'admin_url' => ['nullable', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
