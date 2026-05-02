<?php

use App\Livewire\ExpenseForm;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('expense form updates myr preview when amount changes', function (): void {
    Setting::set('fx_rates', ['USD' => 0.212766]);

    Livewire::test(ExpenseForm::class)
        ->set('currency', 'USD')
        ->set('amount', 10)
        ->assertSet('amount', 10)
        ->assertSee('MYR 47.00');
});
