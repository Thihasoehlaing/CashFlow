<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationFactory extends Factory
{
    public function definition(): array
    {
        return ['quotation_number' => 'QUO-'.now()->format('Y').'-'.fake()->unique()->numberBetween(100, 999), 'client_id' => Client::factory(), 'project_title' => fake()->sentence(3), 'status' => 'draft', 'currency' => 'MYR', 'subtotal' => 1000, 'discount_value' => 0, 'discount_amount' => 0, 'tax_rate' => 0, 'tax_amount' => 0, 'total' => 1000, 'issue_date' => today(), 'valid_until' => today()->addDays(30), 'business_snapshot' => ['business_name' => 'CashFlow']];
    }
}
