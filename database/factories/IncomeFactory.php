<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    public function definition(): array
    {
        return ['source' => 'job', 'amount' => 1000, 'currency' => 'MYR', 'amount_in_myr' => 1000, 'account_id' => Account::factory(), 'description' => fake()->sentence(), 'date' => today(), 'type' => 'personal'];
    }
}
