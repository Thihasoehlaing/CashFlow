<?php

use App\Models\Account;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Setting;
use App\Services\CurrencyService;
use App\Services\InvoiceService;
use App\Services\QuotationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('currency service converts configured currencies to myr', function () {
    Setting::set('fx_rates', ['USD' => 0.212766, 'MMK' => 670]);

    expect(app(CurrencyService::class)->convertToMYR(10, 'USD'))->toBe(47.0)
        ->and(app(CurrencyService::class)->convertToMYR(6700, 'MMK'))->toBe(10.0)
        ->and(app(CurrencyService::class)->exchangeRate('MYR', 'MMK'))->toBe(670.0)
        ->and(app(CurrencyService::class)->exchangeRate('MMK', 'MYR'))->toBe(0.001493);
});

test('quotation totals apply discount and tax', function () {
    $totals = app(QuotationService::class)->calculateTotals([
        ['quantity' => 2, 'unit_price' => 100],
    ], 'percentage', 10, 8);

    expect($totals)->toMatchArray([
        'subtotal' => 200.0,
        'discount_amount' => 20.0,
        'tax_amount' => 14.4,
        'total' => 194.4,
    ]);
});

test('marking an invoice paid can log freelance income', function () {
    Setting::set('fx_rates', []);
    $account = Account::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->for($client)->create(['total' => 500, 'currency' => 'MYR']);

    app(InvoiceService::class)->markAsPaid($invoice, $account->id, today()->toDateString(), true);

    expect($invoice->refresh()->status)->toBe('paid')
        ->and($account->income()->count())->toBe(1);
});
