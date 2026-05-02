<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->words(3, true),
            'status' => 'active',
            'billing_type' => 'paid',
            'currency' => 'MYR',
            'agreed_amount' => 1500,
            'start_date' => today(),
            'end_date' => null,
            'live_url' => null,
            'repository_url' => null,
            'admin_url' => null,
            'notes' => null,
        ];
    }
}
