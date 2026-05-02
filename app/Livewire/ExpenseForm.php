<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Setting;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ExpenseForm extends Component
{
    public string $category = '';

    public float $amount = 0;

    public string $currency = 'MYR';

    public function mount(?Expense $expense = null): void
    {
        $this->category = old('category', $expense?->category ?? '');
        $this->amount = (float) old('amount', $expense?->amount ?? 0);
        $this->currency = old('currency', $expense?->currency ?? 'MYR');
    }

    public function myrPreview(): string
    {
        $service = app(CurrencyService::class);
        $properties = $this->all();
        $amount = (float) ($properties['amount'] ?? 0);
        $currency = (string) ($properties['currency'] ?? 'MYR');

        return $service->formatAmount($service->convertToMYR($amount, $currency), 'MYR');
    }

    public function render(): View
    {
        return view('livewire.expense-form', [
            'categories' => collect(Setting::get('expense_categories', []))->sort()->values(),
            'currencies' => app(CurrencyService::class)->getAvailableCurrencies(),
        ]);
    }
}
