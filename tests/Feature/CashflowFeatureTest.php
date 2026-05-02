<?php

use App\Models\Account;
use App\Models\Client;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create account and income', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('accounts.store'), [
        'name' => 'Maybank',
        'type' => 'bank',
        'currency' => 'MYR',
        'opening_balance' => 100,
        'is_active' => 1,
    ])->assertRedirect(route('accounts.index'));

    $account = Account::query()->firstOrFail();

    $this->post(route('income.store'), [
        'source' => 'job',
        'amount' => 2500,
        'currency' => 'MYR',
        'account_id' => $account->id,
        'date' => today()->toDateString(),
        'type' => 'personal',
    ])->assertRedirect(route('income.index'));

    expect($account->refresh()->current_balance)->toBe(2600.0);
});

test('accounts can be filtered by type and currency', function () {
    $this->actingAs(User::factory()->create());

    Account::factory()->create([
        'name' => 'Maybank MYR',
        'type' => 'bank',
        'currency' => 'MYR',
    ]);

    Account::factory()->create([
        'name' => 'Touch N Go',
        'type' => 'ewallet',
        'currency' => 'MYR',
    ]);

    Account::factory()->create([
        'name' => 'Wise USD',
        'type' => 'bank',
        'currency' => 'USD',
    ]);

    $this->get(route('accounts.index', ['type' => 'bank', 'currency' => 'MYR']))
        ->assertSuccessful()
        ->assertSee('Maybank MYR')
        ->assertDontSee('Touch N Go')
        ->assertDontSee('Wise USD');
});

test('income index uses source aware table headings and values', function () {
    $this->actingAs(User::factory()->create());
    $account = Account::factory()->create(['name' => 'Touch N Go']);

    Income::factory()->create([
        'source' => 'family',
        'project_name' => 'Than Than Htay (Sister)',
        'account_id' => $account->id,
        'description' => null,
        'date' => today(),
    ]);

    $this->get(route('income.index'))
        ->assertSuccessful()
        ->assertSee('Client / From')
        ->assertSee('Project')
        ->assertSee('Account')
        ->assertSee('Than Than Htay (Sister)')
        ->assertSee('Touch N Go');
});

test('quotation can be created with line items', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create();

    $this->post(route('quotations.store'), [
        'client_id' => $client->id,
        'project_title' => 'Website build',
        'status' => 'draft',
        'currency' => 'MYR',
        'issue_date' => today()->toDateString(),
        'valid_until' => today()->addDays(30)->toDateString(),
        'discount_type' => null,
        'discount_value' => 0,
        'tax_rate' => 8,
        'items' => [
            ['description' => 'Design', 'item_type' => 'fixed', 'quantity' => 1, 'unit_price' => 1000],
        ],
    ])->assertRedirect();

    $this->assertDatabaseHas('quotations', ['project_title' => 'Website build', 'total' => 1080]);
    $this->assertDatabaseHas('quotation_items', ['description' => 'Design', 'amount' => 1000]);
});

test('accepting a quotation creates one draft invoice', function () {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create();

    $this->post(route('quotations.store'), [
        'client_id' => $client->id,
        'project_title' => 'Website build',
        'status' => 'draft',
        'currency' => 'MYR',
        'issue_date' => today()->toDateString(),
        'valid_until' => today()->addDays(30)->toDateString(),
        'discount_type' => null,
        'discount_value' => 0,
        'tax_rate' => 8,
        'items' => [
            ['description' => 'Design', 'item_type' => 'fixed', 'quantity' => 1, 'unit_price' => 1000],
        ],
    ]);

    $quotation = Quotation::query()->firstOrFail();

    $this->patch(route('quotations.status', $quotation), ['status' => 'accepted'])
        ->assertRedirect(route('invoices.edit', Invoice::query()->firstOrFail()));

    $this->assertDatabaseHas('quotations', ['id' => $quotation->id, 'status' => 'accepted']);
    $this->assertDatabaseHas('invoices', [
        'quotation_id' => $quotation->id,
        'client_id' => $client->id,
        'project_title' => 'Website build',
        'status' => 'draft',
        'total' => 1080,
    ]);
    $this->assertDatabaseHas('invoice_items', ['description' => 'Design', 'amount' => 1000]);

    $this->patch(route('quotations.status', $quotation), ['status' => 'accepted']);

    expect(Invoice::query()->where('quotation_id', $quotation->id)->count())->toBe(1);
});

test('expenses index renders category breakdown with strict mysql grouping', function () {
    $this->actingAs(User::factory()->create());
    $account = Account::factory()->create();

    Expense::factory()->create([
        'account_id' => $account->id,
        'category' => 'Transport',
        'amount_in_myr' => 75,
        'date' => today()->subDay(),
    ]);

    Expense::factory()->create([
        'account_id' => $account->id,
        'category' => 'Food & Drinks',
        'amount_in_myr' => 25,
        'date' => today(),
    ]);

    $this->get(route('expenses.index'))
        ->assertSuccessful()
        ->assertSee('Transport')
        ->assertSee('Food &amp; Drinks', false);
});
