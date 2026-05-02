<?php

use App\Livewire\IncomeForm;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('income form updates myr preview when amount changes', function (): void {
    Setting::set('fx_rates', ['USD' => 0.212766]);

    Livewire::test(IncomeForm::class)
        ->set('currency', 'USD')
        ->set('amount', 10)
        ->assertSet('amount', 10)
        ->assertSee('MYR 47.00');
});

test('income form calculates hourly freelance amount', function (): void {
    Livewire::test(IncomeForm::class)
        ->set('source', 'freelance')
        ->set('billingType', 'hourly')
        ->set('hours', 3)
        ->set('ratePerHour', 25)
        ->assertSet('amount', 75.0)
        ->assertSee('MYR 75.00');
});

test('income form changes project field label by source', function (): void {
    Livewire::test(IncomeForm::class)
        ->assertSee('Office / company')
        ->set('source', 'family')
        ->assertSee('From who')
        ->set('source', 'freelance')
        ->assertSee('Project name');
});

test('income myr preview handles a missing hydrated amount property', function (): void {
    $component = new IncomeForm;

    unset($component->amount);

    expect($component->myrPreview())->toBe('MYR 0.00');
});
