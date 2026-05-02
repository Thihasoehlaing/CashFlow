<?php

use App\Models\Account;
use App\Models\Expense;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('settings page renders for authenticated user', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get(route('settings.index'))
        ->assertOk()
        ->assertSee('alpinejs@3.x.x', false)
        ->assertSee('x-cloak', false)
        ->assertSee('Business profile')
        ->assertSee('PDF accounts')
        ->assertSee('FX rates')
        ->assertSee('Defaults')
        ->assertSee('1 MYR =', false);
});

test('settings can be updated', function (): void {
    $this->actingAs(User::factory()->create());

    $account = Account::factory()->create([
        'name' => 'Maybank',
        'currency' => 'MYR',
    ]);

    $this->put(route('settings.update'), [
        'business_name' => 'CashFlow Studio',
        'business_email' => 'hello@example.com',
        'business_phone' => '123456',
        'business_address' => 'Kuala Lumpur',
        'business_reg_no' => 'REG-123',
        'bank_details' => [
            'MYR' => (string) $account->id,
        ],
        'fx_rates' => [
            'usd' => '0.212766',
            'SGD' => '0.285714',
        ],
        'default_tax_rate' => '8.5',
        'default_payment_terms' => 'Payment due within 7 days',
        'default_validity_days' => '14',
        'expense_categories' => ['Transport', 'AI Tools'],
    ])->assertRedirect();

    expect(Setting::get('business_name'))->toBe('CashFlow Studio')
        ->and(Setting::get('bank_details'))->toBe(['MYR' => $account->id])
        ->and(Setting::get('fx_rates'))->toBe(['USD' => 0.212766, 'SGD' => 0.285714])
        ->and(Setting::get('expense_categories'))->toBe(['AI Tools', 'Transport']);
});

test('settings expense categories render alphabetically', function (): void {
    $this->actingAs(User::factory()->create());

    Setting::set('expense_categories', ['Transport', 'AI Tools', 'Food & Drinks']);

    $response = $this->get(route('settings.index'))
        ->assertSuccessful();

    expect($response->viewData('settings')['expense_categories'])->toBe([
        'AI Tools',
        'Food & Drinks',
        'Transport',
    ]);
});

test('used expense categories are preserved when categories are cleared', function (): void {
    $this->actingAs(User::factory()->create());
    $account = Account::factory()->create();

    Expense::factory()->create([
        'account_id' => $account->id,
        'category' => 'Infrastructure',
    ]);

    Setting::set('expense_categories', ['Transport', 'Infrastructure']);

    $this->put(route('settings.update'), [
        'business_name' => 'CashFlow',
        'fx_rates' => [],
        'default_tax_rate' => '8',
        'default_validity_days' => '30',
        'expense_categories' => [''],
    ])->assertRedirect();

    expect(Setting::get('expense_categories'))->toBe(['Infrastructure']);
});

test('business logo can be uploaded', function (): void {
    Storage::fake('public');

    $this->actingAs(User::factory()->create());

    $this->post(route('settings.logo'), [
        'logo' => UploadedFile::fake()->image('logo.png'),
    ])->assertRedirect();

    $path = str_replace('storage/', '', Setting::get('business_logo'));

    Storage::disk('public')->assertExists($path);
});
