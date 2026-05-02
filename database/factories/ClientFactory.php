<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->name(), 'company' => fake()->company(), 'email' => fake()->safeEmail(), 'phone' => fake()->phoneNumber(), 'address' => fake()->address(), 'default_currency' => 'MYR', 'preferred_billing' => 'fixed', 'notes' => null];
    }
}
