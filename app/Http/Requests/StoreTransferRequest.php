<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from_account_id' => ['required', 'exists:accounts,id', 'different:to_account_id'],
            'to_account_id' => ['required', 'exists:accounts,id'],
            'from_amount' => ['required', 'numeric', 'min:0.01'],
            'from_currency' => ['required', 'string', 'size:3'],
            'to_amount' => ['required', 'numeric', 'min:0.01'],
            'to_currency' => ['required', 'string', 'size:3'],
            'exchange_rate' => ['required', 'numeric', 'min:0.000001'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'fee_currency' => ['nullable', 'string', 'size:3'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
