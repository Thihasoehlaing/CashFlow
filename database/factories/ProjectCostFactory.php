<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectCost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectCost>
 */
class ProjectCostFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => 'Hetzner VPS',
            'type' => 'server',
            'provider' => 'Hetzner',
            'amount' => 5,
            'currency' => 'EUR',
            'amount_in_myr' => 25.5,
            'billing_cycle' => 'monthly',
            'next_renewal_date' => today()->addMonth(),
            'is_billable' => false,
            'auto_log_expense' => false,
            'notes' => null,
        ];
    }
}
