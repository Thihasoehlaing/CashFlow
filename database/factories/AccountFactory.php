<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->company().' Account', 'type' => 'bank', 'currency' => 'MYR', 'account_number' => fake()->numerify('####-####'), 'opening_balance' => fake()->randomFloat(2, 0, 5000), 'is_active' => true, 'notes' => null];
    }
}
