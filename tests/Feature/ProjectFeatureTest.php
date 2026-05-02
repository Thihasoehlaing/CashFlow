<?php

use App\Models\Account;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create and view a project', function (): void {
    $this->actingAs(User::factory()->create());
    $client = Client::factory()->create(['name' => 'Example Studio']);

    $this->get(route('projects.index'))->assertSuccessful();
    $this->get(route('projects.create'))->assertSuccessful()->assertSee('Add project');

    $this->post(route('projects.store'), [
        'client_id' => $client->id,
        'name' => 'CashFlow Admin',
        'status' => 'active',
        'billing_type' => 'paid',
        'currency' => 'MYR',
        'agreed_amount' => 2500,
        'start_date' => today()->toDateString(),
    ])->assertRedirect();

    $project = Project::query()->firstOrFail();

    $this->get(route('projects.show', $project))
        ->assertSuccessful()
        ->assertSee('CashFlow Admin')
        ->assertSee('Example Studio');
});

test('project costs can auto log a business expense', function (): void {
    $this->actingAs(User::factory()->create());
    $account = Account::factory()->create(['currency' => 'MYR']);
    $project = Project::factory()->create(['currency' => 'MYR']);

    $this->get(route('project-costs.create', ['project_id' => $project->id]))
        ->assertSuccessful()
        ->assertSee('Add cost');

    $this->post(route('project-costs.store'), [
        'project_id' => $project->id,
        'account_id' => $account->id,
        'name' => 'Domain renewal',
        'type' => 'domain',
        'provider' => 'Namecheap',
        'amount' => 65,
        'currency' => 'MYR',
        'billing_cycle' => 'yearly',
        'next_renewal_date' => today()->addYear()->toDateString(),
        'is_billable' => 1,
        'auto_log_expense' => 1,
    ])->assertRedirect(route('projects.show', $project));

    $this->assertDatabaseHas('project_costs', [
        'project_id' => $project->id,
        'name' => 'Domain renewal',
        'amount_in_myr' => 65,
        'is_billable' => 1,
    ]);

    $this->assertDatabaseHas('expenses', [
        'account_id' => $account->id,
        'category' => 'Infrastructure',
        'description' => 'Domain renewal',
        'type' => 'business',
    ]);
});

test('paid invoice income keeps the project link', function (): void {
    $this->actingAs(User::factory()->create());
    $account = Account::factory()->create(['currency' => 'MYR']);
    $project = Project::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $project->client_id,
        'project_id' => $project->id,
        'project_title' => $project->name,
        'status' => 'sent',
        'total' => 900,
    ]);

    $this->post(route('invoices.paid', $invoice), [
        'paid_at' => today()->toDateString(),
        'payment_account_id' => $account->id,
        'log_income' => 1,
    ])->assertRedirect();

    $this->assertDatabaseHas('income', [
        'client_id' => $project->client_id,
        'project_id' => $project->id,
        'source' => 'freelance',
        'amount' => 900,
    ]);
});
