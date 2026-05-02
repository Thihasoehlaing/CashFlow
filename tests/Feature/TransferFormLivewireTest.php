<?php

use App\Livewire\TransferForm;
use App\Models\Account;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('transfer form calculates received amount when sent amount changes', function (): void {
    Setting::set('fx_rates', ['USD' => 0.212766]);

    $from = Account::factory()->create(['currency' => 'MYR']);
    $to = Account::factory()->create(['currency' => 'USD']);

    Livewire::test(TransferForm::class)
        ->set('fromAccountId', $from->id)
        ->set('toAccountId', $to->id)
        ->set('fromAmount', 47)
        ->assertSet('fromCurrency', 'MYR')
        ->assertSet('toCurrency', 'USD')
        ->assertSet('toAmount', 10.0);
});

test('transfer calculation handles a missing hydrated from amount property', function (): void {
    $component = new TransferForm;

    unset($component->fromAmount);

    $component->calculateToAmount();

    expect($component->toAmount)->toBe(0.0);
});
