<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransferFactory extends Factory
{
    public function definition(): array
    {
        return ['from_account_id' => Account::factory(), 'to_account_id' => Account::factory(), 'from_amount' => 100, 'from_currency' => 'MYR', 'to_amount' => 100, 'to_currency' => 'MYR', 'exchange_rate' => 1, 'fee' => 0, 'fee_currency' => 'MYR', 'date' => today(), 'notes' => null];
    }
}
