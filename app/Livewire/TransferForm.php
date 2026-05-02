<?php

namespace App\Livewire;

use App\Models\Account;
use App\Services\CurrencyService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class TransferForm extends Component
{
    public ?int $fromAccountId = null;
    public ?int $toAccountId = null;
    public float $fromAmount = 0;
    public float $toAmount = 0;
    public string $fromCurrency = 'MYR';
    public string $toCurrency = 'MYR';
    public float $exchangeRate = 1;


    public function updatedFromAccountId(): void
    {
        $this->syncCurrencies();
    }


    public function updatedToAccountId(): void
    {
        $this->syncCurrencies();
    }


    public function updatedFromAmount(): void
    {
        $this->calculateToAmount();
    }


    public function mount(): void
    {
        $this->fromAccountId = old('from_account_id');
        $this->toAccountId = old('to_account_id');
        $this->fromAmount = (float) old('from_amount', 0);
        $this->toAmount = (float) old('to_amount', 0);
        $this->syncCurrencies();
    }


    public function syncCurrencies(): void
    {
        $properties = $this->all();

        $this->fromCurrency = Account::query()->find($properties['fromAccountId'] ?? null)?->currency ?? 'MYR';
        $this->toCurrency = Account::query()->find($properties['toAccountId'] ?? null)?->currency ?? 'MYR';
        $this->exchangeRate = $this->getExchangeRate();
        $this->calculateToAmount();
    }


    public function calculateToAmount(): void
    {
        $properties = $this->all();
        $fromAmount = (float) ($properties['fromAmount'] ?? 0);
        $exchangeRate = (float) ($properties['exchangeRate'] ?? 1);

        if ($fromAmount > 0) {
            $this->toAmount = round($fromAmount * $exchangeRate, 2);
        }
    }


    public function getExchangeRate(): float
    {
        $properties = $this->all();
        $fromCurrency = (string) ($properties['fromCurrency'] ?? 'MYR');
        $toCurrency = (string) ($properties['toCurrency'] ?? 'MYR');

        return app(CurrencyService::class)->exchangeRate($fromCurrency, $toCurrency);
    }


    public function render(): View
    {
        return view('livewire.transfer-form', ['accounts' => Account::query()->where('is_active', true)->orderBy('name')->get()]);
    }
}
