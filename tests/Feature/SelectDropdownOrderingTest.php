<?php

use App\Livewire\ExpenseForm;
use App\Models\Account;
use App\Models\Client;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('model backed form dropdowns are ordered by name', function (): void {
    $this->actingAs(User::factory()->create());

    Account::factory()->create(['name' => 'Zulu Wallet', 'currency' => 'MYR']);
    Account::factory()->create(['name' => 'Alpha Bank', 'currency' => 'MYR']);
    Account::factory()->create(['name' => 'Middle Cash', 'currency' => 'MYR']);

    $this->get(route('transfers.create'))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Alpha Bank (MYR)',
            'Middle Cash (MYR)',
            'Zulu Wallet (MYR)',
        ]);
});

test('settings backed livewire dropdowns are ordered alphabetically', function (): void {
    Setting::set('expense_categories', ['Zulu', 'Alpha', 'Middle']);
    Setting::set('fx_rates', ['USD' => 0.212766, 'EUR' => 0.196078, 'MMK' => 880]);

    Livewire::test(ExpenseForm::class)
        ->assertSeeInOrder(['Alpha', 'Middle', 'Zulu'])
        ->assertSeeInOrder(['EUR', 'GBP', 'MMK', 'MYR', 'SGD', 'USD']);
});

test('translated and currency form dropdowns are ordered by their labels', function (): void {
    $this->actingAs(User::factory()->create());

    Setting::set('fx_rates', ['USD' => 0.212766, 'EUR' => 0.196078, 'MMK' => 880]);

    Client::factory()->create(['name' => 'Zulu Client']);
    Client::factory()->create(['name' => 'Alpha Client']);

    $this->get(route('projects.create'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Alpha Client', 'Zulu Client'])
        ->assertSeeInOrder(['Active', 'Cancelled', 'Completed', 'Paused', 'Planned'])
        ->assertSeeInOrder(['Community', 'Free', 'Internal', 'Paid'])
        ->assertSeeInOrder(['EUR', 'GBP', 'MMK', 'MYR', 'SGD', 'USD']);
});
