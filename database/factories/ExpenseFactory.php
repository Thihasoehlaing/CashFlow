<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return ['category' => 'Food & Drinks', 'amount' => 50, 'currency' => 'MYR', 'amount_in_myr' => 50, 'account_id' => Account::factory(), 'description' => fake()->sentence(), 'date' => today(), 'type' => 'personal'];
    }
}
